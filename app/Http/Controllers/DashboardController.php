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

        // 1. Student -> Redirect to Student Dashboard
        if ($user->user_type === 'student') {
            return redirect()->route('student.dashboard');
        }

        // 2. Admin -> Redirect to Admin Dashboard
        if ($user->user_type === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // 3. HR -> Redirect to HR Dashboard
        if ($user->user_type === 'hr') {
            return redirect()->route('hr.dashboard');
        }

        // 4. Consultants -> Redirect to Consultant Dashboard
        if ($user->user_type === 'visa_consultant') {
            return redirect()->route('consultant.dashboard');
        }

        if ($user->user_type === 'academic_advisor') {
            return redirect()->route('academic.dashboard');
        }

        if ($user->user_type === 'travel_agent') {
            return redirect()->route('travel.dashboard');
        }

        // 5. Fallback for undefined roles
        return abort(403, 'User role not recognized.');
    }

    // ==========================================
    // 🎓 ACADEMIC ADVISOR DASHBOARD
    // ==========================================
    public function academic()
    {
        $user = Auth::user();

        // 1. Stats
        // Count applications that are "Submitted" (Waiting for review)
        $pendingReview = Application::where('status', 'submitted')->count();
        $myStudents = 12; // Placeholder for now

        // 2. Recent Applications (The Queue)
        // Fetch applications that need attention
        $applications = Application::with('clientProfile.user')
        ->where('status', 'submitted')
            ->latest()
            ->take(10)
            ->get();

        return view('partials.dashboard-academic', compact('pendingReview', 'myStudents', 'applications'));
    }

    // ==========================================
    // 🎓 STUDENT DASHBOARD
    // ==========================================
    public function student()
    {
        $user = auth()->user();
        $clientProfile = \App\Models\ClientProfile::where('user_id', $user->id)->first();

        // Applications & Tasks logic stays the same...
        $applications = $clientProfile
            ? \App\Models\Application::where('client_id', $clientProfile->id)->latest()->get()
            : collect();
        $tasks = \App\Models\Task::where('assigned_to', $user->id)->get();

        // ==================================================
        // 4. Calculate Profile Completion % (STRICT MODE)
        // ==================================================
        $points = 0;
        $totalPoints = 7; // We increased the requirements

        // 1. Basic Info (From Registration) - Worth 1 point each
        if ($user->phone) $points++;
        if ($user->country) $points++;
        if ($user->location) $points++;
        if ($user->profile_pic) $points++;

        // 2. Critical Documents (The "Real Work")
        // These will force the percentage down if they haven't uploaded them yet
        $hasPassport = \App\Models\File::where('uploaded_by', $user->id)
            ->where('file_type', 'passport')
            ->exists();

        $hasTranscript = \App\Models\File::where('uploaded_by', $user->id)
            ->where('file_type', 'transcript')
            ->exists();

        $hasPhoto = \App\Models\File::where('uploaded_by', $user->id)
            ->where('file_type', 'photo')
            ->exists();

        if ($hasPassport) $points++;
        if ($hasTranscript) $points++;
        if ($hasPhoto) $points++;

        $profileCompletion = round(($points / $totalPoints) * 100);
        // ==================================================

        return view('partials.dashboard-student', compact('applications', 'tasks', 'profileCompletion'));
    }

    // ==========================================
    // 🛡️ ADMIN DASHBOARD (Stats Logic Moved Here)
    // ==========================================
    public function admin()
    {
        // 1. General Counts
        $totalStudents = ClientProfile::count(); // Use ClientProfile for students, or User::where('user_type','student')->count()
        $totalApplications = Application::count();
        $totalVerifiedFiles = File::where('status', 'verified')->count();
        $totalCompletedTasks = Task::where('status', 'completed')->count();
        $totalUsers = User::count(); // Total system users

        // 2. Trends (Last 6 Months)
        $applicationTrends = Application::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')->orderBy('month')->pluck('total', 'month')->toArray();

        $taskTrends = Task::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')->orderBy('month')->pluck('total', 'month')->toArray();

        // 3. Approval Rate
        $totalFiles = File::count();
        $approvalRate = $totalFiles > 0 ? round(($totalVerifiedFiles / $totalFiles) * 100, 1) : 0;

        // 4. Financials
        $totalFees = 0;
        $goalAmount = 20000;
        if (Schema::hasColumn('applications', 'fee')) {
            $totalFees = Application::sum('fee');
        }
        $progressPercent = $goalAmount > 0 ? round(($totalFees / $goalAmount) * 100, 1) : 0;

        // 5. Recent Activity
        $recentApplications = Application::latest()->take(5)->get();

        // 6. Top Destinations
        $topDestinations = collect();
        if (Schema::hasColumn('applications', 'destination')) {
            $topDestinations = Application::select('destination', DB::raw('COUNT(*) as total'))
                ->groupBy('destination')
                ->orderByDesc('total')
                ->take(8)
                ->get();
        }

        // Return the dedicated Admin View
        // Note: Make sure 'partials.dashboard-admin' extends 'layouts.app' just like student view
        return view('partials.dashboard-admin', compact(
            'totalUsers',
            'totalStudents',
            'totalApplications',
            'totalVerifiedFiles',
            'totalCompletedTasks',
            'applicationTrends',
            'taskTrends',
            'approvalRate',
            'totalFees',
            'goalAmount',
            'progressPercent',
            'recentApplications',
            'topDestinations'
        ));
    }

    // ==========================================
    // 👔 HR DASHBOARD
    // ==========================================
    public function hr()
    {
        // Add specific HR logic here later
        return view('partials.dashboard-hr');
    }

    // ==========================================
    // ✈️ CONSULTANT DASHBOARD
    // ==========================================
    public function consultant()
    {
        $tasks = Task::where('assigned_to', auth()->id())->get();
        // Ensure you have this view file created
        return view('partials.dashboard-consultant', compact('tasks'));
    }
}
