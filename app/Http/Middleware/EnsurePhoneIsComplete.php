<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'customer') {
            if (empty(Auth::user()->phone)) {
                // Allow them to visit the profile edit and update pages, otherwise they'd be in an infinite redirect loop
                if (!$request->routeIs('profile.*')) {
                    return redirect()->route('profile.edit')->with('warning', 'Silakan lengkapi nomor WhatsApp Anda agar kami dapat mengirimkan update status dan struk pesanan.');
                }
            }
        }

        return $next($request);
    }
}
