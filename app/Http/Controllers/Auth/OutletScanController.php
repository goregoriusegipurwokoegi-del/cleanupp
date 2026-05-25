<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OutletScanController extends Controller
{
    /**
     * Handle the QR scan from the outlet.
     * URL example: /outlet/scan?phone=08123456789
     */
    public function handleScan(Request $request)
    {
        $phone = $request->query('phone');

        if (!$phone) {
            return redirect()->route('login')->with('info', 'Silakan masuk atau daftar untuk memesan layanan.');
        }

        // Try to find user by phone
        $user = User::where('phone', $phone)->first();

        if ($user) {
            // User exists, log them in
            Auth::login($user);
            return redirect()->route('services.index')->with('success', 'Selamat datang kembali! Silakan pilih layanan kami.');
        }

        // User doesn't exist, create a temporary/guest account or redirect to register with phone prefilled
        return redirect()->route('register', ['phone' => $phone])->with('info', 'Selamat datang di outlet! Silakan lengkapi pendaftaran Anda.');
    }
}
