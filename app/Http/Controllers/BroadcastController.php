<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\MailBroadcast;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class BroadcastController extends Controller
{
    public function sendBroadcast(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $subject = $request->subject;
        $messageContent = $request->message;

        // Ambil semua email user dari database
        $users = User::pluck('email')->toArray();

        foreach ($users as $email) {
            Mail::to($email)->send(new MailBroadcast($subject, $messageContent));
        }

        return response()->json(['message' => 'Broadcast email berhasil dikirim.']);
    }
}
