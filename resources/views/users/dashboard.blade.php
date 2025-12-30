@extends('layouts.app')

@section('content')
<style>
/* ========================================
   USER DASHBOARD - HCI-ENHANCED DESIGN
   ======================================== */

/* Hero Section */
.dashboard-hero {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
    color: white;
    border-radius: 24px;
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
    margin-bottom: 2rem;
}
.hero-pattern {
    position: absolute;
    top: 0; right: 0; bottom: 0; left: 0;
    background-image: radial-gradient(circle at 90% 10%, rgba(255,255,255,0.15) 0, transparent 40%);
}
.hero-greeting { font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem; }
.hero-subtitle { opacity: 0.85; font-size: 1rem; margin: 0; }

/* Credit Wallet */
.credit-wallet {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    padding: 1.25rem 1.5rem;
    text-align: center;
}
.credit-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; }
.credit-value { font-size: 2.5rem; font-weight: 700; line-height: 1; }
.credit-unit { font-size: 1rem; opacity: 0.7; }

/* Quick Stats Row */
.stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem; }
@media (max-width: 768px) { .stats-row { grid-template-columns: repeat(2, 1fr); } }
.stat-card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; transition: all 0.2s ease; }
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
.stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
.stat-icon.total { background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); color: #4f46e5; }
.stat-icon.pending { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #d97706; }
.stat-icon.approved { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #059669; }
.stat-icon.rejected { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #dc2626; }
.stat-value { font-size: 1.5rem; font-weight: 700; color: #1e293b; line-height: 1; }
.stat-label { font-size: 0.8rem; color: #64748b; margin-top: 0.25rem; }

/* Action Cards */
.action-card {
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    background: white;
    padding: 1.5rem;
    height: 100%;
    transition: all 0.2s ease;
    text-decoration: none;
    display: block;
    position: relative;
    overflow: hidden;
}
.action-card:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(0,0,0,0.1); border-color: #cbd5e1; }
.action-bar { height: 4px; width: 100%; position: absolute; top: 0; left: 0; }
.action-bar.blue { background: linear-gradient(90deg, #3b82f6, #1d4ed8); }
.action-bar.green { background: linear-gradient(90deg, #10b981, #059669); }
.action-bar.purple { background: linear-gradient(90deg, #8b5cf6, #6d28d9); }
.action-bar.orange { background: linear-gradient(90deg, #f59e0b, #d97706); }
.action-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1rem; }
.action-icon.blue { background: #eff6ff; color: #3b82f6; }
.action-icon.green { background: #ecfdf5; color: #10b981; }
.action-icon.purple { background: #f5f3ff; color: #8b5cf6; }
.action-icon.orange { background: #fffbeb; color: #f59e0b; }
.action-title { font-size: 1rem; font-weight: 600; color: #1e293b; margin-bottom: 0.375rem; }
.action-desc { font-size: 0.85rem; color: #64748b; margin: 0; line-height: 1.4; }

/* Sidebar Cards */
.sidebar-card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; margin-bottom: 1rem; }
.sidebar-header { padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
.sidebar-title { font-size: 0.9rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 0.5rem; margin: 0; }
.sidebar-title i { color: #3b82f6; }
.sidebar-body { padding: 1rem 1.25rem; }

/* Upcoming Booking Item */
.booking-item { display: flex; align-items: center; gap: 1rem; padding: 0.875rem; background: #f8fafc; border-radius: 12px; margin-bottom: 0.75rem; transition: all 0.2s ease; }
.booking-item:last-child { margin-bottom: 0; }
.booking-item:hover { background: #f1f5f9; }
.booking-date { width: 50px; height: 50px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border-radius: 10px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; flex-shrink: 0; }
.booking-date .day { font-size: 1.25rem; font-weight: 700; line-height: 1; }
.booking-date .month { font-size: 0.65rem; text-transform: uppercase; opacity: 0.9; }
.booking-info { flex: 1; min-width: 0; }
.booking-facility { font-weight: 600; color: #1e293b; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.booking-time { font-size: 0.8rem; color: #64748b; }
.booking-action { flex-shrink: 0; }
.btn-ticket { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border: 2px solid #e2e8f0; border-radius: 8px; background: white; color: #64748b; text-decoration: none; transition: all 0.2s ease; }
.btn-ticket:hover { border-color: #3b82f6; color: #3b82f6; background: #eff6ff; }

/* Pending Badge */
.pending-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; margin-bottom: 0.5rem; }
.pending-item:last-child { margin-bottom: 0; }
.pending-icon { width: 32px; height: 32px; background: #fef3c7; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #d97706; }
.pending-info { flex: 1; }
.pending-facility { font-weight: 600; color: #92400e; font-size: 0.85rem; }
.pending-status { font-size: 0.75rem; color: #b45309; }

/* Empty State */
.empty-state { text-align: center; padding: 1.5rem; color: #94a3b8; }
.empty-state i { font-size: 2rem; opacity: 0.5; margin-bottom: 0.5rem; }
.empty-state p { font-size: 0.85rem; margin: 0; }

/* Security Card */
.security-item { display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; }
.security-label { color: #64748b; font-size: 0.85rem; }
.security-value { font-weight: 600; color: #1e293b; font-size: 0.85rem; }
.security-badge { display: flex; align-items: center; gap: 0.5rem; color: #059669; font-size: 0.85rem; padding: 0.75rem 0; border-top: 1px solid #f1f5f9; margin-top: 0.5rem; }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            
            <!-- HERO BANNER -->
            <div class="dashboard-hero d-flex flex-wrap justify-content-between align-items-center gap-4">
                <div class="hero-pattern"></div>
                
                <div class="position-relative z-1">
                    <span class="badge bg-white bg-opacity-20 text-black border border-white border-opacity-25 mb-2 px-3 py-2 rounded-pill">
                        <i class="bi bi-star-fill me-1"></i> 
                        @if(Auth::user()->role === 'admin')
                            Admin Portal
                        @elseif(Auth::user()->role === 'lecturer')
                            Lecturer Portal
                        @else
                            Student Portal
                        @endif
                    </span>
                    <h1 class="hero-greeting">Welcome back, {{ explode(' ', Auth::user()->name)[0] }}!</h1>
                    <p class="hero-subtitle">Here's an overview of your facility bookings</p>
                </div>

                <!-- Credit Wallet -->
                <div class="credit-wallet position-relative z-1">
                    <div class="credit-label">Credit Balance</div>
                    <div class="d-flex align-items-baseline justify-content-center gap-1 mt-1">
                        <span class="credit-value">{{ Auth::user()->credits }}</span>
                        <span class="credit-unit">CR</span>
                    </div>
                </div>
            </div>

            <!-- QUICK STATS ROW (HCI: Visibility of System Status) -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-icon total"><i class="bi bi-calendar2-week"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['total'] ?? 0 }}</div>
                        <div class="stat-label">Total Bookings</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon pending"><i class="bi bi-hourglass-split"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['pending'] ?? 0 }}</div>
                        <div class="stat-label">Pending</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon approved"><i class="bi bi-check-circle-fill"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['approved'] ?? 0 }}</div>
                        <div class="stat-label">Approved</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon rejected"><i class="bi bi-x-circle-fill"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['rejected'] ?? 0 }}</div>
                        <div class="stat-label">Rejected</div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                
                <!-- MAIN ACTIONS -->
                <div class="col-lg-8">
                    <h5 class="fw-bold text-dark mb-3">Quick Actions</h5>
                    <div class="row g-3">
                        
                        <!-- Book Facility -->
                        <div class="col-md-6">
                            <a href="{{ route('facilities.index') }}" class="action-card">
                                <div class="action-bar blue"></div>
                                <div class="action-icon blue"><i class="bi bi-calendar-plus"></i></div>
                                <h5 class="action-title">Book a Facility</h5>
                                <p class="action-desc">Reserve labs, rooms, and halls for your activities.</p>
                            </a>
                        </div>

                        <!-- My History -->
                        <div class="col-md-6">
                            <a href="{{ route('history') }}" class="action-card">
                                <div class="action-bar green"></div>
                                <div class="action-icon green"><i class="bi bi-clock-history"></i></div>
                                <h5 class="action-title">Booking History</h5>
                                <p class="action-desc">View past bookings and download tickets.</p>
                            </a>
                        </div>

                        <!-- Profile -->
                        <div class="col-md-6">
                            <a href="{{ route('profile.show') }}" class="action-card">
                                <div class="action-bar purple"></div>
                                <div class="action-icon purple"><i class="bi bi-person-circle"></i></div>
                                <h5 class="action-title">My Profile</h5>
                                <p class="action-desc">Manage your account settings and info.</p>
                            </a>
                        </div>

                        <!-- Search Records -->
                        <div class="col-md-6">
                            <a href="{{ route('history.search') }}" class="action-card">
                                <div class="action-bar orange"></div>
                                <div class="action-icon orange"><i class="bi bi-search"></i></div>
                                <h5 class="action-title">Search Records</h5>
                                <p class="action-desc">Find specific bookings or receipts.</p>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- SIDEBAR -->
                <div class="col-lg-4">
                    
                    <!-- Upcoming Bookings (HCI: Recognition over Recall) -->
                    <div class="sidebar-card">
                        <div class="sidebar-header">
                            <h5 class="sidebar-title"><i class="bi bi-calendar-event"></i> Upcoming Bookings</h5>
                            <span class="badge bg-primary">{{ count($upcomingBookings ?? []) }}</span>
                        </div>
                        <div class="sidebar-body">
                            @forelse($upcomingBookings ?? [] as $booking)
                                <div class="booking-item">
                                    <div class="booking-date">
                                        <span class="day">{{ \Carbon\Carbon::parse($booking->start_time)->format('d') }}</span>
                                        <span class="month">{{ \Carbon\Carbon::parse($booking->start_time)->format('M') }}</span>
                                    </div>
                                    <div class="booking-info">
                                        <div class="booking-facility">{{ $booking->facility->name ?? 'Unknown' }}</div>
                                        <div class="booking-time">
                                            <i class="bi bi-clock"></i>
                                            {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                        </div>
                                    </div>
                                    <div class="booking-action">
                                        <a href="{{ route('booking.ticket', $booking->id) }}" class="btn-ticket" title="Download Ticket">
                                            <i class="bi bi-ticket-perforated"></i>
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <i class="bi bi-calendar-x"></i>
                                    <p>No upcoming bookings</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Pending Approval (HCI: Visibility of System Status) -->
                    @if(count($pendingBookings ?? []) > 0)
                    <div class="sidebar-card">
                        <div class="sidebar-header">
                            <h5 class="sidebar-title"><i class="bi bi-hourglass-split" style="color: #d97706 !important;"></i> Awaiting Approval</h5>
                        </div>
                        <div class="sidebar-body">
                            @foreach($pendingBookings as $pending)
                                <div class="pending-item">
                                    <div class="pending-icon"><i class="bi bi-clock"></i></div>
                                    <div class="pending-info">
                                        <div class="pending-facility">{{ $pending->facility->name ?? 'Unknown' }}</div>
                                        <div class="pending-status">Submitted {{ $pending->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Security Status -->
                    <div class="sidebar-card">
                        <div class="sidebar-header">
                            <h5 class="sidebar-title"><i class="bi bi-shield-check" style="color: #059669 !important;"></i> Security</h5>
                        </div>
                        <div class="sidebar-body">
                            <div class="security-item">
                                <span class="security-label">Last Login</span>
                                <span class="security-value">
                                    {{ Auth::user()->last_login_at ? \Carbon\Carbon::parse(Auth::user()->last_login_at)->diffForHumans() : 'Just now' }}
                                </span>
                            </div>
                            <div class="security-item">
                                <span class="security-label">Current IP</span>
                                <code class="bg-light px-2 py-1 rounded text-primary">{{ request()->ip() }}</code>
                            </div>
                            <div class="security-badge">
                                <i class="bi bi-shield-fill-check"></i>
                                <span>Account is secure</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- 3D STREET VIEW SECTION (Premium Design) -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="streetview-container">
                        <div class="streetview-header">
                            <div class="d-flex align-items-center gap-3">
                                <div class="streetview-icon">
                                    <i class="bi bi-camera-reels"></i>
                                </div>
                                <div>
                                    <h5 class="m-0 fw-bold">360° Street View</h5>
                                    <small>TARUMT Kampar Campus, Perak</small>
                                </div>
                            </div>
                            <div class="streetview-badge">
                                <i class="bi bi-hand-index-thumb"></i>
                                <span>Drag to look around</span>
                            </div>
                        </div>
                        <div class="streetview-wrapper">
                            <!-- Google Street View Embed -->
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!4v1703152800000!6m8!1m7!1sCAoSLEFGMVFpcE9ZN0s5VXdMRm5jN1NmNU15Y0xGelBrblVfWWJPX0tMRkVGNjNM!2m2!1d4.329243!2d101.143015!3f0!4f0!5f0.7820865974627469"
                                width="100%" 
                                height="100%" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                            
                            <!-- Overlay Controls -->
                            <div class="streetview-overlay">
                                <div class="view-mode-tabs">
                                    <button class="view-tab active" onclick="switchView('street')">
                                        <i class="bi bi-camera-reels"></i> Street View
                                    </button>
                                    <button class="view-tab" onclick="switchView('satellite')">
                                        <i class="bi bi-globe"></i> Satellite
                                    </button>
                                    <button class="view-tab" onclick="switchView('map')">
                                        <i class="bi bi-map"></i> Map
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="streetview-info-panel">
                            <div class="info-item">
                                <i class="bi bi-building"></i>
                                <span>TARUMT Kampar Campus</span>
                            </div>
                            <div class="info-item">
                                <i class="bi bi-pin-map"></i>
                                <span>Jalan Kolej, Taman Bandar Baru, 31900 Kampar, Perak</span>
                            </div>
                            <div class="info-item">
                                <i class="bi bi-telephone"></i>
                                <span>05-468 8888</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
/* ==================== STREET VIEW STYLES ==================== */
.streetview-container {
    background: #1a1a2e;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    border: 1px solid rgba(255,255,255,0.1);
}

.streetview-header {
    padding: 1.25rem 1.5rem;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border-bottom: 1px solid rgba(255,255,255,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.streetview-header h5 {
    color: white;
}

.streetview-header small {
    color: rgba(255,255,255,0.6);
}

.streetview-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    box-shadow: 0 4px 20px rgba(240, 147, 251, 0.4);
}

.streetview-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255,255,255,0.1);
    padding: 0.5rem 1rem;
    border-radius: 50px;
    color: rgba(255,255,255,0.8);
    font-size: 0.85rem;
    border: 1px solid rgba(255,255,255,0.1);
}

.streetview-badge i {
    animation: point 1s ease-in-out infinite;
}

@keyframes point {
    0%, 100% { transform: translateX(0); }
    50% { transform: translateX(3px); }
}

.streetview-wrapper {
    position: relative;
    height: 450px;
    background: #0a0a1a;
}

.streetview-wrapper iframe {
    width: 100%;
    height: 100%;
}

/* View Mode Overlay */
.streetview-overlay {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
}

.view-mode-tabs {
    display: flex;
    background: rgba(0,0,0,0.7);
    backdrop-filter: blur(10px);
    border-radius: 50px;
    padding: 0.25rem;
    gap: 0.25rem;
}

.view-tab {
    padding: 0.5rem 1rem;
    border: none;
    background: transparent;
    color: rgba(255,255,255,0.7);
    font-size: 0.85rem;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.view-tab:hover {
    color: white;
}

.view-tab.active {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.streetview-info-panel {
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 1rem;
}

.streetview-info-panel .info-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: white;
    font-size: 0.9rem;
}

.streetview-info-panel .info-item i {
    font-size: 1.1rem;
    opacity: 0.9;
}

/* Responsive */
@media (max-width: 768px) {
    .streetview-info-panel {
        flex-direction: column;
        text-align: center;
    }
    .streetview-info-panel .info-item {
        justify-content: center;
    }
    .streetview-wrapper {
        height: 350px;
    }
    .streetview-badge {
        display: none;
    }
}
</style>

<script>
// View switching function
function switchView(mode) {
    const iframe = document.querySelector('.streetview-wrapper iframe');
    const tabs = document.querySelectorAll('.view-tab');
    
    // Update active tab
    tabs.forEach(tab => tab.classList.remove('active'));
    event.target.closest('.view-tab').classList.add('active');
    
    // TARUMT Kampar coordinates
    const lat = 4.329243;
    const lng = 101.143015;
    
    switch(mode) {
        case 'street':
            // Street View panorama
            iframe.src = `https://www.google.com/maps/embed?pb=!4v1703152800000!6m8!1m7!1sCAoSLEFGMVFpcE9ZN0s5VXdMRm5jN1NmNU15Y0xGelBrblVfWWJPX0tMRkVGNjNM!2m2!1d${lat}!2d${lng}!3f0!4f0!5f0.7820865974627469`;
            break;
        case 'satellite':
            // Satellite view
            iframe.src = `https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1500!2d${lng}!3d${lat}!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNMKwMTknNDUuMyJOIDEwMcKwMDgnMzQuOSJF!5e1!3m2!1sen!2smy!4v1703152800000`;
            break;
        case 'map':
            // Regular map view
            iframe.src = `https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3000!2d${lng}!3d${lat}!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNMKwMTknNDUuMyJOIDEwMcKwMDgnMzQuOSJF!5e0!3m2!1sen!2smy!4v1703152800000`;
            break;
    }
}
</script>
@endsection
