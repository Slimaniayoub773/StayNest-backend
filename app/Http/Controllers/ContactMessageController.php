<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactResponseMail;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of contact messages.
     */
    public function index()
    {
        $messages = ContactMessage::with(['user', 'guest'])
            ->latest()
            ->paginate(10);
            
        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    /**
     * Store a new contact message.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $message = ContactMessage::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Your contact message has been submitted successfully.',
            'data'    => $message,
        ], 201);
    }

    /**
     * Display a specific message.
     */
    public function show($id)
    {
        $message = ContactMessage::with(['user', 'guest'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $message
        ]);
    }

    /**
     * Update message status and send response email.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,resolved',
            'response_message' => 'nullable|string'
        ]);

        $message = ContactMessage::findOrFail($id);
        $message->status = $request->status;
        
        if ($request->has('admin_notes')) {
            $message->admin_notes = $request->admin_notes;
        }
        
        $message->save();

        // Send response email if provided
        if ($request->response_message && $request->status === 'resolved') {
            Mail::to($message->email)->send(new ContactResponseMail($message, $request->response_message));
        }

        return response()->json([
            'success' => true,
            'message' => 'Contact message status updated.',
            'data'    => $message,
        ]);
    }

    /**
     * Send response email.
     */
    public function sendResponse(Request $request, $id)
    {
        $request->validate([
            'response_subject' => 'required|string|max:255',
            'response_message' => 'required|string'
        ]);

        $message = ContactMessage::findOrFail($id);

        // Send email
        Mail::to($message->email)->send(new ContactResponseMail(
            $message, 
            $request->response_message,
            $request->response_subject
        ));

        // Update message status to in_progress if it was pending
        if ($message->status === 'pending') {
            $message->status = 'in_progress';
            $message->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Response email sent successfully.'
        ]);
    }

    /**
     * Remove a message.
     */
    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact message deleted successfully.',
        ]);
    }

    /**
     * Get statistics for dashboard.
     */
    public function statistics()
    {
        $total = ContactMessage::count();
        $pending = ContactMessage::where('status', 'pending')->count();
        $inProgress = ContactMessage::where('status', 'in_progress')->count();
        $resolved = ContactMessage::where('status', 'resolved')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'pending' => $pending,
                'in_progress' => $inProgress,
                'resolved' => $resolved
            ]
        ]);
    }
}