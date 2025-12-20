@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1 class="page-title mb-1">Facility Management</h1>
                <p class="page-subtitle text-muted mb-0">Manage rooms, labs, and halls across all buildings</p>
            </div>
            
            <div class="d-flex gap-3 align-items-center">
                <!-- Quick Stats -->
                <div class="stat-pills">
                    <div class="stat-pill available">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>{{ $facilities->where('status', 'Available')->count() }} Available</span>
                    </div>
                    <div class="stat-pill maintenance">
                        <i class="bi bi-tools"></i>
                        <span>{{ $facilities->where('status', 'Maintenance')->count() }} Maintenance</span>
                    </div>
                </div>
                
                <a href="{{ route('admin.facilities.create') }}" class="btn-add">
                    <i class="bi bi-plus-lg"></i>
                    <span>Add New Facility</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert-card success mb-4">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close-alert" onclick="this.parentElement.remove()">
                <i class="bi bi-x"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-card error mb-4">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close-alert" onclick="this.parentElement.remove()">
                <i class="bi bi-x"></i>
            </button>
        </div>
    @endif

    <!-- Facilities Table -->
    <div class="data-card">
        <div class="table-responsive">
            <table class="table data-table">
                <thead>
                    <tr>
                        <th scope="col" style="width: 60px;">#</th>
                        <th scope="col">Facility</th>
                        <th scope="col">Building</th>
                        <th scope="col">Type</th>
                        <th scope="col">Capacity</th>
                        <th scope="col">Hours</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($facilities as $facility)
                    <tr>
                        <td class="row-number">{{ $facility->id }}</td>
                        
                        <!-- Facility with Image -->
                        <td>
                            <div class="facility-info">
                                <div class="facility-image">
                                    @if($facility->image_path)
                                        <img src="{{ asset('storage/' . $facility->image_path) }}" alt="{{ $facility->name }}">
                                    @else
                                        <div class="no-image">
                                            <i class="bi bi-building"></i>
                                        </div>
                                    @endif
                                </div>
                                <span class="facility-name">{{ $facility->name }}</span>
                            </div>
                        </td>
                        
                        <!-- Building -->
                        <td>
                            <span class="building-badge">
                                <i class="bi bi-geo-alt-fill"></i>
                                {{ $facility->building->name ?? 'N/A' }}
                            </span>
                        </td>
                        
                        <!-- Type -->
                        <td>
                            <span class="type-badge {{ strtolower($facility->type) }}">
                                @if($facility->type == 'Lab')
                                    <i class="bi bi-pc-display"></i>
                                @elseif($facility->type == 'Room')
                                    <i class="bi bi-door-open"></i>
                                @elseif($facility->type == 'Hall')
                                    <i class="bi bi-building"></i>
                                @else
                                    <i class="bi bi-grid"></i>
                                @endif
                                {{ $facility->type }}
                            </span>
                        </td>
                        
                        <!-- Capacity -->
                        <td>
                            <div class="capacity-display">
                                <i class="bi bi-people-fill"></i>
                                <span>{{ $facility->capacity }}</span>
                            </div>
                        </td>
                        
                        <!-- Hours -->
                        <td>
                            <span class="hours-display">
                                <i class="bi bi-clock"></i>
                                {{ \Carbon\Carbon::parse($facility->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($facility->end_time)->format('H:i') }}
                            </span>
                        </td>
                        
                        <!-- Status -->
                        <td>
                            @php
                                $statusClass = match($facility->status) {
                                    'Available' => 'available',
                                    'Maintenance' => 'maintenance',
                                    'Closed' => 'closed',
                                    default => 'unknown'
                                };
                                $statusIcon = match($facility->status) {
                                    'Available' => 'bi-check-circle-fill',
                                    'Maintenance' => 'bi-tools',
                                    'Closed' => 'bi-x-circle-fill',
                                    default => 'bi-question-circle'
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">
                                <i class="bi {{ $statusIcon }}"></i>
                                {{ $facility->status }}
                            </span>
                        </td>
                        
                        <!-- Actions -->
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.facilities.edit', $facility->id) }}" 
                                   class="action-btn edit" 
                                   title="Edit facility">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                
                                <form action="{{ route('admin.facilities.destroy', $facility->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="action-btn delete" 
                                            title="Delete facility"
                                            onclick="return confirm('⚠️ DELETE FACILITY\n\nAre you sure you want to delete {{ $facility->name }}?\n\nThis will also delete all associated bookings.');">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="bi bi-building"></i>
                                </div>
                                <h4>No Facilities Found</h4>
                                <p>Add your first facility to get started.</p>
                                <a href="{{ route('admin.facilities.create') }}" class="btn-add-first">
                                    <i class="bi bi-plus-lg"></i> Add New Facility
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($facilities->hasPages())
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Showing {{ $facilities->firstItem() }}–{{ $facilities->lastItem() }} of {{ $facilities->total() }} facilities
            </div>
            {{ $facilities->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<style>
/* Page Header */
.page-header { border-bottom: 1px solid #e2e8f0; padding-bottom: 1.5rem; }
.page-title { font-size: 1.75rem; font-weight: 700; color: #0f172a; }
.page-subtitle { font-size: 0.95rem; }

/* Stat Pills */
.stat-pills { display: flex; gap: 0.75rem; }
.stat-pill { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.875rem; font-weight: 600; }
.stat-pill.available { background: #d1fae5; color: #059669; }
.stat-pill.maintenance { background: #fef3c7; color: #d97706; }

/* Add Button */
.btn-add { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; border-radius: 12px; font-weight: 600; text-decoration: none; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); transition: all 0.2s ease; }
.btn-add:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4); color: white; }

/* Alert Card */
.alert-card { display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.25rem; border-radius: 12px; }
.alert-card.success { background: #d1fae5; border: 1px solid #a7f3d0; color: #059669; }
.alert-card.error { background: #fee2e2; border: 1px solid #fecaca; color: #dc2626; }
.btn-close-alert { background: none; border: none; color: inherit; opacity: 0.7; cursor: pointer; margin-left: auto; }

/* Data Card & Table */
.data-card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
.data-table { margin: 0; font-size: 0.9375rem; }
.data-table thead { background: #f8fafc; border-bottom: 2px solid #e2e8f0; }
.data-table thead th { padding: 1rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; border-bottom: none; }
.data-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background-color 0.15s ease; }
.data-table tbody tr:hover { background: #f8fafc; }
.data-table tbody td { padding: 1rem; vertical-align: middle; }
.row-number { font-weight: 600; color: #94a3b8; }

/* Facility Info */
.facility-info { display: flex; align-items: center; gap: 0.875rem; }
.facility-image { width: 50px; height: 50px; border-radius: 10px; overflow: hidden; flex-shrink: 0; }
.facility-image img { width: 100%; height: 100%; object-fit: cover; }
.facility-image .no-image { width: 100%; height: 100%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 1.25rem; }
.facility-name { font-weight: 600; color: #0f172a; }

/* Building Badge */
.building-badge { display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.875rem; color: #64748b; }
.building-badge i { color: #94a3b8; }

/* Type Badge */
.type-badge { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; border-radius: 8px; font-size: 0.8rem; font-weight: 500; background: #f1f5f9; color: #475569; }
.type-badge.lab { background: #dbeafe; color: #1d4ed8; }
.type-badge.room { background: #fce7f3; color: #be185d; }
.type-badge.hall { background: #f3e8ff; color: #7c3aed; }

/* Capacity Display */
.capacity-display { display: inline-flex; align-items: center; gap: 0.375rem; font-weight: 600; color: #475569; }
.capacity-display i { color: #94a3b8; }

/* Hours Display */
.hours-display { display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.875rem; color: #64748b; }
.hours-display i { color: #94a3b8; }

/* Status Badge */
.status-badge { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.875rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
.status-badge.available { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #059669; }
.status-badge.maintenance { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #d97706; }
.status-badge.closed { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #dc2626; }

/* Action Buttons */
.action-buttons { display: flex; gap: 0.5rem; justify-content: flex-end; }
.action-btn { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border: 2px solid #e2e8f0; border-radius: 10px; background: white; color: #64748b; cursor: pointer; transition: all 0.2s ease; text-decoration: none; }
.action-btn:hover { transform: translateY(-2px); }
.action-btn.edit:hover { border-color: #3b82f6; color: #3b82f6; background: #eff6ff; }
.action-btn.delete { border-color: #fecaca; color: #ef4444; }
.action-btn.delete:hover { border-color: #ef4444; background: #fef2f2; }

/* Empty State */
.empty-state { display: flex; flex-direction: column; align-items: center; padding: 4rem 2rem; text-align: center; }
.empty-icon { width: 80px; height: 80px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #94a3b8; margin-bottom: 1rem; }
.empty-state h4 { font-weight: 600; color: #334155; margin-bottom: 0.5rem; }
.empty-state p { color: #64748b; margin-bottom: 1rem; }
.btn-add-first { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: #3b82f6; color: white; border-radius: 12px; text-decoration: none; font-weight: 600; }

/* Pagination */
.pagination-wrapper { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0; background: #f8fafc; }
.pagination-info { font-size: 0.875rem; color: #64748b; }
</style>
@endsection