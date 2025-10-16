<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FilamentLogoutRedirect
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Cek apakah user telah logout dan sedang mengakses logout dari panel tertentu
        if (!Auth::check()) {
            if ($request->is('admin/logout') || $request->is('owner/logout') || $request->is('logout')) {
                return redirect('/login'); // Redirect ke login Breeze
            }
        }

        return $response;
    }
}
