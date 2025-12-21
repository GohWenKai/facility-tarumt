<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TARUMT Facility Booking</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/dashboard') }}">
                <i class="bi bi-building"></i> TARUMT FBS
            </a>

            <!-- Weather Widget (Premium Design) -->
            <div id="weather-widget" class="d-none d-md-flex align-items-center me-3 px-3 py-1" 
                 style="background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.05) 100%); 
                        backdrop-filter: blur(10px); 
                        border-radius: 50px; 
                        border: 1px solid rgba(255,255,255,0.2);
                        box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <div class="weather-icon-wrapper" style="position: relative;">
                    <img id="weather-icon" src="" alt="weather" style="width: 40px; height: 40px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
                </div>
                <div class="d-flex flex-column ms-2" style="line-height: 1.2;">
                    <span id="weather-temp" class="fw-bold text-white" style="font-size: 1.1rem; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">--°C</span>
                    <span id="weather-desc" class="text-white" style="font-size: 0.7rem; opacity: 0.9; text-transform: capitalize;"></span>
                </div>
                <div class="ms-2 ps-2" style="border-left: 1px solid rgba(255,255,255,0.3);">
                    <i class="bi bi-geo-alt-fill text-white" style="opacity: 0.8; font-size: 0.75rem;"></i>
                    <span class="text-white" style="font-size: 0.7rem; opacity: 0.8;">KL</span>
                </div>
            </div>
            
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto align-items-center">
                    @guest
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                    @else
                        
                        <!-- 2. The Management Dropdown (Assets, Buildings, Facilities) -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-database-gear"></i> Management
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('bookings.approval') }}"><i class="bi bi-calendar-check"></i> Booking Approval</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.scanner') }}"><i class="bi bi-qr-code-scan"></i> QR Scanner</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('admin.buildings.manage') }}"><i class="bi bi-buildings me-2"></i>Buildings</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.facilities.manage') }}"><i class="bi bi-door-open me-2"></i>Facilities</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.assets.manage') }}"><i class="bi bi-box-seam me-2"></i>Assets</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('admin.users.index') }}"><i class="bi bi-people me-2"></i>Users</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.audit_logs.index') }}"><i class="bi bi-shield-lock me-2"></i>Audit Log</a></li>
                            </ul>
                        </li>
                        

                        {{-- USER DROPDOWN (Shared) --}}
                        <li class="nav-item dropdown ms-2">
                            <a class="nav-link dropdown-toggle btn btn-light text-white bg-opacity-10 border-0" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="bi bi-person-gear me-2"></i>My Profile</a></li>
                                
                                {{-- Only Admin sees Dashboard in dropdown --}}
                                @if(Auth::user()->role === 'admin')
                                    <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</a></li>
                                @endif
                                
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

    <!-- Weather API Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const apiKey = 'f3b7f52ea2b25a034f98c0c721985f45';
            const city = 'Kuala Lumpur';
            const url = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.cod === 200) {
                        const temp = Math.round(data.main.temp);
                        const desc = data.weather[0].description;
                        const icon = data.weather[0].icon;
                        
                        document.getElementById('weather-temp').textContent = temp + '°C';
                        document.getElementById('weather-desc').textContent = desc;
                        document.getElementById('weather-icon').src = `https://openweathermap.org/img/wn/${icon}@2x.png`;
                        
                        const widget = document.getElementById('weather-widget');
                        widget.classList.remove('d-none');
                        widget.classList.add('d-flex');
                        
                        // Add fade-in animation
                        widget.style.opacity = '0';
                        widget.style.transform = 'translateY(-10px)';
                        widget.style.transition = 'all 0.5s ease';
                        setTimeout(() => {
                            widget.style.opacity = '1';
                            widget.style.transform = 'translateY(0)';
                        }, 100);
                    }
                })
                .catch(err => console.log('Weather API error:', err));
        });
    </script>
</body>
</html>
