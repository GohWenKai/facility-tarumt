<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent Clickjacking
        $response->headers->set('X-Frame-Options', 'DENY');

        // Prevent Content Type Sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Basic XSS Protection (for older browsers)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        return $response;
    }
}
