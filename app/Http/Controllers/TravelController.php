<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\File;
use App\Models\Task; // 🛑 Import Task Model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationStatusUpdate;

class TravelController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Ready for Travel: Visa Granted AND Assigned to Me
        $readyForTravel = Application::with('clientProfile.user')
            ->where('status', 'visa_granted')
            ->whereHas('clientProfile', function ($q) use ($user) {
                $q->where('travel_agent_id', $user->id);
            })
            ->latest()
            ->get();

        // 2. In Progress: Assigned to Me
        $bookingInProgress = Application::with('clientProfile.user')
            ->whereIn('status', ['travel_booking', 'travel_booked'])
            ->whereHas('clientProfile', function ($q) use ($user) {
                $q->where('travel_agent_id', $user->id);
            })
            ->latest()
            ->get();

        return view('partials.dashboard-travel', compact('readyForTravel', 'bookingInProgress'));
    }

    public function startBooking(Request $request, Application $application)
    {
        // Strict Assignment Check
        if ($application->clientProfile->travel_agent_id !== Auth::id()) {
            abort(403, 'You are not assigned to this travel case.');
        }

        $application->update([
            'status' => 'travel_booking',
            'notes' => 'Travel booking started by ' . Auth::user()->name
        ]);

        return back()->with('success', 'Booking started.');
    }

    public function uploadTickets(Request $request, Application $application)
    {
        // Strict Assignment Check
        if ($application->clientProfile->travel_agent_id !== Auth::id()) {
            abort(403, 'You are not assigned to this travel case.');
        }

        $request->validate([
            'flight_ticket' => 'required|file|mimes:pdf,jpg,png|max:5120',
            'hotel_voucher' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'notes'         => 'nullable|string'
        ]);

        $user = Auth::user();

        // 1. Upload Files (Flight & Hotel)
        // 1. Upload Flight Ticket
        if ($request->hasFile('flight_ticket')) {
            $file = $request->file('flight_ticket');
            $path = $file->store('travel_docs', 'public');

            File::create([
                'profile_id'    => $application->clientProfile->id,
                'application_id'=> $application->id,
                'file_type'     => 'flight_ticket',
                'file_name'     => 'Flight_Ticket_' . $application->application_number,
                'original_name' => $file->getClientOriginalName(),
                'file_path'     => $path,
                'file_size'     => $file->getSize(),
                'mime_type'     => $file->getMimeType(),
                'uploaded_by'   => $user->id,
                'status'        => 'verified'
            ]);
        }

        // 2. Upload Hotel Voucher
        if ($request->hasFile('hotel_voucher')) {
            $file = $request->file('hotel_voucher');
            $path = $file->store('travel_docs', 'public');

            File::create([
                'profile_id'    => $application->clientProfile->id,
                'application_id'=> $application->id,
                'file_type'     => 'hotel_voucher',
                'file_name'     => 'Hotel_' . $application->application_number,
                'original_name' => $file->getClientOriginalName(),
                'file_path'     => $path,
                'file_size'     => $file->getSize(),
                'mime_type'     => $file->getMimeType(),
                'uploaded_by'   => $user->id,
                'status'        => 'verified'
            ]);
        }


        // 3. ✅ FIX: MARK RELATED TASK AS COMPLETED
        Task::where('related_application_id', $application->id)
            ->where('assigned_to', $user->id)
            ->where('status', '!=', 'completed')
            ->update(['status' => 'completed']);

        // 4. Update Application Status
        $application->update([
            'status' => 'travel_booked',
            'notes'  => $request->notes ?? 'Travel documents uploaded.',
        ]);

        // 5. Send Email Notification
        try {
            $student = $application->clientProfile->user;
            Mail::to($student->email)->send(new ApplicationStatusUpdate($application, 'travel_booked'));
        } catch (\Exception $e) {
            // Log error but don't break flow
            \Illuminate\Support\Facades\Log::error('Travel Mail Error: ' . $e->getMessage());
        }

        return redirect()->route('travel.dashboard')->with('success', 'Tickets uploaded and Student Notified!');
    }
}
