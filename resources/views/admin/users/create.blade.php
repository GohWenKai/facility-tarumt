@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="page-title mb-1">Create New User</h1>
                <p class="page-subtitle text-muted mb-0">Add a new student or lecturer to the system</p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card form-card">
        <div class="card-body p-4">
            <form action="{{ route('admin.users.store') }}" method="POST" id="createUserForm">
                @csrf

                <!-- Section: Personal Information -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="bi bi-person"></i>
                        </div>
                        <h3 class="section-title">Personal Information</h3>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-floating-custom">
                                <label class="form-label-custom">Full Name <span class="required">*</span></label>
                                <div class="input-group-custom">
                                    <span class="input-icon"><i class="bi bi-person-fill"></i></span>
                                    <input type="text" name="name" class="form-control-custom @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" placeholder="Enter full name" required>
                                </div>
                                @error('name') <div class="error-message">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating-custom">
                                <label class="form-label-custom">Email Address <span class="required">*</span></label>
                                <div class="input-group-custom">
                                    <span class="input-icon"><i class="bi bi-envelope-fill"></i></span>
                                    <input type="email" name="email" class="form-control-custom @error('email') is-invalid @enderror" 
                                           value="{{ old('email') }}" placeholder="user@tarumt.edu.my" required>
                                </div>
                                @error('email') <div class="error-message">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating-custom">
                                <label class="form-label-custom">TARUMT ID <span class="required">*</span></label>
                                <div class="input-group-custom">
                                    <span class="input-icon"><i class="bi bi-credit-card-2-front-fill"></i></span>
                                    <input type="text" name="tarumt_id" class="form-control-custom @error('tarumt_id') is-invalid @enderror" 
                                           value="{{ old('tarumt_id') }}" placeholder="e.g., 22WMR12345" required>
                                </div>
                                @error('tarumt_id') <div class="error-message">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating-custom">
                                <label class="form-label-custom">Phone Number <span class="required">*</span></label>
                                <div class="input-group-custom">
                                    <span class="input-icon"><i class="bi bi-telephone-fill"></i></span>
                                    <input type="text" name="tel" class="form-control-custom @error('tel') is-invalid @enderror" 
                                           value="{{ old('tel') }}" placeholder="+60123456789" required>
                                </div>
                                @error('tel') <div class="error-message">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating-custom">
                                <label class="form-label-custom">Address <span class="required">*</span></label>
                                <div class="input-group-custom">
                                    <span class="input-icon"><i class="bi bi-geo-alt-fill"></i></span>
                                    <input type="text" name="address" class="form-control-custom @error('address') is-invalid @enderror" 
                                           value="{{ old('address') }}" placeholder="Enter full address" required>
                                </div>
                                @error('address') <div class="error-message">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Role & Access -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <h3 class="section-title">Role & Access</h3>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">Select Role <span class="required">*</span></label>
                            <div class="role-selector">
                                <label class="role-option">
                                    <input type="radio" name="role" value="student" {{ old('role') == 'student' ? 'checked' : '' }} required>
                                    <div class="role-card">
                                        <div class="role-icon student-icon">
                                            <i class="bi bi-mortarboard-fill"></i>
                                        </div>
                                        <div class="role-info">
                                            <span class="role-name">Student</span>
                                            <span class="role-desc">Can book facilities</span>
                                        </div>
                                        <div class="role-check"><i class="bi bi-check-circle-fill"></i></div>
                                    </div>
                                </label>
                                <label class="role-option">
                                    <input type="radio" name="role" value="lecturer" {{ old('role') == 'lecturer' ? 'checked' : '' }}>
                                    <div class="role-card">
                                        <div class="role-icon lecturer-icon">
                                            <i class="bi bi-briefcase-fill"></i>
                                        </div>
                                        <div class="role-info">
                                            <span class="role-name">Lecturer</span>
                                            <span class="role-desc">Priority booking access</span>
                                        </div>
                                        <div class="role-check"><i class="bi bi-check-circle-fill"></i></div>
                                    </div>
                                </label>
                            </div>
                            @error('role') <div class="error-message">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section: Security -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="bi bi-key"></i>
                        </div>
                        <h3 class="section-title">Security</h3>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-floating-custom">
                                <label class="form-label-custom">Password <span class="required">*</span></label>
                                <div class="input-group-custom">
                                    <span class="input-icon"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" name="password" id="password" 
                                           class="form-control-custom @error('password') is-invalid @enderror" 
                                           placeholder="Min 8 chars, mixed case, number, symbol" required>
                                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('password') <div class="error-message">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating-custom">
                                <label class="form-label-custom">Confirm Password <span class="required">*</span></label>
                                <div class="input-group-custom">
                                    <span class="input-icon"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                           class="form-control-custom" placeholder="Re-enter password" required>
                                    <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Password Strength Indicator -->
                        <div class="col-12">
                            <div class="password-requirements">
                                <p class="req-title">Password must contain:</p>
                                <div class="req-grid">
                                    <span class="req-item" id="req-length"><i class="bi bi-circle"></i> 8+ characters</span>
                                    <span class="req-item" id="req-upper"><i class="bi bi-circle"></i> Uppercase letter</span>
                                    <span class="req-item" id="req-lower"><i class="bi bi-circle"></i> Lowercase letter</span>
                                    <span class="req-item" id="req-number"><i class="bi bi-circle"></i> Number</span>
                                    <span class="req-item" id="req-symbol"><i class="bi bi-circle"></i> Symbol (!@#$%)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-cancel">
                        <i class="bi bi-x-lg"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-submit">
                        <i class="bi bi-person-plus-fill"></i> Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Page Header */
.page-header {
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 1.5rem;
}

.btn-back {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: #f1f5f9;
    color: #64748b;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-back:hover {
    background: #e2e8f0;
    color: #1e293b;
}

.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.025em;
}

.page-subtitle {
    font-size: 0.95rem;
}

/* Form Card */
.form-card {
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

/* Form Sections */
.form-section {
    padding: 1.5rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.form-section:last-of-type {
    border-bottom: none;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.section-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    font-size: 1rem;
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

/* Custom Form Controls */
.form-label-custom {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.required {
    color: #ef4444;
}

.input-group-custom {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 14px;
    color: #94a3b8;
    font-size: 1rem;
    z-index: 1;
}

.form-control-custom {
    width: 100%;
    padding: 0.875rem 1rem 0.875rem 2.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    background: #ffffff;
}

.form-control-custom:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.form-control-custom.is-invalid {
    border-color: #ef4444;
}

.error-message {
    color: #ef4444;
    font-size: 0.8rem;
    margin-top: 0.5rem;
}

/* Password Toggle */
.password-toggle {
    position: absolute;
    right: 12px;
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 0.5rem;
}

.password-toggle:hover {
    color: #64748b;
}

/* Role Selector */
.role-selector {
    display: flex;
    gap: 1rem;
}

.role-option {
    flex: 1;
    cursor: pointer;
}

.role-option input {
    display: none;
}

.role-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    transition: all 0.2s ease;
    background: #ffffff;
}

.role-option input:checked + .role-card {
    border-color: #3b82f6;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
}

.role-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border-radius: 12px;
    font-size: 1.25rem;
}

.student-icon {
    background: linear-gradient(135deg, #a855f7 0%, #7c3aed 100%);
    color: white;
}

.lecturer-icon {
    background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
    color: white;
}

.role-info {
    display: flex;
    flex-direction: column;
}

.role-name {
    font-weight: 600;
    color: #1e293b;
}

.role-desc {
    font-size: 0.8rem;
    color: #64748b;
}

.role-check {
    margin-left: auto;
    color: #3b82f6;
    font-size: 1.25rem;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.role-option input:checked + .role-card .role-check {
    opacity: 1;
}

/* Password Requirements */
.password-requirements {
    background: #f8fafc;
    padding: 1rem 1.25rem;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.req-title {
    font-size: 0.8rem;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 0.75rem;
}

.req-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem 1.5rem;
}

.req-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
    color: #94a3b8;
}

.req-item.valid {
    color: #10b981;
}

.req-item.valid i::before {
    content: "\F26B"; /* bi-check-circle-fill */
}

/* Form Actions */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    padding-top: 2rem;
    margin-top: 1rem;
    border-top: 1px solid #f1f5f9;
}

.btn-cancel {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 1.5rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    background: white;
    color: #64748b;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-cancel:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
    color: #475569;
}

.btn-submit {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 2rem;
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
}

/* Responsive */
@media (max-width: 768px) {
    .role-selector {
        flex-direction: column;
    }
}
</style>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.parentElement.querySelector('.password-toggle i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}

// Password strength checker
document.getElementById('password').addEventListener('input', function(e) {
    const pwd = e.target.value;
    
    toggleRequirement('req-length', pwd.length >= 8);
    toggleRequirement('req-upper', /[A-Z]/.test(pwd));
    toggleRequirement('req-lower', /[a-z]/.test(pwd));
    toggleRequirement('req-number', /[0-9]/.test(pwd));
    toggleRequirement('req-symbol', /[^A-Za-z0-9]/.test(pwd));
});

function toggleRequirement(id, isValid) {
    const el = document.getElementById(id);
    if (isValid) {
        el.classList.add('valid');
    } else {
        el.classList.remove('valid');
    }
}
</script>
@endsection