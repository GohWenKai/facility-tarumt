@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        
        <!-- LEFT COLUMN: User Info Card -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 text-center p-3">
                <div class="card-body">
                    <!-- Avatar Placeholder -->
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    
                    <h4>{{ $user->name }}</h4>
                    <p class="text-muted mb-1">{{ ucfirst($user->role) }}</p>
                    <p class="text-muted small">{{ $user->tarumt_id }}</p>

                    <hr>
                    <!-- Credits Display -->
                    <div class="d-flex justify-content-between px-3">
                        <span class="fw-bold">Credits:</span>
                        <span class="badge bg-success fs-6">{{ $user->credits }}</span>
                    </div>
                    <div class="d-flex justify-content-between px-3 mt-2">
                        <span class="fw-bold">Joined:</span>
                        <span>{{ $user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Edit Form -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold">Update Profile Details</div>
                <div class="card-body">
                    
                    {{-- 1. General Error Summary (Optional but helpful) --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    {{-- Make sure this route matches your controller logic --}}
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <!-- Name Field -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" 
                                       name="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $user->name) }}" 
                                       required>
                                
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Email Field -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" 
                                       name="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $user->email) }}" 
                                       required>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Address Field -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" 
                                       name="address" 
                                       class="form-control @error('address') is-invalid @enderror" 
                                       value="{{ old('address', $user->address) }}" 
                                       required>

                                @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Phone Number Field -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" 
                                       name="tel" 
                                       class="form-control @error('tel') is-invalid @enderror" 
                                       value="{{ old('tel', $user->tel) }}" 
                                       required>

                                @error('tel')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection