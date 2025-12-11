<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use App\Models\ClientProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $chatPartner = null;

        // 1. MARK MESSAGES AS READ
        Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // ==========================================
        // 2. ASSIGNMENT LOGIC (Student Side - ADVISOR ONLY)
        // ==========================================

        if ($user->user_type == 'student') {

            // ✅ DEBUG FIX: Perform a direct query to bypass relationship caching/corruption
            $profile = ClientProfile::where('user_id', $user->id)->first();

            if ($profile && $profile->advisor_id) {
                // Priority 1: Academic Advisor (Primary Contact)
                $potentialPartner = User::find($profile->advisor_id);

                if ($potentialPartner) {
                    $chatPartner = $potentialPartner;
                }
            }
            // If assignment fails, $chatPartner remains null, triggering the "Waiting" message.
        }

        // ==========================================
        // 3. ASSIGNMENT LOGIC (Staff/Admin Side)
        // ==========================================
        elseif (in_array($user->user_type, ['academic_advisor', 'visa_consultant', 'travel_agent'])) {
            // Staff logic (resume last chat or find assigned student)
            if ($request->has('student_id')) {
                $chatPartner = User::find($request->student_id);
            } else {
                $lastMessage = Message::where('receiver_id', $user->id)->orWhere('sender_id', $user->id)->latest()->first();
                if ($lastMessage) {
                    $chatPartner = $lastMessage->sender_id == $user->id ? $lastMessage->receiver : $lastMessage->sender;
                } else {
                    $query = ClientProfile::query();
                    if ($user->user_type === 'academic_advisor') $query->where('advisor_id', $user->id);
                    if ($user->user_type === 'visa_consultant') $query->where('visa_consultant_id', $user->id);
                    if ($user->user_type === 'travel_agent') $query->where('travel_agent_id', $user->id);

                    $firstProfile = $query->first();
                    $chatPartner = $firstProfile ? $firstProfile->user : null;
                }
            }
        }

        // Admin Logic
        elseif ($user->user_type == 'admin') {
            $chatPartner = User::where('user_type', 'student')->latest()->first();
        }

        // 4. Fetch Messages (Only if partner exists)
        $messages = [];
        if ($chatPartner) {
            $messages = Message::where(function($q) use ($user, $chatPartner) {
                $q->where('sender_id', $user->id)->where('receiver_id', $chatPartner->id);
            })->orWhere(function($q) use ($user, $chatPartner) {
                $q->where('sender_id', $chatPartner->id)->where('receiver_id', $user->id);
            })->orderBy('created_at', 'asc')->get();
        }

        // Return View
        return view('students.chat.index', [
            'messages' => $messages,
            'chatPartner' => $chatPartner
        ]);
    }

    public function fetch(Request $request)
    {
        $user = Auth::user();
        $receiverId = $request->receiver_id;

        $messages = Message::where(function($q) use ($user, $receiverId) {
            $q->where('sender_id', $user->id)->where('receiver_id', $receiverId);
        })
            ->orWhere(function($q) use ($user, $receiverId) {
                $q->where('sender_id', $receiverId)->where('receiver_id', $user->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages,
            'current_user_id' => $user->id
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'receiver_id' => 'required|exists:users,id'
        ]);

        $msg = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        MessageSent::dispatch($msg);

        if ($request->wantsJson()) {
            return response()->json($msg);
        }

        return back();
    }
}
