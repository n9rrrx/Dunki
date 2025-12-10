<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ClientProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    /**
     * Display a listing of all staff members (Advisors, Consultants, Agents).
     */
    public function index()
    {
        // Fetch users who have staff roles
        $staff = User::whereIn('user_type', ['academic_advisor', 'visa_consultant', 'travel_agent', 'hr'])
            ->latest()
            ->paginate(10);

        return view('admin.staff.index', compact('staff'));
    }

    /**
     * Show the form for creating a new staff member.
     */
    public function create()
    {
        return view('admin.staff.create');
    }

    /**
     * Store a newly created staff member in database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|in:academic_advisor,visa_consultant,travel_agent,hr',
            'phone' => 'nullable|string',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->role,
            'phone' => $request->phone,
            'is_active' => true,
            'country' => 'Headquarters', // Default for internal staff
            'location' => 'Main Office',
        ]);

        return redirect()->route('staff.index')->with('success', 'New staff member added successfully!');
    }

    /**
     * Remove the specified staff member from database.
     */
    public function destroy(User $staff)
    {
        // Security: Prevent deleting yourself or students via this controller
        if ($staff->id === auth()->id() || $staff->user_type === 'student') {
            return back()->with('error', 'Action not allowed.');
        }

        $staff->delete();
        return back()->with('success', 'Staff member removed.');
    }

    /**
     * Assign specific staff members to a student profile.
     * This is called from the Admin Dashboard modal.
     */
    public function assignStaff(Request $request, ClientProfile $clientProfile) // 👈 Changed variable name
    {
        $request->validate([
            'advisor_id' => 'nullable|exists:users,id',
            'visa_consultant_id' => 'nullable|exists:users,id',
            'travel_agent_id' => 'nullable|exists:users,id',
        ]);

        $clientProfile->update([ // 👈 Updated usage
            'advisor_id' => $request->advisor_id,
            'visa_consultant_id' => $request->visa_consultant_id,
            'travel_agent_id' => $request->travel_agent_id,
        ]);

        return back()->with('success', 'Staff assigned successfully!');
    }
}
