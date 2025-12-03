@extends('layouts.admin')

@section('content')

{{-- 1. INTERNAL CSS (Matches your Dashboard Theme) --}}
<style>
    .dashboard-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .user-avatar {
        width: 40px;
        height: 40px;
        background-color: #f1f5f9;
        color: #64748b;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
    }
</style>

<div class="container-fluid px-4 py-4" style="background-color: #f8fafc; min-height: 100vh;">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark m-0">User Management</h3>
            <p class="text-muted small mb-0">Manage students, lecturers, and admins</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus-fill me-2"></i>Add New User
        </a>
    </div>

    {{-- SEARCH & FILTER CARD --}}
    <div class="dashboard-card mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.users.index') }}" method="GET"> <!-- Replace with your actual route -->
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" 
                                   placeholder="Search name, email, or ID..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-dark w-100">Search</button>
                    </div>
                    <div class="col-md-6 text-end">
                        @if(request('search'))
                            <a href="{{ route('admin.users.index') }}" class="btn btn-link text-danger text-decoration-none">
                                <i class="bi bi-x-circle"></i> Clear Filter
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- USER TABLE --}}
    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 text-secondary text-uppercase small" style="width: 50px;">#</th>
                        <th class="text-secondary text-uppercase small">User Profile</th>
                        <th class="text-secondary text-uppercase small">Role</th>
                        <th class="text-secondary text-uppercase small">Credits</th>
                        <th class="text-secondary text-uppercase small">Status</th>
                        <th class="text-secondary text-uppercase small">Joined</th>
                        <th class="text-end pe-4 text-secondary text-uppercase small">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="ps-4 fw-bold text-muted">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                        
                        {{-- User Profile Column --}}
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">{{ $user->name }}</h6>
                                    <small class="text-muted d-block">{{ $user->email }}</small>
                                    <small class="text-primary fw-bold" style="font-size: 0.75rem;">
                                        ID: {{ $user->tarumt_id ?? 'N/A' }}
                                    </small>
                                </div>
                            </div>
                        </td>

                        {{-- Role Badge --}}
                        <td>
                            @php
                                $roleColor = match($user->role) {
                                    'admin' => 'danger',
                                    'lecturer' => 'info text-dark',
                                    'student' => 'success',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $roleColor }} bg-opacity-10 border border-{{ $roleColor }} text-{{ $roleColor === 'info text-dark' ? 'dark' : $roleColor }} px-3 py-2 rounded-pill">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>

                        {{-- Credits --}}
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-coin text-warning me-2"></i>
                                <span class="fw-bold">{{ $user->credits }}</span>
                            </div>
                        </td>

                        {{-- Status (Example Logic) --}}
                        <td>
                            @if($user->created_at->isToday())
                                <span class="text-warning small fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Pending</span>
                            @else
                                <span class="text-success small fw-bold"><i class="bi bi-exclamation-circle-fill me-1"></i> Verified</span>
                            @endif
                        </td>

                        {{-- Joined Date --}}
                        <td>
                            <small class="text-muted">{{ $user->created_at->format('d M Y') }}</small>
                        </td>

                        {{-- Actions --}}
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                {{-- Edit --}}
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-white border hover-shadow" title="Edit User">
                                    <i class="bi bi-pencil-fill text-muted"></i>
                                </a>

                                {{-- Reset Credits (Optional Admin Action) --}}
                                <form action="{{ route('admin.users.credits', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Reset credits to 10?');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-white border hover-shadow" title="Reset Credits">
                                        <i class="bi bi-arrow-counterclockwise text-primary"></i>
                                    </button>
                                </form>

                                {{-- Delete --}}
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    
                                    <button type="submit" class="btn btn-sm btn-white border hover-shadow text-danger" title="Delete User">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center justify-content-center opacity-50">
                                <i class="bi bi-people fs-1 mb-2"></i>
                                <p>No users found matching your search.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="card-footer bg-white border-top-0 d-flex justify-content-center py-3">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection