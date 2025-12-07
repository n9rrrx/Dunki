<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TravelController extends Controller
{
    public function index()
    {
        // 1. Fetch Students Ready for Travel (Visa Granted)
        $readyForTravel = Application::with('clientProfile.user')
            ->where('status', 'visa_granted')
            ->latest()
            ->get();

        // 2. Fetch Students currently being booked
        $bookingInProgress = Application::with('clientProfile.user')
            ->whereIn('status', ['travel_booking', 'travel_booked'])
            ->latest()
            ->get();

        return view('partials.dashboard-travel', compact('readyForTravel', 'bookingInProgress'));
    }

    /**
     * Start the Booking Process
     */
    public function startBooking(Request $request, Application $application)
    {
        // Update status to show travel work has started
        $application->update([
            'status' => 'travel_booking',
            'notes' => 'Travel booking started by ' . Auth::user()->name
        ]);

        return back()->with('success', 'Travel file opened for ' . $application->clientProfile->user->name);
    }

    /**
     * Upload Tickets and Confirm Booking
     */
    public function uploadTickets(Request $request, Application $application)
    {
        $request->validate([
            'flight_ticket' => 'required|file|mimes:pdf,jpg,png|max:5120',
            'hotel_voucher' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'notes'         => 'nullable|string'
        ]);

        $user = Auth::user();

        // 1. Upload Flight Ticket
        if ($request->hasFile('flight_ticket')) {
            $file = $request->file('flight_ticket');
            $path = $file->store('travel_docs', 'public');

            \App\Models\File::create([
                'profile_id'    => $application->clientProfile->id, // Link to student profile
                'application_id'=> $application->id,
                'file_type'     => 'flight_ticket',
                'file_name'     => 'Flight_Ticket_' . $application->application_number,
                'original_name' => $file->getClientOriginalName(),
                'file_path'     => $path,
                'file_size'     => $file->getSize(),
                'mime_type'     => $file->getMimeType(),
                'uploaded_by'   => $user->id,
                'status'        => 'verified' // Auto-verify since Agent uploaded it
            ]);
        }

        // 2. Upload Hotel Voucher (Optional)
        if ($request->hasFile('hotel_voucher')) {
            $file = $request->file('hotel_voucher');
            $path = $file->store('travel_docs', 'public');

            \App\Models\File::create([
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

        // 3. Update Status
        $application->update([
            'status' => 'travel_booked',
            'notes'  => $request->notes ?? 'Travel documents uploaded.',
        ]);

        return redirect()->route('travel.dashboard')->with('success', 'Tickets uploaded and booking confirmed!');
    }
}
