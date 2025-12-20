<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role  <-- We add this parameter
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // 1. Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Check if the user's role matches the required role
        // We allow '|' to split roles (e.g. "admin|lecturer")
        $userRole = Auth::user()->role; // 'admin', 'student', or 'lecturer'
        $allowedRoles = explode('|', $role); 

        if (!in_array($userRole, $allowedRoles)) {
            // If role doesn't match, show 403 Forbidden error
            
            abort(403, 'UNAUTHORIZED ACTION. You do not have access to this page.');
        }

        return $next($request);
    }
}