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
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('users.dashboard') }}">
                <i class="bi bi-building"></i> TARUMT FBS
            </a>
            
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

                        <!-- User Profile Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle btn btn-link text-white text-decoration-none" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
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
</body>
</html>