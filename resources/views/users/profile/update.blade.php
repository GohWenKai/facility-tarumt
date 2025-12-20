@extends('layouts.app')

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

                    <!-- Credits Display (Module 2 Requirement) -->
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
                    
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Name & Email -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="tel" class="form-control" value="{{ old('tel', $user->tel) }}" required>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="text-primary"><i class="bi bi-shield-lock"></i> Change Password (Optional)</h6>

                        <!-- Password Change Section -->
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Required only if changing password">
                            @error('current_password')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror">
                                @error('new_password')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="new_password_confirmation" class="form-control">
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent Activity (Bonus) -->
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-white fw-bold">Recent Bookings</div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Facility</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $booking)
                            <tr>
                                <td class="ps-3">{{ $booking->facility->name }}</td>
                                <td>{{ $booking->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $booking->status == 'approved' ? 'success' : ($booking->status == 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-3">No recent activity.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection