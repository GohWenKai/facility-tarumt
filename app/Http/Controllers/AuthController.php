<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Services\AuthService;
use App\Models\User;
use App\Mail\OtpMail;

class AuthController extends Controller
{
    protected $authService;

    // 1. Dependency Injection (Service Pattern)
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // ============================================================
    // VIEW METHODS
    // ============================================================
    public function showLogin() 
    { 
        return view('auth.login'); 
    }

    public function showRegister() 
    { 
        return view('auth.register'); 
    }

    public function showVerifyOtp() 
    { 
        // Check if user has pending 2FA
        if (!session('2fa_user_id')) {
            return redirect()->route('login');
        }
        return view('auth.verify-otp'); 
    }

    // ============================================================
    // LOGIN LOGIC (Using Service Pattern + 2FA Support)
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

        // 4. Check if 2FA is enabled
        $user = $result['user'];
        
        if ($user->two_factor_enabled) {
            // Don't fully log in yet - store user ID in session for OTP verification
            Auth::logout();
            session(['2fa_user_id' => $user->id]);
            
            // Generate and send OTP
            $otp = $user->generateTwoFactorCode();
            
            // Send OTP via email
            try {
                Mail::to($user->email)->send(new OtpMail($otp, $user->name));
            } catch (\Exception $e) {
                \Log::error('Failed to send OTP email: ' . $e->getMessage());
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'OTP sent to your email',
                'data' => [
                    'requires_2fa' => true,
                    'redirect_url' => route('verify-otp')
                ]
            ], 200);
        }

        // 5. Handle Normal Success (No 2FA)
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
    // VERIFY OTP
    // ============================================================
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $userId = session('2fa_user_id');
        
        if (!$userId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session expired. Please login again.'
            ], 401);
        }

        $user = User::find($userId);
        
        if (!$user) {
            session()->forget('2fa_user_id');
            return response()->json([
                'status' => 'error',
                'message' => 'User not found. Please login again.'
            ], 401);
        }

        // Verify the OTP
        if (!$user->verifyTwoFactorCode($request->otp)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired code. Please try again.'
            ], 401);
        }

        // Clear OTP and session
        $user->clearTwoFactorCode();
        session()->forget('2fa_user_id');

        // Now fully log in the user
        Auth::login($user);
        
        $role = trim(strtolower($user->role));
        $redirectUrl = $role === 'admin' ? '/dashboard' : '/users/dashboard';

        return response()->json([
            'status' => 'success',
            'message' => 'Verification successful',
            'data' => [
                'redirect_url' => $redirectUrl
            ]
        ], 200);
    }

    // ============================================================
    // RESEND OTP
    // ============================================================
    public function resendOtp(Request $request)
    {
        $userId = session('2fa_user_id');
        
        if (!$userId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session expired. Please login again.'
            ], 401);
        }

        $user = User::find($userId);
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.'
            ], 401);
        }

        // Generate new OTP
        $otp = $user->generateTwoFactorCode();
        
        // Send via email
        try {
            Mail::to($user->email)->send(new OtpMail($otp, $user->name));
            return response()->json([
                'status' => 'success',
                'message' => 'New code sent to your email'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send email. Please try again.'
            ], 500);
        }
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