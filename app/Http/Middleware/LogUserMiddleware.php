<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogUserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('Middleware Filament - User: '.optional(auth()->user())->email);

        return $next($request);
    }
}
