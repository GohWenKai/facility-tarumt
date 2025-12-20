@extends('layouts.app')

@section('content')
<style>
/* 2FA Settings Page */
.settings-card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; max-width: 600px; margin: 0 auto; }
.settings-header { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; padding: 1.5rem; }
.settings-header h1 { font-size: 1.25rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.75rem; }
.settings-body { padding: 1.5rem; }

/* Status Badge */
.status-section { display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: #f8fafc; border-radius: 12px; margin-bottom: 1.5rem; }
.status-label { font-weight: 600; color: #1e293b; }
.status-badge { padding: 0.375rem 0.875rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
.status-badge.enabled { background: #d1fae5; color: #047857; }
.status-badge.disabled { background: #fee2e2; color: #b91c1c; }

/* Method Options */
.method-options { margin: 1.5rem 0; }
.method-option { display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 2px solid #e2e8f0; border-radius: 12px; margin-bottom: 0.75rem; cursor: pointer; transition: all 0.2s ease; }
.method-option:hover { border-color: #3b82f6; background: #f8fafc; }
.method-option.selected { border-color: #3b82f6; background: #eff6ff; }
.method-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
.method-icon.email { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1d4ed8; }
.method-icon.sms { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #047857; }
.method-title { font-weight: 600; color: #1e293b; }
.method-desc { font-size: 0.8rem; color: #64748b; }
.method-radio { width: 20px; height: 20px; margin-left: auto; }

/* OTP Input */
.otp-confirm { background: #f8fafc; border-radius: 12px; padding: 1.25rem; margin: 1.5rem 0; }
.otp-confirm-title { font-weight: 600; color: #1e293b; margin-bottom: 0.75rem; }
.otp-input-group { display: flex; gap: 0.75rem; }
.otp-input-group input { flex: 1; padding: 0.75rem 1rem; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1.25rem; text-align: center; letter-spacing: 0.25em; }

/* Buttons */
.btn-enable { width: 100%; padding: 0.875rem; border: none; border-radius: 12px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-weight: 600; cursor: pointer; transition: all 0.2s ease; }
.btn-enable:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35); }
.btn-disable { width: 100%; padding: 0.875rem; border: none; border-radius: 12px; background: #fee2e2; color: #b91c1c; font-weight: 600; cursor: pointer; transition: all 0.2s ease; }
.btn-disable:hover { background: #fecaca; }

/* Info Box */
.info-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 1rem; margin-bottom: 1.5rem; }
.info-box i { color: #3b82f6; }
.info-box p { margin: 0; font-size: 0.875rem; color: #1e40af; }

/* Alert */
.alert-custom { padding: 1rem; border-radius: 12px; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem; }
.alert-success { background: #d1fae5; color: #047857; }
.alert-error { background: #fee2e2; color: #b91c1c; }
.alert-info { background: #dbeafe; color: #1d4ed8; }
</style>

<div class="container py-4">
    <div class="settings-card">
        <div class="settings-header">
            <h1><i class="bi bi-shield-lock-fill"></i> Two-Factor Authentication</h1>
        </div>
        
        <div class="settings-body">
            @if(session('success'))
                <div class="alert-custom alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert-custom alert-error">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ session('error') }}
                </div>
            @endif
            
            @if(session('info'))
                <div class="alert-custom alert-info">
                    <i class="bi bi-info-circle-fill"></i>
                    {{ session('info') }}
                </div>
            @endif

            <!-- Current Status -->
            <div class="status-section">
                <span class="status-label">Current Status</span>
                @if($user->two_factor_enabled)
                    <span class="status-badge enabled"><i class="bi bi-check-circle-fill me-1"></i> Enabled</span>
                @else
                    <span class="status-badge disabled"><i class="bi bi-x-circle-fill me-1"></i> Disabled</span>
                @endif
            </div>

            @if(!$user->two_factor_enabled)
                <!-- Enable 2FA Section -->
                <div class="info-box">
                    <p><i class="bi bi-info-circle-fill me-2"></i> 
                    Two-factor authentication adds an extra layer of security to your account. 
                    You'll need to enter a code from your email or phone when logging in.</p>
                </div>

                <form action="{{ route('2fa.enable') }}" method="POST" id="enableForm">
                    @csrf
                    <input type="hidden" name="method" value="email">
                    
                    <div class="method-options">
                        <div class="method-option selected">
                            <div class="method-icon email"><i class="bi bi-envelope-fill"></i></div>
                            <div>
                                <div class="method-title">Email Verification</div>
                                <div class="method-desc">Receive codes at {{ $user->email }}</div>
                            </div>
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 1.25rem;"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn-enable">
                        <i class="bi bi-shield-check me-2"></i> Enable 2FA
                    </button>
                </form>

                @if(session('info'))
                    <!-- OTP Confirmation -->
                    <div class="otp-confirm">
                        <div class="otp-confirm-title">Enter verification code:</div>
                        <form action="{{ route('2fa.confirm') }}" method="POST">
                            @csrf
                            <div class="otp-input-group">
                                <input type="text" name="otp" maxlength="6" placeholder="000000" required>
                                <button type="submit" class="btn-enable" style="flex: 0 0 auto; width: auto; padding: 0.75rem 1.5rem;">
                                    Confirm
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

            @else
                <!-- Disable 2FA Section -->
                <div class="info-box">
                    <p><i class="bi bi-check-circle-fill me-2"></i> 
                    Your account is protected with 2FA via <strong>{{ ucfirst($user->two_factor_method ?? 'Email') }}</strong>.</p>
                </div>

                <form action="{{ route('2fa.disable') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Enter your password to disable 2FA:</label>
                        <input type="password" name="password" class="form-control" required>
                        @error('password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn-disable" onclick="return confirm('Are you sure? Your account will be less secure.')">
                        <i class="bi bi-shield-x me-2"></i> Disable 2FA
                    </button>
                </form>
            @endif

            <div class="mt-4 text-center">
                <a href="{{ route('profile.show') }}" class="text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> Back to Profile
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function selectMethod(method) {
    document.querySelectorAll('.method-option').forEach(el => el.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
}
document.querySelector('.method-option').classList.add('selected');
</script>
@endsection
