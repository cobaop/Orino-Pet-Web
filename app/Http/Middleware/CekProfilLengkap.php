<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CekProfilLengkap
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->hasRole('customer')) {
            if (empty($user->no_whatsapp) || empty($user->alamat)) {
                // Izinkan hanya halaman profil
                if (
                    $request->routeIs('profile.edit') ||
                    $request->routeIs('profile.update')
                ) {
                    return $next($request);
                }

                return redirect()->route('profile.edit')->withErrors([
                    'no_whatsapp' => 'Lengkapi No WhatsApp terlebih dahulu.',
                    'alamat' => 'Lengkapi Alamat terlebih dahulu.',
                ]);
            }
        }

        return $next($request);
    }
}

