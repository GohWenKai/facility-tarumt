<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Carbon\Carbon;

class AuthService
{
    /**
     * Handle the entire login logic (Captcha -> Lockout -> Auth -> Credits)
     */
    public function loginUser($tarumt_id, $password, $captchaResponse, $ip)
    {
        // 1. Verify CAPTCHA (Logic moved here)
        $verify = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => config('services.recaptcha.secret'),
            'response' => $captchaResponse,
            'remoteip' => $ip
        ]);

        if (!$verify->json()['success']) {
            return ['status' => 403, 'message' => 'Captcha verification failed.'];
        }

        // 2. Check Lockout with Time-Based Unlock (15 minutes)
        $user = User::where('tarumt_id', $tarumt_id)->first();
        if ($user && $user->failed_login_attempts >= 5) {
            // Check if lockout period has expired
            if ($user->locked_until && Carbon::now()->lt($user->locked_until)) {
                $remainingMinutes = (int) ceil(Carbon::now()->diffInSeconds($user->locked_until) / 60);
                return ['status' => 403, 'message' => "Account locked. Try again in {$remainingMinutes} minutes."];
            } else {
                // Lockout period expired - reset attempts
                $user->update([
                    'failed_login_attempts' => 0,
                    'locked_until' => null
                ]);
            }
        }

        // 3. Attempt Auth
        if (!Auth::attempt(['tarumt_id' => $tarumt_id, 'password' => $password])) {
            if ($user) {
                $user->increment('failed_login_attempts');
                // Lock account after 5 failed attempts
                if ($user->failed_login_attempts >= 5) {
                    $user->update(['locked_until' => Carbon::now()->addMinutes(15)]);
                }
            }
            return ['status' => 401, 'message' => 'Invalid credentials.'];
        }

        // 4. Success Logic (Credits & Updates)
        
        // Regenerate session ID to prevent session fixation attacks (5.1.1)
        session()->regenerate();
        
        $user = Auth::user();
        
        // Credit Logic
        if ($user->last_login_at) {
            $lastLogin = Carbon::parse($user->last_login_at);
            if (!$lastLogin->isSameWeek(Carbon::now()) && $user->role !== 'admin') {
                $user->credits = 10;
            }
        }

        $user->update([
            'last_login_at' => now(),
            'ip_address' => $ip,
            'failed_login_attempts' => 0,
            'credits' => $user->credits 
        ]);

        // Create Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return ['status' => 200, 'user' => $user, 'token' => $token];
    }
}