@extends('layouts.app')

@section('content')

{{-- Internal CSS for specific dashboard hover effects --}}
<style>
    .hover-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid #e2e8f0;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1;
    }
    .icon-box {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.5rem;
    }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            
            {{-- 1. HERO SECTION --}}
            <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                <div class="card-body p-4 bg-primary text-white bg-gradient d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1">Welcome back, {{ Auth::user()->name }}!</h2>
                        <p class="mb-0 opacity-75">
                            {{ ucfirst(Auth::user()->role) }} Account • ID: <strong>{{ Auth::user()->tarumt_id ?? 'N/A' }}</strong>
                        </p>
                    </div>
                    <div class="text-end d-none d-md-block">
                        <span class="d-block opacity-75 small text-uppercase fw-bold ls-1">Available Credits</span>
                        <h1 class="display-4 fw-bold mb-0">
                            <i class="bi bi-coin me-2 opacity-50"></i>{{ Auth::user()->credits }}
                        </h1>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                
                {{-- 2. QUICK ACTIONS GRID --}}
                <div class="col-lg-8">
                    <h5 class="fw-bold text-dark mb-3">Quick Actions</h5>
                    <div class="row g-3">
                        
                        <!-- Book Facility -->
                        <div class="col-md-6">
                            <a href="{{ route('facilities.index') }}" class="text-decoration-none">
                                <div class="card hover-card h-100 border-0 shadow-sm">
                                    <div class="card-body d-flex align-items-center p-3">
                                        <div class="icon-box bg-primary bg-opacity-10 text-primary me-3">
                                            <i class="bi bi-building-add"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1">Book a Room</h6>
                                            <small class="text-muted">Browse labs & halls</small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- View History -->
                        <div class="col-md-6">
                            <a href="{{ route('history') }}" class="text-decoration-none">
                                <div class="card hover-card h-100 border-0 shadow-sm">
                                    <div class="card-body d-flex align-items-center p-3">
                                        <div class="icon-box bg-success bg-opacity-10 text-success me-3">
                                            <i class="bi bi-clock-history"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1">My History</h6>
                                            <small class="text-muted">Past & active bookings</small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Edit Profile -->
                        <div class="col-md-6">
                            <a href="{{ route('profile') }}" class="text-decoration-none">
                                <div class="card hover-card h-100 border-0 shadow-sm">
                                    <div class="card-body d-flex align-items-center p-3">
                                        <div class="icon-box bg-info bg-opacity-10 text-info me-3">
                                            <i class="bi bi-person-gear"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1">My Profile</h6>
                                            <small class="text-muted">Update details</small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Search Records -->
                        <div class="col-md-6">
                            <a href="{{ route('history.search') }}" class="text-decoration-none">
                                <div class="card hover-card h-100 border-0 shadow-sm">
                                    <div class="card-body d-flex align-items-center p-3">
                                        <div class="icon-box bg-warning bg-opacity-10 text-warning me-3">
                                            <i class="bi bi-search"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1">Search Records</h6>
                                            <small class="text-muted">Find specific items</small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>

                {{-- 3. SIDE PANEL (Info) --}}
                <div class="col-lg-4">
                    <h5 class="fw-bold text-dark mb-3">Notices</h5>
                    
                    <!-- Credits Info -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <div class="d-flex">
                                <i class="bi bi-info-circle-fill text-primary fs-4 me-3"></i>
                                <div>
                                    <h6 class="fw-bold">Credits Policy</h6>
                                    <p class="small text-muted mb-0">
                                        Credits reset to <strong>10</strong> every Sunday at midnight.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Info -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Account Security</h6>
                            <ul class="list-unstyled small text-muted mb-0">
                                <li class="mb-2 d-flex justify-content-between">
                                    <span>Last Login:</span>
                                    <span class="text-dark fw-bold">
                                        {{ Auth::user()->last_login_at ? \Carbon\Carbon::parse(Auth::user()->last_login_at)->diffForHumans() : 'First Login' }}
                                    </span>
                                </li>
                                <li class="d-flex justify-content-between">
                                    <span>IP Address:</span>
                                    <span class="text-dark fw-bold">{{ request()->ip() }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection