<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClientProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * ADMIN: List all students & Prepare Data for Assignment Modal
     */
    public function index(Request $request)
    {
        // 1. Security Check
        if (Auth::user()->user_type !== 'admin') {
            abort(403, 'Access Denied');
        }

        // 2. Fetch Students (with their profile details)
        $query = User::where('user_type', 'student')->with('clientProfile');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }

        $students = $query->latest()->paginate(10);

        // 3. Fetch Staff for the Assignment Dropdowns
        $advisors = User::where('user_type', 'academic_advisor')->get();
        $visaConsultants = User::where('user_type', 'visa_consultant')->get();
        $travelAgents = User::where('user_type', 'travel_agent')->get();

        return view('students.index', compact('students', 'advisors', 'visaConsultants', 'travelAgents'));
    }

    /**
     * ADMIN: Store a manually created student
     */
    public function store(Request $request)
    {
        if (Auth::user()->user_type !== 'admin') abort(403);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'student',
            'is_active' => $request->is_active ?? true,
            'phone' => $request->phone,
            'country' => $request->country,
            'location' => $request->location,
        ]);

        // Auto-create profile so they can be assigned immediately
        ClientProfile::create(['user_id' => $user->id]);

        return redirect()->route('students.index')->with('success', 'Student created & ready for assignment!');
    }

    // ... (Keep your existing show, edit, update methods below) ...
    public function show() { return view('students.show', ['user' => Auth::user()]); }
    public function edit() { return view('students.edit', ['user' => Auth::user()]); }

    public function update(Request $request) {
        $user = Auth::user();
        $request->validate(['name' => 'required', 'email' => 'required|email']);

        $user->update($request->only('name', 'email', 'phone', 'country', 'location'));

        if ($request->hasFile('profile_pic')) {
            $path = $request->file('profile_pic')->store('avatars', 'public');
            $user->update(['profile_pic' => $path]);
        }

        // Ensure ClientProfile exists
        ClientProfile::firstOrCreate(['user_id' => $user->id]);

        return redirect()->route('profile.show')->with('success', 'Profile updated!');
    }

    public function destroy($id) {
        if (Auth::user()->user_type !== 'admin') abort(403);
        User::destroy($id);
        return back()->with('success', 'Student deleted.');
    }
}
