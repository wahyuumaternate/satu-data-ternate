<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PenanggungJawabMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->hasRole('penanggung-jawab')) {
            abort(403, 'Akses terbatas untuk Penanggung Jawab Data.');
        }

        return $next($request);
    }
}