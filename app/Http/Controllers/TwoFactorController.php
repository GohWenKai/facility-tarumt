<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class TwoFactorController extends Controller
{
    /**
     * Show 2FA settings page
     */
    public function settings()
    {
        $user = Auth::user();
        return view('users.profile.two-factor', compact('user'));
    }

    /**
     * Enable 2FA
     */
    public function enable(Request $request)
    {
        $request->validate([
            'method' => 'required|in:email,sms',
        ]);

        $user = Auth::user();
        
        // Generate and send test OTP
        $otp = $user->generateTwoFactorCode();
        
        if ($request->method === 'email') {
            try {
                Mail::to($user->email)->send(new OtpMail($otp, $user->name));
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to send verification email.');
            }
        }
        
        // Store pending method in session
        session(['2fa_pending_method' => $request->method]);
        
        return back()->with('info', 'Verification code sent. Enter it to confirm 2FA setup.');
    }

    /**
     * Confirm 2FA setup with OTP
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        $method = session('2fa_pending_method', 'email');

        if (!$user->verifyTwoFactorCode($request->otp)) {
            return back()->with('error', 'Invalid or expired code.');
        }

        // Enable 2FA
        $user->two_factor_enabled = true;
        $user->two_factor_method = $method;
        $user->clearTwoFactorCode();
        $user->save();

        session()->forget('2fa_pending_method');

        return back()->with('success', 'Two-Factor Authentication enabled successfully!');
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();
        $user->two_factor_enabled = false;
        $user->two_factor_method = null;
        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        $user->save();

        return back()->with('success', 'Two-Factor Authentication disabled.');
    }
}
