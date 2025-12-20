@extends('layouts.guest')

@section('content')
<script src="https://www.google.com/recaptcha/api.js"></script>

<style>
/* ========================================
   LOGIN PAGE - PREMIUM HCI DESIGN
   ======================================== */

.login-page {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
    position: relative;
    overflow: hidden;
}

/* Animated Background Effects */
.bg-effects {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background-image: 
        radial-gradient(circle at 20% 30%, rgba(59, 130, 246, 0.2) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(139, 92, 246, 0.15) 0%, transparent 50%);
    pointer-events: none;
    z-index: 0;
}

/* Main Content Area */
.login-main {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    position: relative;
    z-index: 1;
}

/* Login Card */
.login-card {
    width: 100%;
    max-width: 420px;
    background: white;
    border-radius: 24px;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
    overflow: hidden;
}

/* Gradient Top Accent */
.card-accent {
    height: 5px;
    background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 50%, #ec4899 100%);
}

.card-body {
    padding: 2.5rem;
}

/* Header Section */
.login-header {
    text-align: center;
    margin-bottom: 2rem;
}

.brand-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #f1f5f9;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    margin-bottom: 1.5rem;
}
.brand-badge i { color: #3b82f6; font-size: 1rem; }
.brand-badge span { font-size: 0.8rem; font-weight: 600; color: #475569; }

.logo-icon {
    width: 72px;
    height: 72px;
    border-radius: 18px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.25rem;
    box-shadow: 0 12px 30px rgba(59, 130, 246, 0.4);
}
.logo-icon i { font-size: 2rem; color: white; }

.login-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 0.375rem;
}

.login-subtitle {
    font-size: 0.9rem;
    color: #64748b;
    margin: 0;
}

/* Error Alert (HCI: Clear Feedback) */
.error-alert {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    border: 1px solid #fecaca;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    display: none;
    align-items: center;
    gap: 0.75rem;
}
.error-alert.show { display: flex; }
.error-alert i { color: #dc2626; font-size: 1.25rem; flex-shrink: 0; }
.error-alert span { color: #b91c1c; font-size: 0.875rem; }

/* Form Groups (HCI: Clear Labels) */
.form-group {
    margin-bottom: 1.25rem;
}

.form-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 1rem;
    color: #94a3b8;
    font-size: 1.1rem;
    transition: color 0.2s ease;
    z-index: 1;
    pointer-events: none;
}

.form-input {
    width: 100%;
    padding: 0.9rem 1rem 0.9rem 3rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    background: #f8fafc;
    color: #1e293b;
}

.form-input:focus {
    outline: none;
    border-color: #3b82f6;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.form-input:focus ~ .input-icon {
    color: #3b82f6;
}

/* Password Toggle (HCI: User Control) */
.password-toggle {
    position: absolute;
    right: 1rem;
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 0.25rem;
    transition: color 0.2s ease;
    z-index: 2;
}
.password-toggle:hover { color: #3b82f6; }

/* reCAPTCHA */
.recaptcha-wrapper {
    display: flex;
    justify-content: center;
    margin: 1.5rem 0;
    transform: scale(0.95);
    transform-origin: center;
}

/* Submit Button (HCI: Clear Primary Action) */
.btn-login {
    width: 100%;
    padding: 1rem;
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.35);
}

.btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.45);
}

.btn-login:disabled {
    background: #94a3b8;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.spinner {
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255,255,255,0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    display: none;
}

.btn-login.loading .spinner { display: block; }
.btn-login.loading .btn-text { display: none; }

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Register Link */
.login-footer {
    text-align: center;
    margin-top: 1.75rem;
    padding-top: 1.5rem;
    border-top: 1px solid #f1f5f9;
}

.login-footer p {
    margin: 0;
    font-size: 0.875rem;
    color: #64748b;
}

.login-footer a {
    color: #3b82f6;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s ease;
}

.login-footer a:hover {
    color: #1d4ed8;
    text-decoration: underline;
}

/* Footer Copyright (HCI: Consistent Layout) */
.page-footer {
    padding: 1.5rem;
    text-align: center;
    position: relative;
    z-index: 1;
}

.page-footer p {
    margin: 0;
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.5);
}

/* Responsive */
@media (max-width: 480px) {
    .card-body { padding: 1.75rem; }
    .login-title { font-size: 1.25rem; }
    .recaptcha-wrapper { transform: scale(0.85); }
}
</style>

<div class="login-page">
    <div class="bg-effects"></div>
    
    <!-- Main Content -->
    <div class="login-main">
        <div class="login-card">
            <div class="card-accent"></div>
            
            <div class="card-body">
                <!-- Header -->
                <div class="login-header">
                    <div class="brand-badge">
                        <i class="bi bi-building"></i>
                        <span>TARUMT FBS</span>
                    </div>
                    
                    <div class="logo-icon">
                        <i class="bi bi-person-lock"></i>
                    </div>
                    
                    <h1 class="login-title">Welcome Back</h1>
                    <p class="login-subtitle">Sign in to access your account</p>
                </div>

                <!-- Error Alert -->
                <div id="error-box" class="error-alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span id="error-text"></span>
                </div>

                <!-- Login Form -->
                <form id="loginForm">
                    @csrf

                    <!-- Student/Staff ID -->
                    <div class="form-group">
                        <label class="form-label">Student / Staff ID</label>
                        <div class="input-wrapper">
                            <input type="text" 
                                   name="tarumt_id" 
                                   class="form-input" 
                                   id="tarumt_id"
                                   placeholder="Enter your ID" 
                                   required 
                                   autofocus>
                            <i class="bi bi-person-badge input-icon"></i>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-wrapper">
                            <input type="password" 
                                   name="password" 
                                   class="form-input" 
                                   id="password"
                                   placeholder="Enter your password" 
                                   required>
                            <i class="bi bi-lock-fill input-icon"></i>
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- reCAPTCHA -->
                    <div class="recaptcha-wrapper">
                        <div class="g-recaptcha" data-sitekey="6Lcj1BQsAAAAAFa-4mbexFmBhpZeLsoJvV4oDqOu"></div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-login" id="loginBtn">
                        <span class="btn-text">Sign In</span>
                        <div class="spinner"></div>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer at Bottom -->
    <footer class="page-footer">
        <p>© {{ date('Y') }} TARUMT Facility Booking System</p>
    </footer>
</div>

<script>
// Toggle Password Visibility
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
    }
}

// Form Submission
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const errorBox = document.getElementById('error-box');
    const errorText = document.getElementById('error-text');
    const loginBtn = document.getElementById('loginBtn');

    // Reset UI
    errorBox.classList.remove('show');
    loginBtn.classList.add('loading');
    loginBtn.disabled = true;

    // Prepare Data
    const formData = new FormData(this);
    const csrfToken = document.querySelector('input[name="_token"]').value;

    try {
        const response = await fetch("{{ route('login') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const result = await response.json();

        if (response.ok && result.status === 'success') {
            window.location.href = result.data.redirect_url;
        } else {
            throw new Error(result.message || 'Login failed');
        }

    } catch (error) {
        errorText.innerText = error.message.includes('object') 
            ? 'Invalid credentials or captcha failed.' 
            : error.message;
        errorBox.classList.add('show');

        loginBtn.classList.remove('loading');
        loginBtn.disabled = false;

        if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
    }
});
</script>
@endsection