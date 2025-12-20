@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1 class="page-title mb-1">
                    <i class="bi bi-buildings header-icon"></i>
                    Building Management
                </h1>
                <p class="page-subtitle text-muted mb-0">Manage campus buildings and their locations</p>
            </div>
            
            <div class="d-flex gap-3 align-items-center">
                <!-- Quick Stats -->
                <div class="stat-pill">
                    <i class="bi bi-building-fill"></i>
                    <span>{{ count($buildings) }} Buildings</span>
                </div>
                
                <a href="{{ route('admin.buildings.create') }}" class="btn-add">
                    <i class="bi bi-plus-lg"></i>
                    <span>Add New Building</span>
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

    <!-- Buildings Table -->
    <div class="data-card">
        <div class="table-responsive">
            <table class="table data-table">
                <thead>
                    <tr>
                        <th scope="col" style="width: 60px;">#</th>
                        <th scope="col">Building</th>
                        <th scope="col">Location</th>
                        <th scope="col">Facilities</th>
                        <th scope="col" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($buildings as $building)
                    <tr>
                        <td class="row-number">{{ $building->id }}</td>
                        
                        <!-- Building with Image -->
                        <td>
                            <div class="building-info">
                                <div class="building-image">
                                    @if($building->image_path)
                                        <img src="{{ asset('storage/' . $building->image_path) }}" alt="{{ $building->name }}">
                                    @else
                                        <div class="no-image">
                                            <i class="bi bi-building"></i>
                                        </div>
                                    @endif
                                </div>
                                <span class="building-name">{{ $building->name }}</span>
                            </div>
                        </td>
                        
                        <!-- Location -->
                        <td>
                            <span class="location-badge">
                                <i class="bi bi-geo-alt-fill"></i>
                                {{ $building->location }}
                            </span>
                        </td>
                        
                        <!-- Facilities Count -->
                        <td>
                            <div class="facility-count">
                                <i class="bi bi-door-open-fill"></i>
                                <span>{{ $building->facilities->count() ?? 0 }} Facilities</span>
                            </div>
                        </td>
                        
                        <!-- Actions -->
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.buildings.edit', $building->id) }}" 
                                   class="action-btn edit" 
                                   title="Edit building">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                
                                <form action="{{ route('admin.buildings.destroy', $building->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="action-btn delete" 
                                            title="Delete building"
                                            onclick="return confirm('⚠️ DELETE BUILDING\n\nAre you sure you want to delete {{ $building->name }}?\n\nThis will also delete all associated facilities!');">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="bi bi-buildings"></i>
                                </div>
                                <h4>No Buildings Found</h4>
                                <p>Add your first building to get started.</p>
                                <a href="{{ route('admin.buildings.create') }}" class="btn-add-first">
                                    <i class="bi bi-plus-lg"></i> Add New Building
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
/* ========================================
   BUILDING MANAGEMENT - PURPLE/VIOLET THEME
   ======================================== */

/* Page Header */
.page-header { border-bottom: 1px solid #e2e8f0; padding-bottom: 1.5rem; }
.page-title { font-size: 1.75rem; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 0.75rem; }
.header-icon { color: #7c3aed; }
.page-subtitle { font-size: 0.95rem; }

/* Stat Pill - PURPLE */
.stat-pill { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.875rem; font-weight: 600; background: #ede9fe; color: #6d28d9; }

/* Add Button - PURPLE Gradient */
.btn-add { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: white; border-radius: 12px; font-weight: 600; text-decoration: none; box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3); transition: all 0.2s ease; }
.btn-add:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4); color: white; }

/* Alert Card */
.alert-card { display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.25rem; border-radius: 12px; }
.alert-card.success { background: #d1fae5; border: 1px solid #a7f3d0; color: #059669; }
.alert-card.error { background: #fee2e2; border: 1px solid #fecaca; color: #dc2626; }
.btn-close-alert { background: none; border: none; color: inherit; opacity: 0.7; cursor: pointer; margin-left: auto; }

/* Data Card & Table */
.data-card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
.data-table { margin: 0; font-size: 0.9375rem; }
.data-table thead { background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%); border-bottom: 2px solid #ddd6fe; }
.data-table thead th { padding: 1rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #6d28d9; border-bottom: none; }
.data-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background-color 0.15s ease; }
.data-table tbody tr:hover { background: #faf5ff; }
.data-table tbody td { padding: 1rem; vertical-align: middle; }
.row-number { font-weight: 600; color: #a78bfa; }

/* Building Info */
.building-info { display: flex; align-items: center; gap: 0.875rem; }
.building-image { width: 50px; height: 50px; border-radius: 10px; overflow: hidden; flex-shrink: 0; }
.building-image img { width: 100%; height: 100%; object-fit: cover; }
.building-image .no-image { width: 100%; height: 100%; background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); display: flex; align-items: center; justify-content: center; color: #8b5cf6; font-size: 1.25rem; }
.building-name { font-weight: 600; color: #0f172a; }

/* Location Badge */
.location-badge { display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.875rem; color: #64748b; }
.location-badge i { color: #8b5cf6; }

/* Facility Count */
.facility-count { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; background: #f3e8ff; border-radius: 8px; font-size: 0.85rem; font-weight: 500; color: #7c3aed; }

/* Action Buttons */
.action-buttons { display: flex; gap: 0.5rem; justify-content: flex-end; }
.action-btn { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border: 2px solid #e2e8f0; border-radius: 10px; background: white; color: #64748b; cursor: pointer; transition: all 0.2s ease; text-decoration: none; }
.action-btn:hover { transform: translateY(-2px); }
.action-btn.edit:hover { border-color: #8b5cf6; color: #8b5cf6; background: #f5f3ff; }
.action-btn.delete { border-color: #fecaca; color: #ef4444; }
.action-btn.delete:hover { border-color: #ef4444; background: #fef2f2; }

/* Empty State */
.empty-state { display: flex; flex-direction: column; align-items: center; padding: 4rem 2rem; text-align: center; }
.empty-icon { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #8b5cf6; margin-bottom: 1rem; }
.empty-state h4 { font-weight: 600; color: #334155; margin-bottom: 0.5rem; }
.empty-state p { color: #64748b; margin-bottom: 1rem; }
.btn-add-first { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: white; border-radius: 12px; text-decoration: none; font-weight: 600; }
</style>
@endsection