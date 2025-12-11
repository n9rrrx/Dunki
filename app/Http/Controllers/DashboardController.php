<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ClientProfile;
use App\Models\File;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // ==========================================
    // TRAFFIC COP (Redirects based on Role) 🚦
    // ==========================================
    public function index()
    {
        $user = auth()->user();
        if ($user->user_type === 'student') return redirect()->route('student.dashboard');
        if ($user->user_type === 'admin') return redirect()->route('admin.dashboard');
        if ($user->user_type === 'hr') return redirect()->route('hr.dashboard');
        if ($user->user_type === 'visa_consultant') return redirect()->route('consultant.dashboard');
        if ($user->user_type === 'academic_advisor') return redirect()->route('academic.dashboard');
        if ($user->user_type === 'travel_agent') return redirect()->route('travel.dashboard');
        return abort(403, 'User role not recognized.');
    }

    // ==========================================
    // 🎓 ACADEMIC ADVISOR DASHBOARD (STRICT FILTERING)
    // ==========================================
    public function academic()
    {
        $user = Auth::user();

        // 1. Stats: Only count applications assigned to THIS advisor
        $pendingReview = Application::where('status', 'submitted')
            ->whereHas('clientProfile', function ($q) use ($user) {
                $q->where('advisor_id', $user->id); // ✅ STRICT FILTERING
            })->count();

        // 2. Count all assigned students (regardless of application status)
        $myStudents = ClientProfile::where('advisor_id', $user->id)->count();

        // 3. Recent Applications (The Queue): Only fetch assigned applications
        $applications = Application::whereHas('clientProfile', function ($query) use ($user) {
            $query->where('advisor_id', $user->id); // ✅ STRICT FILTERING
        })
            ->with('clientProfile.user')
            ->where('status', 'submitted')
            ->latest()
            ->take(10)
            ->get();

        // If the Advisor has no submitted apps assigned, the list will be empty, which is correct.

        return view('partials.dashboard-academic', compact('pendingReview', 'myStudents', 'applications'));
    }

    // ==========================================
    // 🎓 STUDENT DASHBOARD
    // ==========================================
    public function student()
    {
        $user = auth()->user();
        $clientProfile = ClientProfile::where('user_id', $user->id)->first();
        $applications = $clientProfile ? Application::where('client_id', $clientProfile->id)->latest()->get() : collect();
        $tasks = Task::where('assigned_to', $user->id)->get();

        // Profile Completion
        $points = 0; $totalPoints = 7;
        if ($user->phone) $points++;
        if ($user->country) $points++;
        if ($user->location) $points++;
        if ($user->profile_pic) $points++;
        $hasPassport = File::where('uploaded_by', $user->id)->where('file_type', 'passport')->exists();
        $hasTranscript = File::where('uploaded_by', $user->id)->where('file_type', 'transcript')->exists();
        $hasPhoto = File::where('uploaded_by', $user->id)->where('file_type', 'photo')->exists();
        if ($hasPassport) $points++;
        if ($hasTranscript) $points++;
        if ($hasPhoto) $points++;
        $profileCompletion = round(($points / $totalPoints) * 100);

        return view('partials.dashboard-student', compact('applications', 'tasks', 'profileCompletion'));
    }

    // ==========================================
    // 🛡️ ADMIN DASHBOARD
    // ==========================================
    public function admin()
    {
        $totalStudents = ClientProfile::count();
        $totalApplications = Application::count();
        $totalVerifiedFiles = File::where('status', 'verified')->count();
        $totalCompletedTasks = Task::where('status', 'completed')->count();
        $totalUsers = User::count();

        // Admin sees EVERYTHING
        $allApplications = Application::with([
            'clientProfile.user', 'clientProfile.advisor', 'clientProfile.visaConsultant', 'clientProfile.travelAgent'
        ])->latest()->paginate(10);

        $advisors = User::where('user_type', 'academic_advisor')->get();
        $visaConsultants = User::where('user_type', 'visa_consultant')->get();
        $travelAgents = User::where('user_type', 'travel_agent')->get();

        return view('partials.dashboard-admin', compact(
            'totalUsers', 'totalStudents', 'totalApplications', 'totalVerifiedFiles', 'totalCompletedTasks', 'allApplications', 'advisors', 'visaConsultants', 'travelAgents'
        ));
    }

    public function hr() { return view('partials.dashboard-hr'); }

    public function consultant()
    {
        // This should be in VisaController@index, but is kept here for the redirect
        $tasks = Task::where('assigned_to', auth()->id())->get();
        return view('partials.dashboard-consultant', compact('tasks'));
    }

    public function travel()
    {
        // This should be in TravelController@index, but is kept here for the redirect
        $tasks = Task::where('assigned_to', auth()->id())->get();
        return view('partials.dashboard-travel', compact('tasks'));
    }
}
