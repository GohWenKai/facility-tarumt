<?php

/**
 * Security Headers Middleware
 * Author: [Your Name Here]
 * 
 * Purpose: Add security headers to all HTTP responses to prevent common attacks
 * Design Pattern: Middleware Pattern
 * 
 * Security Headers Added:
 * - X-Frame-Options: Prevents clickjacking attacks
 * - X-Content-Type-Options: Prevents MIME type sniffing
 * - X-XSS-Protection: Enables browser XSS protection
 * - Strict-Transport-Security: Enforces HTTPS
 * - Content-Security-Policy: Restricts resource loading
 */

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

        // Prevent clickjacking attacks
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        
        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Enable browser XSS protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Enforce HTTPS (only in production)
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }
        
        // Content Security Policy - allow CDN resources for Bootstrap and reCAPTCHA
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://www.google.com https://www.gstatic.com; " .
               "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://www.google.com; " .
               "font-src 'self' https://cdn.jsdelivr.net; " .
               "img-src 'self' data: https:; " .
               "frame-src https://www.google.com;";
        
        $response->headers->set('Content-Security-Policy', $csp);
        
        // Remove server signature
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
