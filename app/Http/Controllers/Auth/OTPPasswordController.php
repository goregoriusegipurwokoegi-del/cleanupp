<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOTPMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OTPPasswordController extends Controller
{
    public function showRequestForm()
    {
        return view('auth.forgot-password-otp');
    }

    public function sendOTP(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $otp = rand(100000, 999999);
        
        DB::table('password_reset_otps')->updateOrInsert(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        Mail::to($request->email)->send(new PasswordResetOTPMail($otp));

        return redirect()->route('password.otp.verify', ['email' => $request->email])
            ->with('status', 'Kode OTP telah dikirim ke email Anda.');
    }

    public function showVerifyForm(Request $request)
    {
        return view('auth.verify-otp', ['email' => $request->email]);
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric'
        ]);

        $record = DB::table('password_reset_otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'Kode OTP salah atau sudah kedaluwarsa.']);
        }

        return view('auth.reset-password-otp', ['email' => $request->email, 'otp' => $request->otp]);
    }

    public function resetPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        $minPasswordLength = ($user && $user->role === 'admin') ? 12 : 8;

        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
            'password' => 'required|min:' . $minPasswordLength . '|confirmed',
        ]);

        $record = DB::table('password_reset_otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return redirect()->route('password.request')->withErrors(['error' => 'Sesi reset password tidak valid.']);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_otps')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Kata sandi berhasil diubah. Silakan masuk.');
    }
}
