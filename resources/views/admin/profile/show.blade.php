@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1 class="page-title">
            <i class="bi bi-person-badge header-icon"></i>
            My Admin Profile
        </h1>
        <p class="page-subtitle text-muted mb-0">View and manage your account details</p>
    </div>

    <div class="row g-4">
        <!-- Left Column: Profile Card -->
        <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="profile-card">
                <!-- Avatar Section -->
                <div class="profile-header">
                    <div class="profile-avatar-wrapper">
                        {{-- DiceBear Adventurer API - Generates unique cartoon avatars --}}
                        <img src="https://api.dicebear.com/7.x/adventurer/svg?seed={{ urlencode($user->email) }}&backgroundColor=ffd5dc,ffdfbf,c0aede" 
                             alt="{{ $user->name }}" 
                             class="profile-avatar">
                    </div>
                    <h3 class="profile-name">{{ $user->name }}</h3>
                    <span class="role-badge admin">
                        <i class="bi bi-shield-fill"></i>
                        {{ ucfirst($user->role) }}
                    </span>
                </div>

                <!-- Profile Details -->
                <div class="profile-details">
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="bi bi-credit-card-2-front-fill"></i>
                        </div>
                        <div class="detail-content">
                            <span class="detail-label">TARUMT ID</span>
                            <span class="detail-value">{{ $user->tarumt_id ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div class="detail-content">
                            <span class="detail-label">Email Address</span>
                            <span class="detail-value">{{ $user->email }}</span>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div class="detail-content">
                            <span class="detail-label">Phone Number</span>
                            <span class="detail-value">{{ $user->tel ?? 'Not set' }}</span>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div class="detail-content">
                            <span class="detail-label">Address</span>
                            <span class="detail-value">{{ $user->address ?? 'Not set' }}</span>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="bi bi-calendar-check-fill"></i>
                        </div>
                        <div class="detail-content">
                            <span class="detail-label">Member Since</span>
                            <span class="detail-value">{{ $user->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Edit Button -->
                <div class="profile-actions">
                    <a href="{{ route('admin.profile.edit') }}" class="btn-edit-profile">
                        <i class="bi bi-pencil-fill"></i>
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Column: Recent Bookings -->
        <div class="col-lg-8">
            <div class="bookings-card">
                <div class="bookings-header">
                    <div>
                        <h3 class="bookings-title">
                            <i class="bi bi-calendar2-week"></i>
                            Recent System Bookings
                        </h3>
                        <p class="bookings-subtitle">Latest 5 booking requests across the system</p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="btn-view-all">
                        View All <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table bookings-table">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>User</th>
                                <th>Facility</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($recentBookings) && $recentBookings->count() > 0)
                                @foreach($recentBookings as $booking)
                                <tr>
                                    <td>
                                        <span class="booking-id">#{{ Str::limit($booking->id, 8, '...') }}</span>
                                    </td>
                                    
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-avatar-small">
                                                {{ strtoupper(substr($booking->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <span class="user-name">{{ $booking->user ? $booking->user->name : 'Unknown' }}</span>
                                                <span class="user-role">{{ $booking->user ? ucfirst($booking->user->role) : '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <span class="facility-name">{{ $booking->facility ? $booking->facility->name : 'Deleted' }}</span>
                                    </td>
                                    
                                    <td>
                                        <div class="date-time">
                                            <span class="date">{{ \Carbon\Carbon::parse($booking->start_time)->format('M d') }}</span>
                                            <span class="time">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</span>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        @php
                                            $statusClass = match($booking->status) {
                                                'approved' => 'approved',
                                                'pending' => 'pending',
                                                'rejected' => 'rejected',
                                                'cancelled' => 'cancelled',
                                                default => 'unknown'
                                            };
                                        @endphp
                                        <span class="status-badge {{ $statusClass }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    
                                    <td class="text-end">
                                        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="action-btn view">
                                            <i class="bi bi-eye-fill"></i>
                                            View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <i class="bi bi-calendar-x"></i>
                                            <span>No recent bookings found</span>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Page Header */
.page-header { margin-bottom: 1.5rem; }
.page-title { font-size: 1.75rem; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem; }
.header-icon { color: #ef4444; }
.page-subtitle { font-size: 0.95rem; }

/* Profile Card */
.profile-card { background: white; border: 1px solid #e2e8f0; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }

.profile-header { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 2rem; text-align: center; }

/* Avatar - Premium Circular Design with Animated Ring */
.profile-avatar-wrapper {
    position: relative;
    display: inline-block;
    margin-bottom: 1rem;
}
.profile-avatar-wrapper::before {
    content: '';
    position: absolute;
    inset: -5px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ef4444, #f59e0b, #8b5cf6, #3b82f6, #ef4444);
    background-size: 400% 400%;
    animation: adminAvatarGlow 3s ease infinite;
    z-index: 0;
}
@keyframes adminAvatarGlow {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}
.profile-avatar { 
    position: relative;
    z-index: 1;
    width: 100px; 
    height: 100px; 
    border-radius: 50%; 
    border: 4px solid rgba(255,255,255,0.95);
    box-shadow: 0 8px 32px rgba(0,0,0,0.4);
    object-fit: cover;
}

.profile-name { color: white; font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; }
.role-badge { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 1rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
.role-badge.admin { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }

.profile-details { padding: 1.5rem; }
.detail-item { display: flex; align-items: flex-start; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid #f1f5f9; }
.detail-item:last-child { border-bottom: none; }
.detail-icon { width: 40px; height: 40px; border-radius: 10px; background: #f8fafc; display: flex; align-items: center; justify-content: center; color: #64748b; flex-shrink: 0; }
.detail-content { display: flex; flex-direction: column; gap: 0.125rem; min-width: 0; }
.detail-label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; }
.detail-value { font-size: 0.9375rem; color: #1e293b; word-break: break-word; }

.profile-actions { padding: 0 1.5rem 1.5rem; }
.btn-edit-profile { display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 0.875rem; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); }
.btn-edit-profile:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4); color: white; }

/* Bookings Card */
.bookings-card { background: white; border: 1px solid #e2e8f0; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }

.bookings-header { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); color: white; }
.bookings-title { display: flex; align-items: center; gap: 0.5rem; font-size: 1.125rem; font-weight: 600; margin: 0; }
.bookings-subtitle { font-size: 0.8rem; color: #94a3b8; margin: 0.25rem 0 0; }
.btn-view-all { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: rgba(255,255,255,0.1); color: white; border-radius: 8px; font-size: 0.8rem; font-weight: 500; text-decoration: none; transition: all 0.2s ease; }
.btn-view-all:hover { background: rgba(255,255,255,0.2); color: white; }

/* Bookings Table */
.bookings-table { margin: 0; }
.bookings-table thead { background: #f8fafc; }
.bookings-table thead th { padding: 1rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; border-bottom: 2px solid #e2e8f0; }
.bookings-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background-color 0.15s ease; }
.bookings-table tbody tr:hover { background: #f8fafc; }
.bookings-table tbody td { padding: 1rem; vertical-align: middle; }

.booking-id { font-family: 'SF Mono', monospace; font-size: 0.8rem; color: #64748b; background: #f1f5f9; padding: 0.25rem 0.5rem; border-radius: 4px; }

.user-cell { display: flex; align-items: center; gap: 0.75rem; }
.user-avatar-small { width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); display: flex; align-items: center; justify-content: center; font-size: 0.875rem; font-weight: 600; color: white; flex-shrink: 0; }
.user-name { display: block; font-weight: 600; color: #1e293b; font-size: 0.9rem; }
.user-role { display: block; font-size: 0.75rem; color: #94a3b8; }

.facility-name { color: #475569; font-size: 0.9rem; }

.date-time { display: flex; flex-direction: column; }
.date-time .date { font-weight: 600; color: #1e293b; }
.date-time .time { font-size: 0.8rem; color: #64748b; }

.status-badge { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
.status-badge.approved { background: #d1fae5; color: #059669; }
.status-badge.pending { background: #fef3c7; color: #d97706; }
.status-badge.rejected { background: #fee2e2; color: #dc2626; }
.status-badge.cancelled { background: #f1f5f9; color: #64748b; }

.action-btn { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; border: 2px solid #e2e8f0; border-radius: 8px; background: white; color: #64748b; font-size: 0.8rem; font-weight: 500; text-decoration: none; transition: all 0.2s ease; }
.action-btn.view:hover { border-color: #3b82f6; color: #3b82f6; background: #eff6ff; }

.empty-state { display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 3rem; color: #94a3b8; }
.empty-state i { font-size: 2rem; }

/* Responsive */
@media (max-width: 991px) {
    .bookings-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
}
</style>
@endsection