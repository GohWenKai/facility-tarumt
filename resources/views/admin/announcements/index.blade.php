@extends('layouts.admin')

@section('content')
<style>
    .page-header { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-radius: 16px; padding: 2rem; margin-bottom: 2rem; color: white; }
    .page-header h1 { font-size: 1.75rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.75rem; }
    .page-header p { opacity: 0.9; margin: 0.5rem 0 0 0; }
    .btn-create { background: white; color: #1e40af; padding: 0.75rem 1.5rem; border-radius: 10px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
    .btn-create:hover { background: #f0f9ff; color: #1e40af; }
    .announcement-card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 1rem; overflow: hidden; }
    .announcement-header { padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; }
    .announcement-body { padding: 1.5rem; }
    .badge-type { padding: 0.375rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .badge-info { background: #dbeafe; color: #1e40af; }
    .badge-warning { background: #fef3c7; color: #92400e; }
    .badge-success { background: #d1fae5; color: #047857; }
    .badge-danger { background: #fee2e2; color: #b91c1c; }
    .badge-priority { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; }
    .badge-high { background: #dc2626; color: white; }
    .badge-medium { background: #f59e0b; color: white; }
    .badge-low { background: #6b7280; color: white; }
    .status-active { color: #10b981; }
    .status-inactive { color: #ef4444; }
    .action-btn { padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.8rem; text-decoration: none; margin-right: 0.5rem; }
    .btn-edit { background: #dbeafe; color: #1e40af; }
    .btn-toggle { background: #fef3c7; color: #92400e; }
    .btn-delete { background: #fee2e2; color: #b91c1c; border: none; cursor: pointer; }
    .meta-info { font-size: 0.8rem; color: #64748b; margin-top: 0.5rem; }
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="bi bi-megaphone-fill"></i> Admin Announcements</h1>
                <p>Create and manage system-wide announcements</p>
            </div>
            <a href="{{ route('admin.announcements.create') }}" class="btn-create">
                <i class="bi bi-plus-lg"></i> New Announcement
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Announcements List -->
    @forelse($announcements as $announcement)
        <div class="announcement-card">
            <div class="announcement-header">
                <div class="d-flex align-items-center gap-3">
                    <span class="badge-type badge-{{ $announcement->type }}">
                        @if($announcement->type == 'info') <i class="bi bi-info-circle"></i>
                        @elseif($announcement->type == 'warning') <i class="bi bi-exclamation-triangle"></i>
                        @elseif($announcement->type == 'success') <i class="bi bi-check-circle"></i>
                        @else <i class="bi bi-x-circle"></i>
                        @endif
                        {{ ucfirst($announcement->type) }}
                    </span>
                    <span class="badge-priority badge-{{ $announcement->priority }}">{{ $announcement->priority }}</span>
                    @if($announcement->is_active)
                        <span class="status-active"><i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Active</span>
                    @else
                        <span class="status-inactive"><i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Inactive</span>
                    @endif
                </div>
                <div>
                    <a href="{{ route('admin.announcements.edit', $announcement) }}" class="action-btn btn-edit">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <form action="{{ route('admin.announcements.toggle', $announcement) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="action-btn btn-toggle">
                            <i class="bi bi-toggle-{{ $announcement->is_active ? 'on' : 'off' }}"></i>
                            {{ $announcement->is_active ? 'Disable' : 'Enable' }}
                        </button>
                    </form>
                    <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this announcement?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn btn-delete">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
            <div class="announcement-body">
                <h5 style="margin: 0 0 0.75rem 0; font-weight: 600;">{{ $announcement->title }}</h5>
                <p style="margin: 0; color: #475569;">{{ $announcement->content }}</p>
                <div class="meta-info">
                    <i class="bi bi-person"></i> Created by {{ $announcement->creator->name ?? 'N/A' }} |
                    <i class="bi bi-calendar"></i> {{ $announcement->created_at->format('M d, Y H:i') }}
                    @if($announcement->ends_at)
                        | <i class="bi bi-clock"></i> Expires: {{ $announcement->ends_at->format('M d, Y') }}
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="bi bi-megaphone" style="font-size: 3rem; color: #cbd5e1;"></i>
            <p class="text-muted mt-3">No announcements yet. Create your first one!</p>
        </div>
    @endforelse

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $announcements->links() }}
    </div>
</div>
@endsection
