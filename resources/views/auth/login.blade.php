@extends('layouts.guest')

@section('content')
<script src="https://www.google.com/recaptcha/api.js"></script> 

<!-- Custom Styles for this page only -->
<style>
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
    }
    .login-card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .login-header {
        background-color: #fff;
        padding-bottom: 0;
    }
    .form-floating > label {
        color: #6c757d;
    }
    .btn-login {
        border-radius: 50px;
        padding: 12px;
        font-weight: bold;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }
    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
    }
</style>

<div class="container d-flex flex-column justify-content-center min-vh-100 py-4">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            
            <div class="card login-card p-4">
                <div class="card-body">
                    <!-- Logo / Header Section -->
                    <div class="text-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; font-size: 24px;">
                            <i class="bi bi-person-lock"></i> <!-- Requires Bootstrap Icons -->
                        </div>
                        <h4 class="fw-bold text-dark">Welcome Back</h4>
                        <p class="text-muted small">Please sign in to your account</p>
                    </div>

                    <!-- Error Alert Box -->
                    <div id="error-box" class="alert alert-danger d-none text-center small rounded-3"></div>

                    <!-- Login Form -->
                    <form id="loginForm">
                        @csrf 

                        <!-- Student ID Field -->
                        <div class="form-floating mb-3">
                            <input type="text" name="tarumt_id" class="form-control" id="floatingInput" placeholder="123456" required autofocus>
                            <label for="floatingInput">Student / Staff ID</label>
                        </div>
                        
                        <!-- Password Field -->
                        <div class="form-floating mb-4">
                            <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                            <label for="floatingPassword">Password</label>
                        </div>

                        <!-- Recaptcha -->
                        <div class="mb-4 d-flex justify-content-center">
                            <div class="g-recaptcha" data-sitekey="6Lcj1BQsAAAAAFa-4mbexFmBhpZeLsoJvV4oDqOu"></div>
                        </div>
                        
                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary w-100 btn-login" id="loginBtn">
                            <span id="btnText">Login</span>
                            <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- JAVASCRIPT LOGIC -->
<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault(); 

    // UI Elements
    let errorBox = document.getElementById('error-box');
    let loginBtn = document.getElementById('loginBtn');
    let btnText = document.getElementById('btnText');
    let btnSpinner = document.getElementById('btnSpinner');

    // 1. Reset UI State
    errorBox.classList.add('d-none');
    errorBox.innerText = "";
    
    // 2. Set Loading State
    loginBtn.disabled = true;
    btnText.innerText = "Signing in...";
    btnSpinner.classList.remove('d-none');
    
    // 3. Prepare Data
    let formData = new FormData(this);
    let csrfToken = document.querySelector('input[name="_token"]').value;

    try {
        let response = await fetch("{{ route('login') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        let result = await response.json();

        if (response.ok && result.status === 'success') {
            // SUCCESS
            window.location.href = result.data.redirect_url;
        } else {
            // ERROR
            throw new Error(result.message || 'Login Failed');
        }

    } catch (error) {
        // HANDLE ERRORS
        errorBox.classList.remove('d-none');
        
        // Handle specific validation errors if available in the catch block
        // (Note: standard fetch doesn't throw on 422, so we usually catch logical errors here)
        // Since we threw "result.message" manually above, we catch it here.
        if(error.message === 'Login Failed' || error.message === '[object Object]') {
             // Try to be more specific if possible, otherwise generic
             errorBox.innerText = "Invalid credentials or captcha failed.";
        } else {
             errorBox.innerText = error.message;
        }

        // Reset Button
        loginBtn.disabled = false;
        btnText.innerText = "Login";
        btnSpinner.classList.add('d-none');

        // Reset Captcha
        if(typeof grecaptcha !== 'undefined') grecaptcha.reset();
    }
});
</script>
@endsection