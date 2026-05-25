<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
            $driver = Socialite::driver('google');
            $googleUser = $driver->stateless()->user();
            
            // Check if user already exists with this Google ID
            $user = User::where('google_id', $googleUser->getId())->first();

            if ($user) {
                // User exists, just log them in
                Auth::login($user);
                return redirect($this->roleRedirect($user));
            }

            // Check if user exists with this email but no Google ID linked yet
            $existingUser = User::where('email', $googleUser->getEmail())->first();

            if ($existingUser) {
                // Link the Google ID to the existing account
                $existingUser->update([
                    'google_id' => $googleUser->getId()
                ]);
                Auth::login($existingUser);
            } else {
                // Create a new user
                $newUser = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt(Str::random(16)),
                    'role' => 'customer' // Default role for new users
                ]);
                Auth::login($newUser);
            }

            return redirect($this->roleRedirect(Auth::user()));

        } catch (\Exception $e) {
            \Log::error('Google Auth Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return redirect()->route('login')->withErrors(['error' => 'Gagal masuk dengan Google. Silakan coba lagi.']);
        }
    }

    /**
     * Get redirect URL based on user role.
     */
    private function roleRedirect($user): string
    {
        return match($user->role) {
            'admin'    => route('admin.dashboard'),
            'employee' => route('employee.dashboard'),
            default    => route('customer.dashboard'),
        };
    }
}
