@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1 class="page-title mb-1">Asset Management</h1>
                <p class="page-subtitle text-muted mb-0">Track and manage facility equipment and resources</p>
            </div>
            
            <div class="d-flex gap-3 align-items-center">
                <!-- Quick Stats -->
                <div class="stat-pills">
                    <div class="stat-pill good">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>{{ $assets->where('condition', 'Good')->count() }} Good</span>
                    </div>
                    <div class="stat-pill warning">
                        <i class="bi bi-tools"></i>
                        <span>{{ $assets->whereIn('condition', ['Maintenance', 'Damaged'])->count() }} Issues</span>
                    </div>
                </div>
                
                <a href="{{ route('admin.assets.create') }}" class="btn-add-asset">
                    <i class="bi bi-plus-lg"></i>
                    <span>Add New Asset</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="search-card mb-4">
        <form action="{{ route('admin.assets.manage') }}" method="GET" class="search-form">
            <div class="search-input-wrapper">
                <i class="bi bi-search search-icon"></i>
                <input type="text" name="search" class="search-input" 
                       placeholder="Search by name, serial number, or facility..." 
                       value="{{ request('search') }}"
                       aria-label="Search assets">
            </div>
            
            <!-- Condition Filter Pills -->
            <div class="filter-pills">
                <a href="{{ route('admin.assets.manage') }}" 
                   class="filter-pill {{ !request('condition') ? 'active' : '' }}">
                    All
                </a>
                <a href="{{ route('admin.assets.manage', ['condition' => 'Good']) }}" 
                   class="filter-pill good {{ request('condition') == 'Good' ? 'active' : '' }}">
                    <i class="bi bi-check-circle"></i> Good
                </a>
                <a href="{{ route('admin.assets.manage', ['condition' => 'Maintenance']) }}" 
                   class="filter-pill warning {{ request('condition') == 'Maintenance' ? 'active' : '' }}">
                    <i class="bi bi-tools"></i> Maintenance
                </a>
                <a href="{{ route('admin.assets.manage', ['condition' => 'Damaged']) }}" 
                   class="filter-pill danger {{ request('condition') == 'Damaged' ? 'active' : '' }}">
                    <i class="bi bi-exclamation-triangle"></i> Damaged
                </a>
            </div>
            
            <button type="submit" class="btn-search">
                <i class="bi bi-search"></i> Search
            </button>
        </form>
    </div>

    <!-- Assets Table -->
    <div class="data-card">
        <div class="table-responsive">
            <table class="table data-table" role="grid" aria-label="Assets management table">
                <thead>
                    <tr>
                        <th scope="col" style="width: 60px;">#</th>
                        <th scope="col">Asset</th>
                        <th scope="col">Facility</th>
                        <th scope="col">Type</th>
                        <th scope="col">Serial Number</th>
                        <th scope="col">Condition</th>
                        <th scope="col" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                    <tr class="asset-row">
                        <td class="row-number">{{ $asset->id }}</td>
                        
                        <!-- Asset Name with Icon -->
                        <td>
                            <div class="asset-info">
                                <div class="asset-icon {{ strtolower($asset->type) }}">
                                    @if($asset->type == 'Equipment')
                                        <i class="bi bi-projector"></i>
                                    @elseif($asset->type == 'Furniture')
                                        <i class="bi bi-lamp"></i>
                                    @elseif($asset->type == 'Electronics')
                                        <i class="bi bi-display"></i>
                                    @else
                                        <i class="bi bi-box"></i>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('admin.assets.show', $asset->id) }}" class="asset-name">
                                        {{ $asset->name }}
                                    </a>
                                </div>
                            </div>
                        </td>
                        
                        <!-- Facility -->
                        <td>
                            <span class="facility-badge">
                                <i class="bi bi-building"></i>
                                {{ $asset->facility->name ?? 'Unassigned' }}
                            </span>
                        </td>
                        
                        <!-- Type -->
                        <td>
                            <span class="type-label">{{ $asset->type }}</span>
                        </td>
                        
                        <!-- Serial Number -->
                        <td>
                            <code class="serial-number">{{ $asset->serial_number }}</code>
                        </td>
                        
                        <!-- Condition Badge -->
                        <td>
                            @php
                                $conditionClass = match($asset->condition) {
                                    'Good' => 'good',
                                    'Fair' => 'fair',
                                    'Damaged', 'Retired' => 'damaged',
                                    'Maintenance' => 'maintenance',
                                    default => 'unknown'
                                };
                                $conditionIcon = match($asset->condition) {
                                    'Good' => 'bi-check-circle-fill',
                                    'Fair' => 'bi-dash-circle-fill',
                                    'Damaged' => 'bi-x-circle-fill',
                                    'Maintenance' => 'bi-tools',
                                    'Retired' => 'bi-archive-fill',
                                    default => 'bi-question-circle'
                                };
                            @endphp
                            <span class="condition-badge {{ $conditionClass }}">
                                <i class="bi {{ $conditionIcon }}"></i>
                                {{ $asset->condition }}
                            </span>
                        </td>
                        
                        <!-- Actions -->
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.assets.show', $asset->id) }}" 
                                   class="action-btn view" 
                                   title="View asset history">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                
                                <a href="{{ route('admin.assets.edit', $asset->id) }}" 
                                   class="action-btn edit" 
                                   title="Edit asset">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                
                                <form action="{{ route('admin.assets.destroy', $asset->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="action-btn delete" 
                                            title="Delete asset"
                                            onclick="return confirm('⚠️ DELETE ASSET\n\nAre you sure you want to delete {{ $asset->name }}?\n\nThis action CANNOT be undone.');">
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
                                    <i class="bi bi-box-seam"></i>
                                </div>
                                <h4>No Assets Found</h4>
                                <p>Add your first asset to get started.</p>
                                <a href="{{ route('admin.assets.create') }}" class="btn-add-first">
                                    <i class="bi bi-plus-lg"></i> Add New Asset
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($assets->hasPages())
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Showing {{ $assets->firstItem() }}–{{ $assets->lastItem() }} of {{ $assets->total() }} assets
            </div>
            {{ $assets->withQueryString()->links() }}
        </div>
        @endif
    </div>

    <!-- Quick Actions Footer -->
    <div class="quick-actions">
        <a href="{{ route('admin.assets.report') }}" class="quick-action-btn" target="_blank">
            <i class="bi bi-file-earmark-pdf"></i>
            <span>Export Maintenance Report</span>
        </a>
    </div>
</div>

<style>
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

/* Stat Pills */
.stat-pills {
    display: flex;
    gap: 0.75rem;
}

.stat-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}

.stat-pill.good {
    background: #d1fae5;
    color: #059669;
}

.stat-pill.warning {
    background: #fef3c7;
    color: #d97706;
}

/* Add Button */
.btn-add-asset {
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

.btn-add-asset:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    color: white;
}

/* Search Card */
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

/* Filter Pills */
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

.filter-pill.good.active {
    background: #059669;
}

.filter-pill.warning.active {
    background: #d97706;
}

.filter-pill.danger.active {
    background: #dc2626;
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

/* Data Card & Table */
.data-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
}

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

.data-table tbody td {
    padding: 1rem;
    vertical-align: middle;
}

.row-number {
    font-weight: 600;
    color: #94a3b8;
}

/* Asset Info */
.asset-info {
    display: flex;
    align-items: center;
    gap: 0.875rem;
}

.asset-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}

.asset-icon.equipment {
    background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
}

.asset-icon.furniture {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.asset-icon.electronics {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.asset-icon {
    background: linear-gradient(135deg, #64748b 0%, #475569 100%);
}

.asset-name {
    font-weight: 600;
    color: #0f172a;
    text-decoration: none;
    transition: color 0.2s ease;
}

.asset-name:hover {
    color: #3b82f6;
}

/* Facility Badge */
.facility-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.875rem;
    color: #64748b;
}

.facility-badge i {
    color: #94a3b8;
}

/* Type Label */
.type-label {
    font-size: 0.875rem;
    color: #64748b;
}

/* Serial Number */
.serial-number {
    background: #f1f5f9;
    color: #475569;
    padding: 0.25rem 0.625rem;
    border-radius: 6px;
    font-size: 0.8rem;
    font-family: 'SF Mono', 'Consolas', monospace;
}

/* Condition Badge */
.condition-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.875rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.condition-badge.good {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #059669;
}

.condition-badge.fair {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #2563eb;
}

.condition-badge.maintenance {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #d97706;
}

.condition-badge.damaged {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #dc2626;
}

/* Action Buttons */
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
    text-decoration: none;
}

.action-btn:hover {
    transform: translateY(-2px);
}

.action-btn.view:hover {
    border-color: #14b8a6;
    color: #14b8a6;
    background: #f0fdfa;
}

.action-btn.edit:hover {
    border-color: #3b82f6;
    color: #3b82f6;
    background: #eff6ff;
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

.btn-add-first {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: #3b82f6;
    color: white;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
}

.btn-add-first:hover {
    background: #1d4ed8;
    color: white;
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

/* Quick Actions */
.quick-actions {
    display: flex;
    justify-content: center;
    margin-top: 1.5rem;
}

.quick-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    color: #64748b;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
}

.quick-action-btn:hover {
    background: #f1f5f9;
    color: #334155;
    border-color: #cbd5e1;
}

.quick-action-btn i {
    color: #ef4444;
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
    
    .stat-pills {
        display: none;
    }
}
</style>
@endsection