@extends('layouts.app')

@section('content')

{{-- CUSTOM DASHBOARD STYLES --}}
<style>
    /* Hero Section */
    .dashboard-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        color: white;
        border-radius: 20px;
        padding: 3rem 2.5rem;
        position: relative;
        overflow: hidden;
        margin-bottom: 2.5rem;
        box-shadow: 0 10px 20px -5px rgba(30, 58, 138, 0.4);
    }
    .hero-pattern {
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        background-image: radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0, transparent 25%);
    }

    /* Credit Wallet Card */
    .credit-wallet {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        padding: 1.5rem;
        min-width: 250px;
        color: white;
    }

    /* Action Cards */
    .action-card {
        border: none;
        border-radius: 16px;
        background: white;
        transition: all 0.2s ease;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        height: 100%;
        position: relative;
        overflow: hidden;
        border: 1px solid #f1f5f9;
    }
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 25px -5px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1;
    }
    /* Colorful accent bars */
    .action-bar { height: 4px; width: 100%; position: absolute; top: 0; left: 0; }
    .bar-blue { background: #3b82f6; }
    .bar-green { background: #10b981; }
    .bar-purple { background: #8b5cf6; }
    .bar-orange { background: #f59e0b; }

    .action-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 1rem;
    }
    .bg-blue-light { background: #eff6ff; color: #3b82f6; }
    .bg-green-light { background: #ecfdf5; color: #10b981; }
    .bg-purple-light { background: #f5f3ff; color: #8b5cf6; }
    .bg-orange-light { background: #fffbeb; color: #f59e0b; }

    /* Notice Card */
    .notice-card {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.25rem;
        border: 1px dashed #cbd5e1;
    }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            
            {{-- 1. HERO BANNER --}}
            <div class="dashboard-hero d-flex flex-wrap justify-content-between align-items-center gap-4">
                <div class="hero-pattern"></div> <!-- Decorative -->
                
                <div class="position-relative z-1">
                    <span class="badge bg-white bg-opacity-20 text-white border border-white border-opacity-25 mb-2 px-3 py-2 rounded-pill">
                        Student Portal v2.0
                    </span>
                    <h1 class="fw-bold display-6 mb-1">Welcome, {{ explode(' ', Auth::user()->name)[0] }}!</h1>
                    <p class="mb-0 opacity-75 fs-5">Ready to book your next study session?</p>
                </div>

                {{-- Credit Wallet Widget --}}
                <div class="credit-wallet text-end position-relative z-1">
                    <small class="text-uppercase fw-bold opacity-75" style="letter-spacing: 1px;">Balance</small>
                    <div class="d-flex align-items-center justify-content-end gap-2 mt-1">
                        <i class="bi bi-wallet2 fs-3 opacity-50"></i>
                        <h2 class="mb-0 fw-bold display-5">{{ Auth::user()->credits }}</h2>
                        <span class="fs-5 opacity-75">CR</span>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                
                {{-- 2. MAIN ACTIONS GRID --}}
                <div class="col-lg-8">
                    <h5 class="fw-bold text-dark mb-3 ps-1">Quick Actions</h5>
                    <div class="row g-4">
                        
                        <!-- Card 1: Book Facility -->
                        <div class="col-md-6">
                            <a href="{{ route('facilities.index') }}" class="text-decoration-none">
                                <div class="action-card p-4">
                                    <div class="action-bar bar-blue"></div>
                                    <div class="action-icon bg-blue-light">
                                        <i class="bi bi-calendar-plus"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">Book a Facility</h5>
                                    <p class="text-muted small mb-0">Reserve labs, discussion rooms, and halls instantly.</p>
                                </div>
                            </a>
                        </div>

                        <!-- Card 2: My History -->
                        <div class="col-md-6">
                            <a href="{{ route('history') }}" class="text-decoration-none">
                                <div class="action-card p-4">
                                    <div class="action-bar bar-green"></div>
                                    <div class="action-icon bg-green-light">
                                        <i class="bi bi-clock-history"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">My History</h5>
                                    <p class="text-muted small mb-0">Track your past bookings and upcoming schedules.</p>
                                </div>
                            </a>
                        </div>

                        <!-- Card 3: Profile -->
                        <div class="col-md-6">
                            <a href="{{ route('profile') }}" class="text-decoration-none">
                                <div class="action-card p-4">
                                    <div class="action-bar bar-purple"></div>
                                    <div class="action-icon bg-purple-light">
                                        <i class="bi bi-person-circle"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">My Profile</h5>
                                    <p class="text-muted small mb-0">Manage account settings and preferences.</p>
                                </div>
                            </a>
                        </div>

                        <!-- Card 4: Search Records -->
                        <div class="col-md-6">
                            <a href="{{ route('history.search') }}" class="text-decoration-none">
                                <div class="action-card p-4">
                                    <div class="action-bar bar-orange"></div>
                                    <div class="action-icon bg-orange-light">
                                        <i class="bi bi-search"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">Search Records</h5>
                                    <p class="text-muted small mb-0">Find specific transactions or receipts.</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- 3. SIDEBAR NOTICES --}}
                <div class="col-lg-4">
                    <h5 class="fw-bold text-dark mb-3 ps-1">Updates & Info</h5>
                    
                    <!-- Notice 1 -->
                    <div class="notice-card mb-3">
                        <div class="d-flex gap-3">
                            <i class="bi bi-info-circle-fill text-primary fs-4"></i>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Weekly Reset</h6>
                                <p class="text-muted small mb-0" style="line-height: 1.4;">
                                    Your booking credits will automatically reset to <strong>10</strong> every Sunday at midnight.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Security Status -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-secondary text-uppercase small mb-3 ls-1">Security Status</h6>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted small">Last Login</span>
                                <span class="fw-bold text-dark small">
                                    {{ Auth::user()->last_login_at ? \Carbon\Carbon::parse(Auth::user()->last_login_at)->diffForHumans() : 'Just now' }}
                                </span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Current IP</span>
                                <code class="bg-light px-2 py-1 rounded text-primary fw-bold">{{ request()->ip() }}</code>
                            </div>

                            <hr class="my-3 opacity-25">
                            
                            <div class="d-flex align-items-center text-success small">
                                <i class="bi bi-shield-check me-2 fs-5"></i>
                                <span>Account is secure</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection