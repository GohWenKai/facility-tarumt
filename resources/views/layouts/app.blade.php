<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TARUMT Facility Booking</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- ADDED: Bootstrap Icons (Required for <i class="bi"> to work) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        /* Header Avatar - Animated */
        .header-avatar-wrapper {
            position: relative;
            animation: float 3s ease-in-out infinite;
        }
        .header-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.8);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            background: white;
        }
        .header-avatar-wrapper:hover .header-avatar {
            transform: scale(1.15);
            box-shadow: 0 4px 16px rgba(0,0,0,0.3);
        }
        
        /* Floating Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-4px); }
        }
        
        /* Bounce Animation for Profile Page */
        .profile-avatar-wrapper {
            animation: bounce 2s ease-in-out infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        .profile-avatar-wrapper:hover {
            animation: wiggle 0.5s ease;
        }
        @keyframes wiggle {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-5deg); }
            75% { transform: rotate(5deg); }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('users.dashboard') }}">
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
            
            <!-- ADDED: Hamburger Menu for Mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    @guest
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    @else
                        
                        <!-- Booking Management Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-calendar-check"></i> Booking Management
                            </a>
                            <ul class="dropdown-menu shadow">
                                <li>
                                    <a class="dropdown-item" href="{{ route('facilities.index') }}">
                                        <i class="bi bi-plus-circle me-2"></i>Book a Room
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('history') }}">
                                        <i class="bi bi-clock-history me-2"></i>Booking History
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Credits Display -->
                        <li class="nav-item mx-2">
                            <span class="nav-link text-warning fw-bold bg-white bg-opacity-10 rounded px-3">
                                <i class="bi bi-coin"></i> Credits: <span id="credit-display">{{ Auth::user()->credits }}</span>
                            </span>
                        </li>

                        <!-- Notification Bell -->
                        @php
                            $unreadCount = \App\Models\Notification::where('user_id', Auth::id())->unread()->count();
                            $recentNotifications = \App\Models\Notification::where('user_id', Auth::id())
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();
                        @endphp
                        <li class="nav-item dropdown mx-2">
                            <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-bell-fill" style="font-size: 1.2rem;"></i>
                                @if($unreadCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                    </span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow" style="width: 320px; max-height: 400px; overflow-y: auto;">
                                <li class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2">
                                    <span class="fw-bold">Notifications</span>
                                    @if($unreadCount > 0)
                                        <span class="badge bg-primary">{{ $unreadCount }} new</span>
                                    @endif
                                </li>
                                <li><hr class="dropdown-divider m-0"></li>
                                @forelse($recentNotifications as $notification)
                                    <li>
                                        <a class="dropdown-item py-2 {{ !$notification->is_read ? 'bg-light' : '' }}" 
                                           href="{{ $notification->link ?? '#' }}">
                                            <div class="d-flex align-items-start gap-2">
                                                <i class="bi bi-{{ $notification->type == 'success' ? 'check-circle text-success' : ($notification->type == 'warning' ? 'exclamation-triangle text-warning' : ($notification->type == 'danger' ? 'x-circle text-danger' : 'info-circle text-primary')) }}" style="font-size: 1.25rem;"></i>
                                                <div>
                                                    <div class="fw-semibold" style="font-size: 0.9rem;">{{ $notification->title }}</div>
                                                    <small class="text-muted">{{ Str::limit($notification->message, 50) }}</small>
                                                    <div><small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small></div>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="text-center py-4">
                                        <i class="bi bi-bell-slash text-muted" style="font-size: 2rem;"></i>
                                        <p class="text-muted mb-0 mt-2">No notifications yet</p>
                                    </li>
                                @endforelse
                            </ul>
                        </li>

                        <!-- User Profile Dropdown with Animated Avatar -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-white text-decoration-none" href="#" role="button" data-bs-toggle="dropdown">
                                <div class="header-avatar-wrapper">
                                    <img src="https://api.dicebear.com/7.x/adventurer/svg?seed={{ urlencode(Auth::user()->email) }}&backgroundColor=b6e3f4" 
                                         alt="Avatar" 
                                         class="header-avatar">
                                </div>
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li class="px-3 py-2 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://api.dicebear.com/7.x/adventurer/svg?seed={{ urlencode(Auth::user()->email) }}&backgroundColor=b6e3f4" 
                                             alt="Avatar" 
                                             style="width: 40px; height: 40px; border-radius: 50%;">
                                        <div>
                                            <div class="fw-bold">{{ Auth::user()->name }}</div>
                                            <small class="text-muted">{{ Auth::user()->email }}</small>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('users.dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('transactions.index') }}">
                                        <i class="bi bi-wallet2 me-2"></i>Credit History
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.show') }}">
                                        <i class="bi bi-person-gear me-2"></i>My Profile
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </button>
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
        {{-- IMPROVED: Flash Messages (Dismissible) --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Mark notifications as read when dropdown opens -->
    @auth
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notificationDropdown = document.querySelector('.nav-item.dropdown .bi-bell-fill')?.closest('.dropdown');
            
            if (notificationDropdown) {
                notificationDropdown.addEventListener('shown.bs.dropdown', function() {
                    // Make AJAX call to mark all as read
                    fetch('{{ route("notifications.markAllRead") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    }).then(response => {
                        if (response.ok) {
                            // Remove the badge
                            const badge = notificationDropdown.querySelector('.badge.bg-danger');
                            if (badge) badge.remove();
                            
                            // Remove the "X new" badge in dropdown header
                            const newBadge = notificationDropdown.querySelector('.dropdown-header .badge.bg-primary');
                            if (newBadge) newBadge.remove();
                            
                            // Remove bg-light from all notification items (unread indicator)
                            notificationDropdown.querySelectorAll('.dropdown-item.bg-light').forEach(item => {
                                item.classList.remove('bg-light');
                            });
                        }
                    });
                });
            }
        });
    </script>
    @endauth
    
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