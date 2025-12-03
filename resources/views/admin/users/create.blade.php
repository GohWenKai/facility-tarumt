@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-white fw-bold">Create New User</div>
        <div class="card-body">
            
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="row">
                    <!-- Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Email -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- TARUMT ID -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">TARUMT ID</label>
                        <input type="text" name="tarumt_id" class="form-control @error('tarumt_id') is-invalid @enderror" value="{{ old('tarumt_id') }}" required>
                        @error('tarumt_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Role (Restricted Dropdown) -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="" disabled selected>Select Role</option>
                            <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                            <option value="lecturer" {{ old('role') == 'lecturer' ? 'selected' : '' }}>Lecturer</option>
                        </select>
                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Phone -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="tel" class="form-control @error('tel') is-invalid @enderror" value="{{ old('tel') }}" required>
                        @error('tel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Address -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}" required>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Password -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>

                <div class="text-end">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection