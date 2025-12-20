@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.profile') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="page-title">
                    <i class="bi bi-pencil-square header-icon"></i>
                    Edit Profile
                </h1>
                <p class="page-subtitle text-muted mb-0">Update your account information</p>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if ($errors->any())
        <div class="alert-card error mb-4">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if (session('success'))
        <div class="alert-card success mb-4">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left Column: Profile Preview -->
        <div class="col-lg-4">
            <div class="preview-card">
                <div class="preview-header">
                    <div class="preview-avatar">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                </div>
                <div class="preview-body">
                    <h3 class="preview-name">{{ $user->name }}</h3>
                    <span class="role-badge admin">
                        <i class="bi bi-shield-fill"></i>
                        {{ ucfirst($user->role) }}
                    </span>
                    <p class="preview-id">
                        <i class="bi bi-credit-card-2-front"></i>
                        {{ $user->tarumt_id }}
                    </p>
                    
                    <div class="preview-stats">
                        <div class="stat-item">
                            <span class="stat-label">Member Since</span>
                            <span class="stat-value">{{ $user->created_at->format('M Y') }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Last Updated</span>
                            <span class="stat-value">{{ $user->updated_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Edit Form -->
        <div class="col-lg-8">
            <div class="form-card">
                <div class="form-card-header">
                    <i class="bi bi-person-lines-fill"></i>
                    <h3>Update Profile Details</h3>
                </div>
                
                <div class="form-card-body">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Personal Details Section -->
                        <div class="form-section">
                            <h4 class="section-label">
                                <i class="bi bi-person-fill"></i>
                                Personal Information
                            </h4>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label-custom">Full Name <span class="required">*</span></label>
                                    <div class="input-group-custom">
                                        <span class="input-icon"><i class="bi bi-person-fill"></i></span>
                                        <input type="text" 
                                               name="name" 
                                               class="form-control-custom @error('name') is-invalid @enderror" 
                                               value="{{ old('name', $user->name) }}" 
                                               required>
                                    </div>
                                    @error('name')
                                        <span class="error-text">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label-custom">Email Address <span class="required">*</span></label>
                                    <div class="input-group-custom">
                                        <span class="input-icon"><i class="bi bi-envelope-fill"></i></span>
                                        <input type="email" 
                                               name="email" 
                                               class="form-control-custom @error('email') is-invalid @enderror" 
                                               value="{{ old('email', $user->email) }}" 
                                               required>
                                    </div>
                                    @error('email')
                                        <span class="error-text">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Contact Details Section -->
                        <div class="form-section">
                            <h4 class="section-label">
                                <i class="bi bi-telephone-fill"></i>
                                Contact Information
                            </h4>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label-custom">Phone Number <span class="required">*</span></label>
                                    <div class="input-group-custom">
                                        <span class="input-icon"><i class="bi bi-telephone-fill"></i></span>
                                        <input type="text" 
                                               name="tel" 
                                               class="form-control-custom @error('tel') is-invalid @enderror" 
                                               value="{{ old('tel', $user->tel) }}" 
                                               placeholder="+60123456789"
                                               required>
                                    </div>
                                    @error('tel')
                                        <span class="error-text">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label-custom">Address <span class="required">*</span></label>
                                    <div class="input-group-custom">
                                        <span class="input-icon"><i class="bi bi-geo-alt-fill"></i></span>
                                        <input type="text" 
                                               name="address" 
                                               class="form-control-custom @error('address') is-invalid @enderror" 
                                               value="{{ old('address', $user->address) }}" 
                                               required>
                                    </div>
                                    @error('address')
                                        <span class="error-text">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <a href="{{ route('admin.profile') }}" class="btn btn-cancel">
                                <i class="bi bi-x-lg"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-submit">
                                <i class="bi bi-check-lg"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ========================================
   EDIT PROFILE - AMBER/ORANGE THEME
   ======================================== */

/* Page Header */
.page-header { border-bottom: 1px solid #e2e8f0; padding-bottom: 1.5rem; }
.btn-back { display: flex; align-items: center; justify-content: center; width: 42px; height: 42px; border-radius: 12px; background: #fef3c7; color: #d97706; text-decoration: none; transition: all 0.2s ease; }
.btn-back:hover { background: #fde68a; color: #92400e; }
.page-title { font-size: 1.75rem; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem; }
.header-icon { color: #d97706; }
.page-subtitle { font-size: 0.95rem; }

/* Alert Cards */
.alert-card { display: flex; align-items: flex-start; gap: 1rem; padding: 1rem 1.25rem; border-radius: 12px; }
.alert-card.error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
.alert-card.success { background: #d1fae5; border: 1px solid #a7f3d0; color: #059669; }

/* Preview Card */
.preview-card { background: white; border: 1px solid #e2e8f0; border-radius: 20px; overflow: hidden; text-align: center; }
.preview-header { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 2rem; }
.preview-avatar { width: 80px; height: 80px; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; color: #d97706; margin: 0 auto; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
.preview-body { padding: 1.5rem; }
.preview-name { font-size: 1.25rem; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; }
.role-badge { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 1rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
.role-badge.admin { background: #fee2e2; color: #dc2626; }
.preview-id { display: flex; align-items: center; justify-content: center; gap: 0.5rem; color: #64748b; font-size: 0.9rem; margin-top: 0.75rem; }

.preview-stats { margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9; }
.stat-item { display: flex; justify-content: space-between; padding: 0.5rem 0; }
.stat-label { color: #94a3b8; font-size: 0.875rem; }
.stat-value { color: #1e293b; font-weight: 600; font-size: 0.875rem; }

/* Form Card */
.form-card { background: white; border: 1px solid #fde68a; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(217, 119, 6, 0.1); }
.form-card-header { display: flex; align-items: center; gap: 0.75rem; padding: 1.25rem 1.5rem; background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-bottom: 1px solid #fde68a; }
.form-card-header i { font-size: 1.25rem; color: #d97706; }
.form-card-header h3 { font-size: 1.1rem; font-weight: 600; color: #92400e; margin: 0; }
.form-card-body { padding: 1.5rem; }

/* Form Sections */
.form-section { margin-bottom: 2rem; }
.form-section:last-of-type { margin-bottom: 0; }
.section-label { display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; font-weight: 600; color: #d97706; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #fef3c7; }

/* Form Controls */
.form-label-custom { display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem; }
.required { color: #ef4444; }
.input-group-custom { position: relative; display: flex; align-items: center; }
.input-icon { position: absolute; left: 14px; color: #f59e0b; font-size: 1rem; z-index: 1; }
.form-control-custom { width: 100%; padding: 0.875rem 1rem 0.875rem 2.75rem; border: 2px solid #fde68a; border-radius: 12px; font-size: 0.95rem; transition: all 0.2s ease; background: #fff; }
.form-control-custom:focus { outline: none; border-color: #f59e0b; box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1); }
.form-control-custom.is-invalid { border-color: #ef4444; }
.error-text { display: block; margin-top: 0.375rem; font-size: 0.8rem; color: #ef4444; }

/* Form Actions */
.form-actions { display: flex; justify-content: flex-end; gap: 1rem; padding-top: 2rem; margin-top: 1.5rem; border-top: 1px solid #fef3c7; }
.btn-cancel { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 1.5rem; border: 2px solid #e2e8f0; border-radius: 12px; background: white; color: #64748b; font-weight: 600; text-decoration: none; transition: all 0.2s ease; }
.btn-cancel:hover { border-color: #cbd5e1; background: #f8fafc; color: #475569; }
.btn-submit { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 2rem; border: none; border-radius: 12px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; font-weight: 600; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); }
.btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4); }
</style>
@endsection