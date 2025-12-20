<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                
                // --- YOUR CUSTOM REDIRECT LOGIC ---
                
                // 1. Get the current logged-in user
                $user = Auth::guard($guard)->user(); 
                
                // 2. Check Role and Redirect
                $role = trim(strtolower($user->role ?? ''));

                if ($role === 'admin') {
                    return redirect()->route('dashboard'); // Admin
                } else {
                    return redirect()->route('users.dashboard'); // Student/Lecturer
                }
                
                // ----------------------------------
            }
        }

        return $next($request);
    }
}