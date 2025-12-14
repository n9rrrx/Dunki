<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\University;
use App\Models\ClientProfile;
use App\Models\Task;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\ApplicationStatusUpdate;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->user_type === 'student') {
            $clientProfile = ClientProfile::where('user_id', $user->id)->first();
            if (!$clientProfile) return redirect()->route('profile.edit');
            $applications = Application::where('client_id', $clientProfile->id)->latest()->paginate(10);
        }
        elseif (in_array($user->user_type, ['academic_advisor', 'admin', 'visa_consultant', 'travel_agent'])) {

            $applicationsQuery = Application::whereHas('clientProfile', function ($query) use ($user) {
                if ($user->user_type === 'academic_advisor') $query->where('advisor_id', $user->id);
                if ($user->user_type === 'visa_consultant') $query->where('visa_consultant_id', $user->id);
                if ($user->user_type === 'travel_agent') $query->where('travel_agent_id', $user->id);
            });

            $applications = $applicationsQuery->with([
                'clientProfile.user',
                'clientProfile.advisor',
                'clientProfile.visaConsultant',
                'clientProfile.travelAgent'
            ])
                ->latest()
                ->paginate(15);

            if ($user->user_type === 'admin') {
                $applications = Application::with([
                    'clientProfile.user',
                    'clientProfile.advisor',
                    'clientProfile.visaConsultant',
                    'clientProfile.travelAgent'
                ])->latest()->paginate(15);
            }
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
        // 2. STAFF (Strict Assignment Check)
        elseif (in_array($user->user_type, ['academic_advisor', 'visa_consultant', 'travel_agent'])) {
            $profile = $application->clientProfile;
            $isAssigned = false;

            if ($user->user_type === 'academic_advisor' && $profile->advisor_id === $user->id) $isAssigned = true;
            if ($user->user_type === 'visa_consultant' && $profile->visa_consultant_id === $user->id) $isAssigned = true;
            if ($user->user_type === 'travel_agent' && $profile->travel_agent_id === $user->id) $isAssigned = true;

            if (!$isAssigned && $user->user_type !== 'admin') return back()->with('error', 'You are not assigned to this student.');

            // Status Logic
            if ($user->user_type === 'academic_advisor' && in_array($application->status, ['approved', 'rejected']))
                return redirect()->route('academic.dashboard')->with('info', "Already processed.");

            if ($user->user_type === 'visa_consultant' && !in_array($application->status, ['approved', 'visa_processing', 'visa_submitted', 'visa_granted', 'visa_rejected']))
                return back()->with('error', 'Not ready for visa processing.');

            if ($user->user_type === 'travel_agent' && !in_array($application->status, ['visa_granted', 'travel_booking', 'travel_booked']))
                return back()->with('error', 'Visa not yet granted.');
        }
        // 3. ADMIN
        elseif ($user->user_type !== 'admin') {
            abort(403);
        }

        return view('students.applications.show', compact('application'));
    }

    public function updateStatus(Request $request, Application $application)
    {
        $user = Auth::user();
        $newStatus = $request->status;

        if (!in_array($user->user_type, ['academic_advisor', 'admin', 'visa_consultant', 'travel_agent'])) abort(403);

        $request->validate(['status' => 'required', 'reason' => 'nullable|max:500']);

        // 🛑 PREFERENCE & FILE CLEARING LOGIC 🛑
        $statusesRequiringClearance = ['submitted', 'approved', 'visa_processing', 'visa_submitted', 'visa_granted'];

        if (in_array($newStatus, $statusesRequiringClearance)) {
            // 1. Clear the travel preference form data
            $application->travel_preferences = null;

            // 2. Clear associated travel documents from the files table
            // ✅ CRITICAL FIX: Use DB Facade to ensure deletion is executed
            DB::table('files')
                ->where('application_id', $application->id)
                ->whereIn('file_type', ['flight_ticket', 'hotel_voucher', 'travel_insurance'])
                ->delete();
        }

        $application->update([
            'status' => $newStatus,
            'notes'  => $request->reason,
        ]);


        // 🛑 TASK CREATION LOGIC
        if ($newStatus === 'visa_granted') {
            $travelAgentId = $application->clientProfile->travel_agent_id;

            if ($travelAgentId) {
                // Check if a PENDING task already exists to prevent duplication on status reset
                $existingTask = Task::where('related_application_id', $application->id)
                    ->where('assigned_to', $travelAgentId)
                    ->where('status', '!=', 'completed')
                    ->first();

                if (!$existingTask) {
                    Task::create([
                        'title' => 'Book Travel Tickets for ' . $application->clientProfile->user->name,
                        'description' => 'The visa has been granted. Coordinate with the student to finalize travel preferences and book flights/accommodation.',
                        'assigned_to' => $travelAgentId,
                        'status' => 'pending',
                        'related_application_id' => $application->id,
                        'assigned_by' => Auth::id(),
                    ]);
                }
            }
        }
        // END TASK CREATION LOGIC 🛑

        try {
            Mail::to($application->clientProfile->user->email)->send(new ApplicationStatusUpdate($application, $newStatus));
        } catch (\Exception $e) {}

        if ($user->user_type === 'academic_advisor') return redirect()->route('academic.dashboard')->with('success', 'Processed!');
        if ($user->user_type === 'visa_consultant') return redirect()->route('consultant.dashboard')->with('success', 'Visa updated!');
        if ($user->user_type === 'travel_agent') return redirect()->route('travel.dashboard')->with('success', 'Travel booked!');

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
            return redirect()->route('files.index')->with('error', "⚠️ Application Blocked! Missing: $list.");
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

        return redirect()->route('student.dashboard')->with('success', 'Application submitted successfully!');
    }

    public function create()
    {
        return view('students.applications.create');
    }

    public function edit(Application $application)
    {
        if ($application->clientProfile->user_id !== auth()->id()) abort(403);
        if (!in_array($application->status, ['rejected', 'visa_rejected'])) return back();
        return view('students.applications.edit', compact('application'));
    }

    public function update(Request $request, Application $application)
    {
        if ($application->clientProfile->user_id !== auth()->id()) abort(403);

        $request->validate(['course_name' => 'required', 'intake' => 'required']);

        $application->update([
            'course_name' => $request->course_name,
            'intake'      => $request->intake,
            'status'      => 'submitted',
            'notes'       => null
        ]);

        return redirect()->route('applications.show', $application->id)
            ->with('success', 'Application resubmitted successfully!');
    }

    public function submitTravelPreferences(Request $request, Application $application)
    {
        $user = Auth::user();

        if ($user->user_type !== 'student') abort(403);
        if ($application->clientProfile->user_id !== $user->id) abort(403);

        $request->validate([
            'travel_date' => 'required|date|after:today',
            'departure_city' => 'required|string|max:255',
            'airline_preference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $data = [
            'date' => $request->travel_date,
            'city' => $request->departure_city,
            'airline' => $request->airline_preference,
            'notes' => $request->notes,
            'submitted_at' => now()->toDateTimeString()
        ];

        $application->travel_preferences = $data;
        $application->save();

        return back()->with('success', 'Travel preferences sent to agent! They will contact you shortly.');
    }
}
