<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckEmailVerified
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->email_verified_at) {
            // User's email is not verified
            return response()->json(['status' => false , 'error' => 'Email not verified.'], 403);
        }
        return $next($request);
    }
}
