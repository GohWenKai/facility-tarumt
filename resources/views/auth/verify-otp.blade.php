@extends('layouts.guest')

@section('content')
<script src="https://www.google.com/recaptcha/api.js"></script>

<style>
/* OTP Verification Page */
.otp-page {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
    position: relative;
    overflow: hidden;
}

.bg-effects {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background-image: 
        radial-gradient(circle at 20% 30%, rgba(59, 130, 246, 0.2) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(139, 92, 246, 0.15) 0%, transparent 50%);
    pointer-events: none;
    z-index: 0;
}

.otp-main {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    position: relative;
    z-index: 1;
}

.otp-card {
    width: 100%;
    max-width: 420px;
    background: white;
    border-radius: 24px;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
    overflow: hidden;
}

.card-accent { height: 5px; background: linear-gradient(90deg, #10b981 0%, #3b82f6 50%, #8b5cf6 100%); }
.card-body { padding: 2.5rem; }

/* Header */
.otp-header { text-align: center; margin-bottom: 2rem; }
.lock-icon { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #10b981 0%, #059669 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 12px 30px rgba(16, 185, 129, 0.35); }
.lock-icon i { font-size: 2.25rem; color: white; }
.otp-title { font-size: 1.5rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; }
.otp-subtitle { font-size: 0.9rem; color: #64748b; margin: 0; }
.email-badge { display: inline-block; background: #f1f5f9; padding: 0.375rem 0.875rem; border-radius: 8px; font-size: 0.8rem; color: #1e293b; font-weight: 500; margin-top: 0.75rem; }

/* OTP Input */
.otp-inputs { display: flex; gap: 0.75rem; justify-content: center; margin: 2rem 0; }
.otp-input { width: 50px; height: 60px; text-align: center; font-size: 1.5rem; font-weight: 700; border: 2px solid #e2e8f0; border-radius: 12px; background: #f8fafc; color: #1e293b; transition: all 0.2s ease; }
.otp-input:focus { outline: none; border-color: #3b82f6; background: white; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }

/* Error Alert */
.error-alert { background: #fee2e2; border: 1px solid #fecaca; border-radius: 12px; padding: 1rem; margin-bottom: 1.5rem; display: none; align-items: center; gap: 0.75rem; }
.error-alert.show { display: flex; }
.error-alert i { color: #dc2626; font-size: 1.25rem; }
.error-alert span { color: #b91c1c; font-size: 0.875rem; }

/* Buttons */
.btn-verify { width: 100%; padding: 1rem; border: none; border-radius: 12px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35); }
.btn-verify:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(16, 185, 129, 0.45); }
.btn-verify:disabled { background: #94a3b8; cursor: not-allowed; transform: none; }

.spinner { width: 20px; height: 20px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.8s linear infinite; display: none; }
.btn-verify.loading .spinner { display: block; }
.btn-verify.loading .btn-text { display: none; }

@keyframes spin { to { transform: rotate(360deg); } }

/* Resend */
.resend-section { text-align: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9; }
.resend-text { font-size: 0.875rem; color: #64748b; margin-bottom: 0.75rem; }
.btn-resend { background: none; border: none; color: #3b82f6; font-weight: 600; cursor: pointer; font-size: 0.875rem; }
.btn-resend:hover { text-decoration: underline; }
.btn-resend:disabled { color: #94a3b8; cursor: not-allowed; }

/* Timer */
.timer { background: #f1f5f9; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.8rem; color: #64748b; display: inline-block; margin-bottom: 1rem; }
.timer strong { color: #1e293b; }

/* Footer */
.page-footer { padding: 1.5rem; text-align: center; position: relative; z-index: 1; }
.page-footer p { margin: 0; font-size: 0.8rem; color: rgba(255, 255, 255, 0.5); }
</style>

<div class="otp-page">
    <div class="bg-effects"></div>
    
    <div class="otp-main">
        <div class="otp-card">
            <div class="card-accent"></div>
            
            <div class="card-body">
                <div class="otp-header">
                    <div class="lock-icon">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <h1 class="otp-title">Verify Your Identity</h1>
                    <p class="otp-subtitle">Enter the 6-digit code sent to your email</p>
                    <div class="email-badge">
                        <i class="bi bi-envelope-fill me-1"></i> Check your inbox
                    </div>
                </div>

                <div id="error-box" class="error-alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span id="error-text"></span>
                </div>

                <div class="timer">
                    Code expires in <strong id="countdown">5:00</strong>
                </div>

                <form id="otpForm">
                    @csrf
                    <div class="otp-inputs">
                        <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" autofocus>
                        <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]">
                        <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]">
                        <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]">
                        <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]">
                        <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]">
                    </div>
                    <input type="hidden" name="otp" id="otpHidden">

                    <button type="submit" class="btn-verify" id="verifyBtn">
                        <span class="btn-text">Verify Code</span>
                        <div class="spinner"></div>
                    </button>
                </form>

                <div class="resend-section">
                    <p class="resend-text">Didn't receive the code?</p>
                    <button type="button" class="btn-resend" id="resendBtn" onclick="resendOtp()">
                        <i class="bi bi-arrow-repeat me-1"></i> Resend Code
                    </button>
                </div>
            </div>
        </div>
    </div>

    <footer class="page-footer">
        <p>© {{ date('Y') }} TARUMT Facility Booking System</p>
    </footer>
</div>

<script>
// OTP Input Auto-Focus
const otpInputs = document.querySelectorAll('.otp-input');
otpInputs.forEach((input, index) => {
    input.addEventListener('input', (e) => {
        if (e.target.value.length === 1 && index < otpInputs.length - 1) {
            otpInputs[index + 1].focus();
        }
        updateHiddenOtp();
    });
    
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !e.target.value && index > 0) {
            otpInputs[index - 1].focus();
        }
    });
    
    input.addEventListener('paste', (e) => {
        e.preventDefault();
        const pastedData = e.clipboardData.getData('text').slice(0, 6);
        pastedData.split('').forEach((char, i) => {
            if (otpInputs[i]) otpInputs[i].value = char;
        });
        updateHiddenOtp();
    });
});

function updateHiddenOtp() {
    let otp = '';
    otpInputs.forEach(input => otp += input.value);
    document.getElementById('otpHidden').value = otp;
}

// Countdown Timer
let timeLeft = 300; // 5 minutes
const countdownEl = document.getElementById('countdown');
const timer = setInterval(() => {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    countdownEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
    if (timeLeft === 0) {
        clearInterval(timer);
        countdownEl.textContent = 'Expired';
        countdownEl.style.color = '#dc2626';
    }
    timeLeft--;
}, 1000);

// Form Submit
document.getElementById('otpForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const errorBox = document.getElementById('error-box');
    const errorText = document.getElementById('error-text');
    const verifyBtn = document.getElementById('verifyBtn');
    const otp = document.getElementById('otpHidden').value;
    
    if (otp.length !== 6) {
        errorText.innerText = 'Please enter all 6 digits';
        errorBox.classList.add('show');
        return;
    }
    
    errorBox.classList.remove('show');
    verifyBtn.classList.add('loading');
    verifyBtn.disabled = true;
    
    try {
        const response = await fetch('{{ route("verify-otp.submit") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ otp })
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            window.location.href = result.data.redirect_url;
        } else {
            throw new Error(result.message || 'Verification failed');
        }
    } catch (error) {
        errorText.innerText = error.message;
        errorBox.classList.add('show');
        verifyBtn.classList.remove('loading');
        verifyBtn.disabled = false;
    }
});

// Resend OTP
async function resendOtp() {
    const resendBtn = document.getElementById('resendBtn');
    resendBtn.disabled = true;
    resendBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Sending...';
    
    try {
        const response = await fetch('{{ route("verify-otp.resend") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            // Reset timer
            timeLeft = 300;
            resendBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Code Sent!';
            setTimeout(() => {
                resendBtn.disabled = false;
                resendBtn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Resend Code';
            }, 3000);
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        resendBtn.disabled = false;
        resendBtn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Resend Code';
        document.getElementById('error-text').innerText = error.message;
        document.getElementById('error-box').classList.add('show');
    }
}
</script>
@endsection
