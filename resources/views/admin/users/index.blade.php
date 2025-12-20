@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Page Header with Stats -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1 class="page-title mb-1">User Management</h1>
                <p class="page-subtitle text-muted mb-0">Manage students, lecturers, and system access</p>
            </div>
            
            <!-- Quick Stats - HCI: Visibility of System Status -->
            <div class="d-flex gap-3 align-items-center">
                <div class="stat-pill">
                    <span class="stat-count">{{ $users->total() }}</span>
                    <span class="stat-label">Total Users</span>
                </div>
                <a href="{{ route('admin.users.create') }}" class="btn-add-user">
                    <i class="bi bi-person-plus-fill"></i>
                    <span>Add New User</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Search & Filter - HCI: User Control and Freedom -->
    <div class="search-card mb-4">
        <form action="{{ route('admin.users.index') }}" method="GET" class="search-form">
            <div class="search-input-wrapper">
                <i class="bi bi-search search-icon"></i>
                <input type="text" name="search" class="search-input" 
                       placeholder="Search by name, email, or TARUMT ID..." 
                       value="{{ request('search') }}"
                       aria-label="Search users">
                @if(request('search'))
                    <a href="{{ route('admin.users.index') }}" class="clear-search" title="Clear search (Esc)">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
            
            <!-- Role Filter - HCI: Recognition over Recall -->
            <div class="filter-pills">
                <a href="{{ route('admin.users.index') }}" 
                   class="filter-pill {{ !request('role') ? 'active' : '' }}">
                    All
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'student']) }}" 
                   class="filter-pill {{ request('role') == 'student' ? 'active' : '' }}">
                    <i class="bi bi-mortarboard"></i> Students
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'lecturer']) }}" 
                   class="filter-pill {{ request('role') == 'lecturer' ? 'active' : '' }}">
                    <i class="bi bi-briefcase"></i> Lecturers
                </a>
            </div>
            
            <button type="submit" class="btn-search">
                <i class="bi bi-search"></i> Search
            </button>
        </form>
    </div>

    <!-- Results Summary - HCI: Visibility of System Status -->
    @if(request('search'))
    <div class="results-summary mb-3">
        <i class="bi bi-info-circle"></i>
        Showing <strong>{{ $users->count() }}</strong> results for "<strong>{{ request('search') }}</strong>"
        <a href="{{ route('admin.users.index') }}" class="ms-2">Clear filter</a>
    </div>
    @endif

    <!-- User Table - HCI: Consistency, Aesthetic Design -->
    <div class="data-card">
        <div class="table-responsive">
            <table class="table data-table" role="grid" aria-label="User management table">
                <thead>
                    <tr>
                        <th scope="col" style="width: 50px;">#</th>
                        <th scope="col">User Profile</th>
                        <th scope="col">Role</th>
                        <th scope="col">Credits</th>
                        <th scope="col">Status</th>
                        <th scope="col">Joined</th>
                        <th scope="col" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="user-row" tabindex="0" role="row">
                        <td class="row-number">
                            {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                        </td>
                        
                        <!-- User Profile - HCI: Recognition -->
                        <td>
                            <div class="user-profile">
                                <div class="user-avatar {{ $user->role }}">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="user-info">
                                    <span class="user-name">{{ $user->name }}</span>
                                    <span class="user-email">{{ $user->email }}</span>
                                    <span class="user-id">
                                        <i class="bi bi-credit-card-2-front"></i>
                                        {{ $user->tarumt_id ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Role Badge - HCI: Mapping (color = meaning) -->
                        <td>
                            <span class="role-badge {{ $user->role }}">
                                @if($user->role == 'admin')
                                    <i class="bi bi-shield-fill"></i>
                                @elseif($user->role == 'lecturer')
                                    <i class="bi bi-briefcase-fill"></i>
                                @else
                                    <i class="bi bi-mortarboard-fill"></i>
                                @endif
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>

                        <!-- Credits - HCI: Visibility -->
                        <td>
                            <div class="credits-display">
                                <i class="bi bi-coin"></i>
                                <span class="credits-value">{{ $user->credits }}</span>
                            </div>
                        </td>

                        <!-- Status - HCI: Feedback (clear icons) -->
                        <td>
                            @if($user->created_at->isToday())
                                <span class="status-badge pending" title="Account created today, awaiting verification">
                                    <i class="bi bi-clock-fill"></i> Pending
                                </span>
                            @else
                                <span class="status-badge verified" title="Account verified and active">
                                    <i class="bi bi-patch-check-fill"></i> Verified
                                </span>
                            @endif
                        </td>

                        <!-- Joined Date -->
                        <td>
                            <span class="join-date" title="{{ $user->created_at->format('F d, Y H:i') }}">
                                {{ $user->created_at->format('d M Y') }}
                            </span>
                        </td>

                        <!-- Actions - HCI: Affordance, Error Prevention -->
                        <td>
                            <div class="action-buttons">
                                <!-- Edit - Primary Action -->
                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                   class="action-btn edit" 
                                   title="Edit user details"
                                   aria-label="Edit {{ $user->name }}">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>

                                <!-- Reset Credits -->
                                <form action="{{ route('admin.users.credits', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" 
                                            class="action-btn reset" 
                                            title="Reset credits to 10"
                                            aria-label="Reset credits for {{ $user->name }}"
                                            onclick="return confirm('Reset credits to 10 for {{ $user->name }}?');">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </form>

                                <!-- Delete - Destructive Action (Error Prevention) -->
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="action-btn delete" 
                                            title="Delete user permanently"
                                            aria-label="Delete {{ $user->name }}"
                                            onclick="return confirm('⚠️ DELETE USER\n\nAre you sure you want to permanently delete {{ $user->name }}?\n\nThis action CANNOT be undone.');">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="bi bi-people"></i>
                                </div>
                                <h4>No Users Found</h4>
                                <p>Try adjusting your search or filter criteria.</p>
                                <a href="{{ route('admin.users.index') }}" class="btn-reset-search">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset Filters
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination - HCI: User Control -->
        @if($users->hasPages())
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} users
            </div>
            {{ $users->withQueryString()->links() }}
        </div>
        @endif
    </div>

    <!-- Keyboard Shortcuts Help - HCI: Help & Documentation -->
    <div class="keyboard-help">
        <i class="bi bi-keyboard"></i>
        <span>Tip: Use <kbd>/</kbd> to focus search</span>
    </div>
</div>

<style>
/* ========================================
   HCI DESIGN SYSTEM
   Based on Nielsen's 10 Usability Heuristics
   ======================================== */

/* Page Header */
.page-header {
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 1.5rem;
}

.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.025em;
}

.page-subtitle {
    font-size: 0.95rem;
}

/* Stat Pill - HCI: Visibility of System Status */
.stat-pill {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0.5rem 1rem;
    background: #f1f5f9;
    border-radius: 12px;
}

.stat-count {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
}

.stat-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
}

/* Add User Button - HCI: Affordance */
.btn-add-user {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    transition: all 0.2s ease;
}

.btn-add-user:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    color: white;
}

/* Search Card - HCI: User Control */
.search-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 1rem 1.5rem;
}

.search-form {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.search-input-wrapper {
    position: relative;
    flex: 1;
    min-width: 250px;
}

.search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

.search-input {
    width: 100%;
    padding: 0.75rem 2.5rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.95rem;
    transition: all 0.2s ease;
}

.search-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.clear-search {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    padding: 0.25rem;
}

.clear-search:hover {
    color: #ef4444;
}

/* Filter Pills - HCI: Recognition over Recall */
.filter-pills {
    display: flex;
    gap: 0.5rem;
}

.filter-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.5rem 1rem;
    background: #f1f5f9;
    color: #64748b;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
}

.filter-pill:hover {
    background: #e2e8f0;
    color: #475569;
}

.filter-pill.active {
    background: #1e293b;
    color: white;
}

.btn-search {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    background: #1e293b;
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-search:hover {
    background: #0f172a;
}

/* Results Summary */
.results-summary {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    color: #1e40af;
    font-size: 0.875rem;
}

/* Data Card */
.data-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
}

/* Data Table - HCI: Consistency */
.data-table {
    margin: 0;
    font-size: 0.9375rem;
}

.data-table thead {
    background: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
}

.data-table thead th {
    padding: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    border-bottom: none;
}

.data-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background-color 0.15s ease;
}

.data-table tbody tr:hover {
    background: #f8fafc;
}

.data-table tbody tr:focus {
    outline: 2px solid #3b82f6;
    outline-offset: -2px;
}

.data-table tbody td {
    padding: 1rem;
    vertical-align: middle;
}

.row-number {
    font-weight: 600;
    color: #94a3b8;
}

/* User Profile Cell */
.user-profile {
    display: flex;
    align-items: center;
    gap: 0.875rem;
}

.user-avatar {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.125rem;
    color: white;
}

.user-avatar.student {
    background: linear-gradient(135deg, #a855f7 0%, #7c3aed 100%);
}

.user-avatar.lecturer {
    background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
}

.user-avatar.admin {
    background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
}

.user-info {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
}

.user-name {
    font-weight: 600;
    color: #0f172a;
}

.user-email {
    font-size: 0.8rem;
    color: #64748b;
}

.user-id {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #3b82f6;
}

/* Role Badge - HCI: Mapping (color = meaning) */
.role-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.875rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.role-badge.student {
    background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
    color: #7c3aed;
}

.role-badge.lecturer {
    background: linear-gradient(135deg, #ccfbf1 0%, #99f6e4 100%);
    color: #0d9488;
}

.role-badge.admin {
    background: linear-gradient(135deg, #ffe4e6 0%, #fecdd3 100%);
    color: #e11d48;
}

/* Credits Display */
.credits-display {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.25rem 0.75rem;
    background: #fef3c7;
    border-radius: 8px;
}

.credits-display i {
    color: #f59e0b;
}

.credits-value {
    font-weight: 700;
    color: #92400e;
}

/* Status Badge - HCI: Feedback */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.8rem;
    font-weight: 600;
}

.status-badge.pending {
    color: #d97706;
}

.status-badge.verified {
    color: #059669;
}

/* Join Date */
.join-date {
    color: #64748b;
    font-size: 0.875rem;
    cursor: help;
}

/* Action Buttons - HCI: Affordance, Error Prevention */
.action-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    background: white;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
}

.action-btn:hover {
    transform: translateY(-2px);
}

.action-btn.edit:hover {
    border-color: #3b82f6;
    color: #3b82f6;
    background: #eff6ff;
}

.action-btn.reset:hover {
    border-color: #8b5cf6;
    color: #8b5cf6;
    background: #f5f3ff;
}

.action-btn.delete {
    border-color: #fecaca;
    color: #ef4444;
}

.action-btn.delete:hover {
    border-color: #ef4444;
    background: #fef2f2;
}

/* Empty State */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 4rem 2rem;
    text-align: center;
}

.empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #94a3b8;
    margin-bottom: 1rem;
}

.empty-state h4 {
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #64748b;
    margin-bottom: 1rem;
}

.btn-reset-search {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #f1f5f9;
    color: #475569;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
}

.btn-reset-search:hover {
    background: #e2e8f0;
}

/* Pagination */
.pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-top: 1px solid #e2e8f0;
    background: #f8fafc;
}

.pagination-info {
    font-size: 0.875rem;
    color: #64748b;
}

/* Keyboard Help - HCI: Help & Documentation */
.keyboard-help {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1rem;
    color: #94a3b8;
    font-size: 0.8rem;
}

.keyboard-help kbd {
    padding: 0.125rem 0.5rem;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    font-family: monospace;
}

/* Responsive */
@media (max-width: 768px) {
    .search-form {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-pills {
        overflow-x: auto;
        padding-bottom: 0.5rem;
    }
    
    .stat-pill {
        display: none;
    }
}
</style>

<script>
// HCI: Keyboard Shortcuts for Power Users
document.addEventListener('keydown', function(e) {
    // Press "/" to focus search
    if (e.key === '/' && document.activeElement.tagName !== 'INPUT') {
        e.preventDefault();
        document.querySelector('.search-input').focus();
    }
    
    // Press Escape to clear search focus
    if (e.key === 'Escape') {
        document.querySelector('.search-input').blur();
    }
});

// HCI: Keyboard navigation for table rows
document.querySelectorAll('.user-row').forEach(row => {
    row.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            // Navigate to edit page on Enter
            const editBtn = row.querySelector('.action-btn.edit');
            if (editBtn) editBtn.click();
        }
    });
});
</script>
@endsection