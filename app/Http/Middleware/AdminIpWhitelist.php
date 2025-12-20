<?php

/**
 * Admin IP Whitelist Middleware
 * Author: [Your Name Here]
 * 
 * Purpose: Restrict admin routes to whitelisted IP addresses for enhanced security
 * Design Pattern: Middleware Pattern
 * Security Practice: Network-level access control
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminIpWhitelist
{
    /**
     * Whitelisted IP addresses that can access admin routes
     * Add production IPs here
     */
    protected $whitelist = [
        '127.0.0.1',           // Localhost IPv4
        '::1',                  // Localhost IPv6
        // Add your production admin IPs here:
        // '192.168.1.100',
        // '203.0.113.0/24',   // CIDR support
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip IP check in local development (COMMENTED OUT FOR DEMO)
        // if (app()->environment('local', 'testing')) {
        //     return $next($request);
        // }
        $clientIp = $request->ip();

        // Check if IP is in whitelist
        if (!in_array($clientIp, $this->whitelist)) {
            // Log unauthorized access attempt
            \Log::warning('Unauthorized admin access attempt', [
                'ip' => $clientIp,
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
            ]);

            abort(403, 'Access denied. Your IP address is not authorized to access this area.');
        }

        return $next($request);
    }
}
