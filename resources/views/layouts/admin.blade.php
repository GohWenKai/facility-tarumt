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
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('admin.buildings.manage') }}"><i class="bi bi-buildings me-2"></i>Buildings</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.facilities.manage') }}"><i class="bi bi-door-open me-2"></i>Facilities</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.assets.manage') }}"><i class="bi bi-box-seam me-2"></i>Assets</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('admin.users.index') }}"><i class="bi bi-box-seam me-2"></i>Users</a></li>
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
                                        <button type="submit" class="dropdown-item text-danger">Logout</button>
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
</body>
</html>