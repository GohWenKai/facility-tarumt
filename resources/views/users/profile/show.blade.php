@extends('layouts.app')

@section('content')
<style>
/* ========================================
   USER PROFILE - PREMIUM HCI DESIGN
   ======================================== */

/* Profile Header */
.profile-header {
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    color: white;
    padding: 2.5rem;
    border-radius: 20px;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}
.profile-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 350px;
    height: 350px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
}

/* Avatar */
.profile-avatar {
    width: 90px;
    height: 90px;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.2);
    border: 3px solid rgba(255, 255, 255, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: 700;
    color: white;
    margin-right: 1.5rem;
}

.profile-info h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem; }
.profile-info p { opacity: 0.85; margin: 0; }
.role-badge { display: inline-flex; align-items: center; gap: 0.375rem; background: rgba(255,255,255,0.2); padding: 0.375rem 0.875rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; margin-top: 0.5rem; }

/* Credit Card */
.credit-card {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    color: white;
    border-radius: 16px;
    padding: 1.5rem;
    text-align: center;
    margin-bottom: 1.5rem;
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.25);
}
.credit-value { font-size: 3rem; font-weight: 700; line-height: 1; }
.credit-label { font-size: 0.85rem; opacity: 0.9; margin-top: 0.25rem; }

/* Info Card */
.info-card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; margin-bottom: 1.5rem; }
.info-card-header { padding: 1rem 1.25rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 0.5rem; }
.info-card-header i { color: #3b82f6; }

/* Info Item */
.info-item { padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 1rem; }
.info-item:last-child { border-bottom: none; }
.info-icon { width: 40px; height: 40px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #64748b; font-size: 1rem; flex-shrink: 0; }
.info-label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
.info-value { font-weight: 600; color: #1e293b; }

/* Edit Button */
.btn-edit { width: 100%; padding: 0.875rem; border: none; border-radius: 12px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 0.5rem; text-decoration: none; transition: all 0.2s ease; }
.btn-edit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(245, 158, 11, 0.35); color: white; }

/* Booking Card */
.booking-card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
.booking-card-header { padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
.booking-card-title { font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 0.5rem; margin: 0; }
.booking-card-title i { color: #3b82f6; }
.btn-new-booking { padding: 0.5rem 1rem; border: none; border-radius: 8px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-weight: 600; font-size: 0.8rem; text-decoration: none; display: flex; align-items: center; gap: 0.375rem; transition: all 0.2s ease; }
.btn-new-booking:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35); color: white; }

/* Booking Table */
.booking-table { width: 100%; margin: 0; }
.booking-table thead { background: #f8fafc; }
.booking-table thead th { padding: 0.875rem 1rem; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; border-bottom: 2px solid #e2e8f0; text-align: left; }
.booking-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background-color 0.15s ease; }
.booking-table tbody tr:hover { background: #f8fafc; }
.booking-table tbody td { padding: 0.875rem 1rem; vertical-align: middle; }

/* Facility Cell */
.facility-cell { display: flex; align-items: center; gap: 0.75rem; }
.facility-icon-sm { width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); display: flex; align-items: center; justify-content: center; color: #3b82f6; font-size: 1rem; }
.facility-name { font-weight: 600; color: #1e293b; font-size: 0.875rem; }
.facility-location { font-size: 0.75rem; color: #94a3b8; }

/* Status Badge */
.status-badge { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.625rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; }
.status-badge.pending { background: #fef3c7; color: #92400e; }
.status-badge.approved { background: #d1fae5; color: #047857; }
.status-badge.rejected { background: #fee2e2; color: #b91c1c; }

/* Empty State */
.empty-state { text-align: center; padding: 3rem 2rem; color: #94a3b8; }
.empty-state i { font-size: 2.5rem; opacity: 0.5; margin-bottom: 0.75rem; }
.empty-state h5 { color: #64748b; font-weight: 600; margin-bottom: 0.25rem; }
</style>

<div class="container py-4">
    
    <!-- Profile Header -->
    <div class="profile-header d-flex align-items-center">
        <div class="profile-avatar">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div class="profile-info">
            <h1>{{ $user->name }}</h1>
            <p>{{ $user->email }}</p>
            <span class="role-badge">
                <i class="bi bi-mortarboard-fill"></i>
                {{ ucfirst($user->role) }}
            </span>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-4">
            
            <!-- Credit Card -->
            <div class="credit-card">
                <div class="credit-value">{{ $user->credits }}</div>
                <div class="credit-label">Available Credits</div>
                <small style="opacity:0.7;">Resets every Sunday</small>
            </div>

            <!-- Personal Info -->
            <div class="info-card">
                <div class="info-card-header">
                    <i class="bi bi-person-fill"></i> Personal Details
                </div>
                
                <div class="info-item">
                    <div class="info-icon"><i class="bi bi-person"></i></div>
                    <div>
                        <div class="info-label">Full Name</div>
                        <div class="info-value">{{ $user->name }}</div>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon"><i class="bi bi-person-badge"></i></div>
                    <div>
                        <div class="info-label">TARUMT ID</div>
                        <div class="info-value">{{ $user->tarumt_id ?? 'N/A' }}</div>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon"><i class="bi bi-envelope"></i></div>
                    <div>
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $user->email }}</div>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon"><i class="bi bi-telephone"></i></div>
                    <div>
                        <div class="info-label">Phone</div>
                        <div class="info-value">{{ $user->tel ?? 'Not set' }}</div>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon"><i class="bi bi-geo-alt"></i></div>
                    <div>
                        <div class="info-label">Address</div>
                        <div class="info-value">{{ $user->address ?? 'Not set' }}</div>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon"><i class="bi bi-calendar"></i></div>
                    <div>
                        <div class="info-label">Member Since</div>
                        <div class="info-value">{{ $user->created_at->format('d M Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Edit Button -->
            <a href="{{ route('profile.edit') }}" class="btn-edit">
                <i class="bi bi-pencil-fill"></i> Edit Profile
            </a>
            
            <!-- 2FA Settings Button -->
            <a href="{{ route('2fa.settings') }}" class="btn-edit" style="background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); margin-top: 0.75rem;">
                <i class="bi bi-shield-lock-fill"></i> Two-Factor Auth
                @if($user->two_factor_enabled)
                    <span style="background: #10b981; padding: 0.125rem 0.5rem; border-radius: 10px; font-size: 0.7rem; margin-left: 0.5rem;">ON</span>
                @endif
            </a>
        </div>

        <!-- Right Column -->
        <div class="col-lg-8">
            <div class="booking-card">
                <div class="booking-card-header">
                    <h5 class="booking-card-title">
                        <i class="bi bi-calendar-check"></i> Recent Bookings
                    </h5>
                    <a href="{{ url('/facilities') }}" class="btn-new-booking">
                        <i class="bi bi-plus-lg"></i> New Booking
                    </a>
                </div>

                @if($bookings->isEmpty())
                    <div class="empty-state">
                        <i class="bi bi-calendar-x"></i>
                        <h5>No Bookings Yet</h5>
                        <p>Book a facility to see your history here.</p>
                    </div>
                @else
                    <table class="booking-table">
                        <thead>
                            <tr>
                                <th>Facility</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Cost</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                            <tr>
                                <td>
                                    <div class="facility-cell">
                                        <div class="facility-icon-sm"><i class="bi bi-building"></i></div>
                                        <div>
                                            <div class="facility-name">{{ $booking->facility->name }}</div>
                                            <div class="facility-location">{{ $booking->facility->building->name ?? 'Main Campus' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-weight: 500; color: #1e293b;">
                                    {{ $booking->start_time->format('d M Y') }}
                                </td>
                                <td style="color: #64748b;">
                                    {{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}
                                </td>
                                <td style="font-weight: 600; color: #1e293b;">
                                    {{ $booking->total_cost }} pts
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($booking->status) {
                                            'approved' => 'approved',
                                            'pending' => 'pending',
                                            'rejected' => 'rejected',
                                            default => 'pending'
                                        };
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if($bookings->hasPages())
                    <div class="p-3 border-top">
                        {{ $bookings->links() }}
                    </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection