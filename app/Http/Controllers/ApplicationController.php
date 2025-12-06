<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\University;
use App\Models\ClientProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. STUDENT
        if ($user->user_type === 'student') {
            $clientProfile = ClientProfile::where('user_id', $user->id)->first();
            if (!$clientProfile) return redirect()->route('profile.edit');
            $applications = Application::where('client_id', $clientProfile->id)->latest()->paginate(10);
        }
        // 2. STAFF (Advisor, Admin, Visa, Travel)
        elseif (in_array($user->user_type, ['academic_advisor', 'admin', 'visa_consultant', 'travel_agent'])) {
            $applications = Application::with('clientProfile.user')->latest()->paginate(15);
        }
        else {
            abort(403, 'Access denied.');
        }

        return view('students.applications.index', compact('applications'));
    }

    public function show(Application $application)
    {
        $user = Auth::user();

        // 1. STUDENT
        if ($user->user_type === 'student') {
            if ($application->clientProfile->user_id !== $user->id) abort(403);
        }

        // 2. ADVISOR
        elseif ($user->user_type === 'academic_advisor') {
            if (in_array($application->status, ['approved', 'rejected'])) {
                return redirect()->route('academic.dashboard')->with('info', "Already processed.");
            }
        }

        // 3. VISA CONSULTANT
        elseif ($user->user_type === 'visa_consultant') {
            if (in_array($application->status, ['draft', 'submitted', 'under_review', 'rejected'])) {
                return back()->with('error', 'Not ready for visa processing.');
            }
        }

        // 4. ✅ TRAVEL AGENT
        elseif ($user->user_type === 'travel_agent') {
            // Only allow if Visa is Granted or Booking is in progress
            if (!in_array($application->status, ['visa_granted', 'travel_booking', 'travel_booked'])) {
                return back()->with('error', 'Visa not yet granted. Cannot book travel.');
            }
        }

        // 5. ADMIN
        elseif ($user->user_type !== 'admin') {
            abort(403);
        }

        return view('students.applications.show', compact('application'));
    }

    public function updateStatus(Request $request, Application $application)
    {
        $user = Auth::user();

        // ✅ Allow Travel Agent
        if (!in_array($user->user_type, ['academic_advisor', 'admin', 'visa_consultant', 'travel_agent'])) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|string',
            'reason' => 'nullable|string|max:500',
        ]);

        $application->update([
            'status' => $request->status,
            'notes'  => $request->reason,
        ]);

        // Redirects
        if ($user->user_type === 'academic_advisor') {
            return redirect()->route('academic.dashboard')->with('success', 'Processed!');
        } elseif ($user->user_type === 'visa_consultant') {
            return redirect()->route('consultant.dashboard')->with('success', 'Visa updated!');
        } elseif ($user->user_type === 'travel_agent') {
            return redirect()->route('travel.dashboard')->with('success', 'Travel booked!');
        }

        return back()->with('success', 'Status updated.');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $clientProfile = ClientProfile::where('user_id', $user->id)->first();

        if (!$clientProfile) {
            return redirect()->route('profile.edit')->with('error', 'Complete profile first.');
        }

        // Strict Document Check
        $uploadedFiles = \App\Models\File::where('uploaded_by', $user->id)
            ->where('status', '!=', 'rejected')
            ->pluck('file_type')->toArray();

        $missing = array_diff(['passport', 'transcript', 'photo'], $uploadedFiles);

        if (!empty($missing)) {
            $list = implode(', ', array_map('ucfirst', $missing));
            return redirect()->route('files.index')
                ->with('error', "⚠️ Application Blocked! Missing: $list.");
        }

        $request->validate([
            'university_id' => 'required|exists:universities,id',
            'course_name'   => 'required|string|max:255',
            'intake'        => 'required|string'
        ]);

        $university = University::findOrFail($request->university_id);

        if (Application::where('client_id', $clientProfile->id)
            ->where('university_name', $university->name)
            ->whereIn('status', ['draft', 'submitted', 'under_review'])->exists()) {
            return back()->with('error', 'Already applied to ' . $university->name);
        }

        Application::create([
            'application_number' => 'APP-' . strtoupper(Str::random(8)),
            'client_id'          => $clientProfile->id,
            'university_name'    => $university->name,
            'destination_country'=> $university->country,
            'course_name'        => $request->course_name,
            'intake'             => $request->intake,
            'type'               => 'university_application',
            'status'             => 'submitted',
            'submission_date'    => now(),
        ]);

        return redirect()->route('student.dashboard')
            ->with('success', 'Application submitted successfully!');
    }

    public function create()
    {
        return view('students.applications.create');
    }

    public function edit(Application $application)
    {
        if ($application->clientProfile->user_id !== auth()->id()) abort(403);

        // Allow editing if rejected at EITHER stage (Academic or Visa)
        if (!in_array($application->status, ['rejected', 'visa_rejected'])) {
            return back();
        }

        return view('students.applications.edit', compact('application'));
    }

    public function update(Request $request, Application $application)
    {
        if ($application->clientProfile->user_id !== auth()->id()) abort(403);

        $request->validate([
            'course_name' => 'required|string',
            'intake' => 'required|string',
        ]);

        $application->update([
            'course_name' => $request->course_name,
            'intake'      => $request->intake,
            'status'      => 'submitted', // Reset to start
            'notes'       => null
        ]);

        return redirect()->route('applications.show', $application->id)
            ->with('success', 'Application resubmitted successfully!');
    }
}
