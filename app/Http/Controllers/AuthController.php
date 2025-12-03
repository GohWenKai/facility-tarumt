<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\AuthService; // Don't forget this import!

class AuthController extends Controller
{
    protected $authService;

    // 1. Dependency Injection (Service Pattern)
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // ============================================================
    // VIEW METHODS (These were missing and caused the error)
    // ============================================================
    public function showLogin() 
    { 
        return view('auth.login'); 
    }

    public function showRegister() 
    { 
        return view('auth.register'); 
    }

    // ============================================================
    // LOGIN LOGIC (Using Service Pattern)
    // ============================================================
    public function login(Request $request)
    {
        // 1. Validate Inputs
        $validator = Validator::make($request->all(), [
            'tarumt_id' => 'required',
            'password' => 'required',
            'g-recaptcha-response' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error', 
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Delegate to Service
        $result = $this->authService->loginUser(
            $request->tarumt_id,
            $request->password,
            $request->input('g-recaptcha-response'),
            $request->ip()
        );

        // 3. Handle Failure
        if ($result['status'] !== 200) {
            return response()->json([
                'status' => 'error', 
                'message' => $result['message']
            ], $result['status']);
        }

        // 4. Handle Success
        $user = $result['user'];
        $role = trim(strtolower($user->role));
        $redirectUrl = $role === 'admin' ? '/dashboard' : '/users/dashboard';

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'role' => $role,
                    'token' => $result['token'],
                    'redirect_url' => $redirectUrl
                ]
            ], 200);
        }

        return redirect()->intended($redirectUrl);
    }

    // ============================================================
    // LOGOUT LOGIC
    // ============================================================
    public function logout(Request $request)
    {
        $user = $request->user();

        // 1. API Token Logout
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        // 2. Web Session Logout
        Auth::guard('web')->logout();
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // 3. Smart Response
        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Logged out successfully']);
        }

        return redirect('/login');
    }
}