<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Notifications\OtpResetPassword;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    // 1. Kirim OTP ke email
    public function sendOtp(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
    
        $otp = rand(100000, 999999);
        $email = $request->email;
    
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $otp,
                'created_at' => now()
            ]
        );
    
        // Kirim notifikasi ke user
        $user = User::where('email', $email)->first();
        $user->notify(new OtpResetPassword($otp));
    
        return response()->json(['message' => 'OTP berhasil dikirim ke email.']);
    }

    // 2. Verifikasi OTP
    public function verifyOtp(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->otp)
            ->first();

        if (!$record) {
            return response()->json(['error' => 'OTP tidak valid'], 400);
        }

        return response()->json(['message' => 'OTP valid']);
    }

    // 3. Reset Password
    public function resetPassword(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->otp)
            ->first();

        if (!$record) {
            return response()->json(['error' => 'OTP tidak valid'], 400);
        }

        // Reset password
        User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        // Hapus token setelah reset
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password berhasil direset']);
    }
}

