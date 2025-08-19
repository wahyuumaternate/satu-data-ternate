<?php


// ============================================================================
// MIDDLEWARE (OPTIONAL) - app/Http/Middleware/CaptchaRateLimit.php
// ============================================================================

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class CaptchaRateLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'captcha:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 10)) { // 10 attempts per minute
            return response()->json([
                'success' => false,
                'error' => 'Too many captcha requests. Please try again later.'
            ], 429);
        }
        
        RateLimiter::hit($key, 60); // 1 minute window
        
        return $next($request);
    }
}