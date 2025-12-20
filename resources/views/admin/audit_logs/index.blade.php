@extends('layouts.admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="container-fluid px-4 py-4">
    <!-- Professional Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="page-title mb-2">Security Audit Log</h1>
                <p class="page-subtitle text-muted mb-0">System activity monitoring and compliance tracking</p>
            </div>
            <div class="header-actions">
                <span class="record-count">
                    <span class="count-number">{{ $logs->total() }}</span>
                    <span class="count-label">Total Records</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card filter-section mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.audit_logs.index') }}">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="filter-label">Action Type</label>
                        <select name="action" class="form-select">
                            <option value="">All Actions</option>
                            <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Created</option>
                            <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Updated</option>
                            <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="filter-label">Model Type</label>
                        <select name="model_type" class="form-select">
                            <option value="">All Models</option>
                            @foreach($modelTypes as $type)
                            <option value="{{ $type }}" {{ request('model_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="filter-label">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="filter-label">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">Apply Filters</button>
                        <a href="{{ route('admin.audit_logs.index') }}" class="btn btn-outline-secondary">Reset</a>
                        <a href="{{ route('admin.audit_logs.export', request()->query()) }}" class="btn btn-dark" target="_blank">
                            <i class="bi bi-file-pdf"></i> Export
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card data-table-card">
        <div class="table-responsive">
            <table class="table data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th style="width: 15%;">User</th>
                        <th style="width: 10%;">Action</th>
                        <th style="width: 12%;">Model</th>
                        <th style="width: 8%;">Model ID</th>
                        <th style="width: 10%;">Details</th>
                        <th style="width: 15%;">IP Address</th>
                        <th style="width: 15%;">Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>
                            <span class="cell-id">{{ $log->id }}</span>
                        </td>
                        <td>
                            @if($log->user)
                                <div class="user-cell">
                                    <span class="user-name">{{ $log->user->name }}</span>
                                    <span class="user-role">{{ $log->user->role }}</span>
                                </div>
                            @else
                                <span class="system-label">System</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge status-{{ $log->action }}">
                                {{ ucfirst($log->action) }}
                            </span>
                        </td>
                        <td>
                            <code class="model-label">{{ class_basename($log->model_type) }}</code>
                        </td>
                        <td>
                            <span class="model-id">{{ $log->model_id }}</span>
                        </td>
                        <td>
                            <button class="btn-view-details" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#detailsModal{{ $log->id }}">
                                View Changes
                            </button>

                            @if($log->action === 'deleted' && $log->model_type === 'App\Models\Asset')
                                <button class="btn-undo ms-2" onclick="confirmUndo({{ $log->id }})">
                                    <i class="bi bi-arrow-counterclockwise"></i> Undo
                                </button>
                            @endif

                            {{-- Modal --}}
                            <div class="modal fade" id="detailsModal{{ $log->id }}" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <div>
                                                <h5 class="modal-title">Change Details</h5>
                                                <small class="text-muted">Audit Log #{{ $log->id }} • {{ $log->created_at->format('F d, Y H:i:s') }}</small>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="diff-container" id="diffContainer{{ $log->id }}">
                                                <div class="diff-viewer">
                                                    @php
                                                        $oldData = $log->old_values ?? [];
                                                        $newData = $log->new_values ?? [];
                                                        
                                                        // For UPDATE actions, show ALL fields from old record
                                                        // For CREATE/DELETE, show all fields from both
                                                        $allKeys = array_unique(array_merge(array_keys($oldData), array_keys($newData)));
                                                    @endphp

                                                    @foreach($allKeys as $key)
                                                        @php
                                                            $hasOldValue = array_key_exists($key, $oldData);
                                                            $hasNewValue = array_key_exists($key, $newData);
                                                            $oldValue = $oldData[$key] ?? null;
                                                            $newValue = $hasNewValue ? $newData[$key] : $oldValue; // If no new value, same as old
                                                            
                                                            $isChanged = $hasNewValue && ($oldValue !== $newValue);
                                                            $isAdded = !$hasOldValue && $hasNewValue;
                                                            $isRemoved = $hasOldValue && !$hasNewValue && $log->action === 'deleted';
                                                        @endphp

                                                        <div class="diff-row {{ $isChanged ? 'diff-changed' : '' }} {{ $isAdded ? 'diff-added' : '' }} {{ $isRemoved ? 'diff-removed' : '' }}">
                                                            <div class="diff-key">
                                                                @if($isAdded)
                                                                    <span class="diff-indicator added">+</span>
                                                                @elseif($isRemoved)
                                                                    <span class="diff-indicator removed">-</span>
                                                                @elseif($isChanged)
                                                                    <span class="diff-indicator changed">~</span>
                                                                @else
                                                                    <span class="diff-indicator unchanged">•</span>
                                                                @endif
                                                                <span class="key-name">{{ $key }}</span>
                                                            </div>
                                                            <div class="diff-values">
                                                                @if($hasOldValue)
                                                                <div class="diff-old-value {{ !$isChanged && !$isRemoved ? 'unchanged' : '' }}">
                                                                    <span class="value-content">{{ is_null($oldValue) ? 'null' : (is_bool($oldValue) ? ($oldValue ? 'true' : 'false') : json_encode($oldValue)) }}</span>
                                                                </div>
                                                                @endif
                                                                
                                                                @if($isChanged || $isAdded || $isRemoved)
                                                                <div class="diff-arrow">→</div>
                                                                @endif
                                                                
                                                                @if($isRemoved)
                                                                <div class="diff-new-value removed-placeholder">
                                                                    <span class="value-content">(removed)</span>
                                                                </div>
                                                                @elseif($isChanged || $isAdded)
                                                                <div class="diff-new-value">
                                                                    <span class="value-content">{{ is_null($newValue) ? 'null' : (is_bool($newValue) ? ($newValue ? 'true' : 'false') : json_encode($newValue)) }}</span>
                                                                </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                    @if(count($allKeys) === 0)
                                                        <div class="text-center text-muted py-4">
                                                            <p>No changes recorded</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="ip-address">{{ $log->ip_address }}</span>
                        </td>
                        <td>
                            <div class="timestamp-cell">
                                <div class="timestamp-date">{{ $log->created_at->format('M d, Y') }}</div>
                                <div class="timestamp-time">{{ $log->created_at->format('H:i:s') }}</div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="text-muted mb-3">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                <h6 class="text-muted mb-2">No Audit Logs Found</h6>
                                <p class="text-muted mb-0 small">Adjust your filters or check back later for activity</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="pagination-info">
                    Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }}
                </div>
                {{ $logs->links() }}
            </div>
        </div>
        @endif
    </div>

    <!-- Real-time Activity Feed Widget -->
    <div class="activity-feed-widget" id="activityFeed">
        <div class="feed-header">
            <div class="feed-title">
                <span class="pulse-indicator"></span>
                <h3>Live Activity</h3>
            </div>
            <button class="feed-toggle" id="feedToggle">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>
        <div class="feed-content" id="feedContent">
            <div class="feed-empty">
                <i class="bi bi-hourglass-split"></i>
                <p>Waiting for new activity...</p>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div class="toast-container" id="toastContainer"></div>
</div>

<style>
/* Typography & Base */
:root {
    --primary-color: #2563eb;
    --secondary-color: #64748b;
    --success-color: #16a34a;
    --warning-color: #ea580c;
    --danger-color: #dc2626;
    --border-color: #e2e8f0;
    --text-primary: #0f172a;
    --text-secondary: #64748b;
    --background-subtle: #f8fafc;
}

/* Page Header */
.page-header {
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 1.5rem;
}

.page-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
    letter-spacing: -0.025em;
}

.page-subtitle {
    font-size: 0.95rem;
    color: var(--text-secondary);
}

.record-count {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.25rem;
}

.count-number {
    font-size: 1.75rem;
    font-weight: 600;
    color: var(--text-primary);
    line-height: 1;
}

.count-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-secondary);
    font-weight: 500;
}

/* Filter Section */
.filter-section {
    border: 1px solid var(--border-color);
    box-shadow: none;
}

.filter-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
    display: block;
}

.form-select, .form-control {
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 0.5rem 0.75rem;
    font-size: 0.9375rem;
    transition: all 0.15s ease;
}

.form-select:focus, .form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Buttons */
.btn {
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.9375rem;
    padding: 0.5rem 1rem;
    transition: all 0.15s ease;
}

.btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-primary:hover {
    background-color: #1d4ed8;
    border-color: #1d4ed8;
}

.btn-outline-secondary {
    color: var(--text-secondary);
    border-color: var(--border-color);
}

.btn-outline-secondary:hover {
    background-color: var(--background-subtle);
    color: var(--text-primary);
}

/* Data Table */
.data-table-card {
    border: 1px solid var(--border-color);
    box-shadow: none;
}

.data-table {
    margin: 0;
    font-size: 0.9375rem;
}

.data-table thead {
    background-color: var(--background-subtle);
    border-bottom: 2px solid var(--border-color);
}

.data-table thead th {
    padding: 1rem 1rem;
    font-weight: 600;
    font-size: 0.8125rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-secondary);
    border-bottom: none;
}

.data-table tbody tr {
    border-bottom: 1px solid var(--border-color);
    transition: background-color 0.15s ease;
}

.data-table tbody tr:hover {
    background-color: var(--background-subtle);
}

.data-table tbody td {
    padding: 1rem 1rem;
    vertical-align: middle;
}

/* Table Cells */
.cell-id {
    font-weight: 500;
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.user-cell {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.user-name {
    font-weight: 500;
    color: var(--text-primary);
    font-size: 0.9375rem;
}

.user-role {
    font-size: 0.8125rem;
    color: var(--text-secondary);
    text-transform: capitalize;
}

.system-label {
    color: var(--text-secondary);
    font-style: italic;
}

/* Status Badges */
.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.8125rem;
    font-weight: 500;
    line-height: 1.5;
}

.status-created {
    background-color: #d1fae5;
    color: #065f46;
}

.status-updated {
    background-color: #fed7aa;
    color: #92400e;
}

.status-deleted {
    background-color: #fecaca;
    color: #991b1b;
}

/* Model Label */
.model-label {
    background-color: #f1f5f9;
    color: #334155;
    padding: 0.25rem 0.625rem;
    border-radius: 4px;
    font-size: 0.875rem;
    font-weight: 500;
    border: 1px solid #e2e8f0;
}

.model-id {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

/* View Details Button */
.btn-view-details {
    background: transparent;
    border: 1px solid var(--border-color);
    color: var(--primary-color);
    border-radius: 6px;
    padding: 0.375rem 0.875rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
}

.btn-view-details:hover {
    background-color: #eff6ff;
    border-color: var(--primary-color);
}

.btn-undo {
    background: transparent;
    border: 1px solid #dc2626;
    color: #dc2626;
    border-radius: 6px;
    padding: 0.375rem 0.875rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
}

.btn-undo:hover {
    background-color: #fef2f2;
    border-color: #b91c1c;
    color: #b91c1c;
}

/* IP Address */
.ip-address {
    font-family: 'SF Mono', 'Consolas', 'Monaco', monospace;
    font-size: 0.875rem;
    color: var(--text-secondary);
    background-color: #f8fafc;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

/* Timestamp */
.timestamp-cell {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
}

.timestamp-date {
    font-size: 0.9375rem;
    color: var(--text-primary);
    font-weight: 500;
}

.timestamp-time {
    font-size: 0.8125rem;
    color: var(--text-secondary);
    font-family: 'SF Mono', 'Consolas', 'Monaco', monospace;
}

/* Modal */
.modal-header {
    border-bottom: 1px solid var(--border-color);
    padding: 1.5rem;
}

.modal-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.modal-body {
    padding: 1.5rem;
}

/* Diff Viewer - HCI Enhanced */
.diff-container {
    background: linear-gradient(to bottom, #ffffff 0%, #fafbfc 100%);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.diff-viewer {
    font-family: 'SF Mono', 'Consolas', 'Monaco', monospace;
    font-size: 0.875rem;
}

.diff-row {
    display: grid;
    grid-template-columns: 220px 1fr;
    border-bottom: 1px solid #f1f5f9;
    transition: all 0.2s ease;
    position: relative;
}

.diff-row:last-child {
    border-bottom: none;
}

.diff-row:hover {
    background-color: #f8f9fb;
    transform: translateX(2px);
}

/* Enhanced highlight colors with gradients and depth */
.diff-row.diff-added {
    background: linear-gradient(to right, #ecfdf5 0%, #f0fdf4 50%, #ffffff 100%);
    border-left: 4px solid #10b981;
    box-shadow: inset 4px 0 0 0 rgba(16, 185, 129, 0.1);
}

.diff-row.diff-removed {
    background: linear-gradient(to right, #fef2f2 0%, #fef5f5 50%, #ffffff 100%);
    border-left: 4px solid #ef4444;
    box-shadow: inset 4px 0 0 0 rgba(239, 68, 68, 0.1);
}

.diff-row.diff-changed {
    background: linear-gradient(to right, #fffbeb 0%, #fef9ec 50%, #ffffff 100%);
    border-left: 4px solid #f59e0b;
    box-shadow: inset 4px 0 0 0 rgba(245, 158, 11, 0.1);
}

.diff-key {
    display: flex;
    align-items: center;
    padding: 1rem 1.25rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-right: 2px solid #e2e8f0;
    font-weight: 600;
    color: var(--text-primary);
    position: relative;
}

.diff-key::after {
    content: '';
    position: absolute;
    right: 0;
    top: 20%;
    height: 60%;
    width: 1px;
    background: linear-gradient(to bottom, transparent, #cbd5e1, transparent);
}

.diff-indicator {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    margin-right: 0.75rem;
    font-weight: 700;
    border-radius: 4px;
    font-size: 0.875rem;
}

.diff-indicator.added {
    background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
    color: white;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
}

.diff-indicator.removed {
    background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
    color: white;
    box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
}

.diff-indicator.changed {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: white;
    box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);
}

.diff-indicator.unchanged {
    background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
    color: #64748b;
}

.key-name {
    color: #334155;
    font-weight: 600;
    letter-spacing: 0.01em;
}

.diff-values {
    display: flex;
    align-items: center;
    padding: 1rem 1.25rem;
    gap: 1rem;
}

.diff-old-value {
    flex: 1;
    padding: 0.75rem 1rem;
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    border-radius: 8px;
    border: 1px solid #fca5a5;
    box-shadow: 0 1px 3px rgba(239, 68, 68, 0.1);
    position: relative;
    overflow: hidden;
}

.diff-old-value::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(to right, #f87171, #fca5a5);
}

.diff-old-value.unchanged {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-color: #cbd5e1;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.diff-old-value.unchanged::before {
    background: linear-gradient(to right, #cbd5e1, #e2e8f0);
}

.diff-new-value {
    flex: 1;
    padding: 0.75rem 1rem;
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    border-radius: 8px;
    border: 1px solid #6ee7b7;
    box-shadow: 0 1px 3px rgba(16, 185, 129, 0.1);
    position: relative;
    overflow: hidden;
}

.diff-new-value::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(to right, #34d399, #6ee7b7);
}

.diff-new-value.removed-placeholder {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-color: #cbd5e1;
    color: var(--text-secondary);
    font-style: italic;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.diff-new-value.removed-placeholder::before {
    background: linear-gradient(to right, #94a3b8, #cbd5e1);
}

.diff-arrow {
    color: #64748b;
    font-weight: 700;
    font-size: 1.5rem;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

.value-content {
    word-break: break-word;
    color: var(--text-primary);
    font-weight: 500;
    line-height: 1.5;
}


/* Remove old change panel styles */
.change-panel {
    border: 1px solid var(--border-color);
    border-radius: 8px;
    overflow: hidden;
}

.change-panel-title {
    background-color: var(--background-subtle);
    padding: 0.75rem 1rem;
    margin: 0;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-primary);
    border-bottom: 1px solid var(--border-color);
}

.change-content {
    background-color: #ffffff;
    padding: 1rem;
    margin: 0;
    font-size: 0.8125rem;
    line-height: 1.6;
    color: var(--text-primary);
    max-height: 400px;
    overflow-y: auto;
    font-family: 'SF Mono', 'Consolas', 'Monaco', monospace;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

/* Pagination */
.card-footer {
    background-color: var(--background-subtle);
    border-top: 1px solid var(--border-color);
    padding: 1rem 1.5rem;
}

.pagination-info {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

/* Remove Bootstrap Default Overrides */
.card {
    border-radius: 8px;
}

.table > :not(caption) > * > * {
    background-color: transparent;
}

/* Real-time Activity Feed Widget */
.activity-feed-widget {
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 420px;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 252, 0.95) 100%);
    backdrop-filter: blur(20px);
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), 0 0 1px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    z-index: 999;
    border: 1px solid rgba(226, 232, 240, 0.8);
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.activity-feed-widget.collapsed .feed-content {
    display: none;
}

.activity-feed-widget.collapsed {
    width: 200px;
}

.feed-header {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    user-select: none;
}

.feed-title {
    display: flex;
    align-items: center;
    gap: 12px;
}

.feed-title h3 {
    color: #ffffff;
    font-size: 16px;
    font-weight: 600;
    margin: 0;
}

.pulse-indicator {
    width: 12px;
    height: 12px;
    background: #10b981;
    border-radius: 50%;
    position: relative;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 1);
    animation: pulse-ring 2s ease-out infinite;
}

@keyframes pulse-ring {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    }
    50% {
        box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
    }
}

.feed-toggle {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: #ffffff;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.feed-toggle:hover {
    background: rgba(255, 255, 255, 0.3);
}

.feed-content {
    max-height: 400px;
    overflow-y: auto;
    padding: 12px;
}

.feed-empty {
    text-align: center;
    padding: 40px 20px;
    color: #94a3b8;
}

.feed-empty i {
    font-size: 48px;
    margin-bottom: 12px;
    display: block;
    opacity: 0.5;
}

.feed-empty p {
    margin: 0;
    font-size: 14px;
}

.feed-item {
    background: #ffffff;
    border-radius: 12px;
    padding: 14px;
    margin-bottom: 10px;
    border-left: 4px solid transparent;
    transition: all 0.2s ease;
    animation: slideInRight 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.feed-item:hover {
    transform: translateX(-4px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.feed-item.action-created {
    border-left-color: #10b981;
    background: linear-gradient(to right, #f0fdf4 0%, #ffffff 100%);
}

.feed-item.action-updated {
    border-left-color: #f59e0b;
    background: linear-gradient(to right, #fffbeb 0%, #ffffff 100%);
}

.feed-item.action-deleted {
    border-left-color: #ef4444;
    background: linear-gradient(to right, #fef2f2 0%, #ffffff 100%);
}

.feed-item-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
}

.feed-user {
    font-weight: 600;
    color: #0f172a;
    font-size: 14px;
}

.feed-time {
    font-size: 12px;
    color: #94a3b8;
}

.feed-message {
    font-size: 13px;
    color: #64748b;
    line-height: 1.5;
}

.feed-action-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-right: 6px;
}

.feed-action-badge.created {
    background: #d1fae5;
    color: #065f46;
}

.feed-action-badge.updated {
    background: #fed7aa;
    color: #92400e;
}

.feed-action-badge.deleted {
    background: #fecaca;
    color: #991b1b;
}

/* Toast Notifications */
.toast-container {
    position: fixed;
    top: 80px;
    right: 24px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 12px;
    pointer-events: none;
}

.toast {
    min-width: 350px;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 100%);
    backdrop-filter: blur(20px);
    border-radius: 12px;
    padding: 16px 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15), 0 0 1px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(226, 232, 240, 0.8);
    display: flex;
    align-items: flex-start;
    gap: 14px;
    pointer-events: all;
    animation: toastSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    position: relative;
    overflow: hidden;
}

@keyframes toastSlideIn {
    from {
        opacity: 0;
        transform: translateX(400px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.toast.hiding {
    animation: toastSlideOut 0.3s ease forwards;
}

@keyframes toastSlideOut {
    to {
        opacity: 0;
        transform: translateX(400px);
    }
}

.toast::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(to bottom, #2563eb, #1d4ed8);
}

.toast-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 20px;
}

.toast.info .toast-icon {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
}

.toast.success .toast-icon {
    background: linear-gradient(135deg, #34d399, #10b981);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
}

.toast.warning .toast-icon {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
}

.toast-content {
    flex: 1;
}

.toast-title {
    font-weight: 600;
    color: #0f172a;
    font-size: 14px;
    margin-bottom: 4px;
}

.toast-message {
    font-size: 13px;
    color: #64748b;
    line-height: 1.4;
}

.toast-close {
    background: transparent;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.toast-close:hover {
    background: rgba(148, 163, 184, 0.1);
    color: #64748b;
}

</style>

<script>
// Real-time Audit Log Monitoring System
(function() {
    'use strict';
    
    let latestLogId = {{ $logs->first()?->id ?? 0 }};
    let pollingInterval = null;
    const POLL_INTERVAL = 10000; // 10 seconds
    
    // Toast Notification System
    const ToastManager = {
        container: document.getElementById('toastContainer'),
        
        show(title, message, type = 'info', duration = 5000) {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            const icons = {
                info: '🔔',
                success: '✅',
                warning: '⚠️',
                error: '❌'
            };
            
            toast.innerHTML = `
                <div class="toast-icon">
                    ${icons[type] || icons.info}
                </div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">
                    <i class="bi bi-x-lg"></i>
                </button>
            `;
            
            this.container.appendChild(toast);
            
            // Auto-remove after duration
            setTimeout(() => {
                toast.classList.add('hiding');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        },
        
        newLog(log) {
            const actionEmoji = {
                created: '➕',
                updated: '✏️',
                deleted: '🗑️'
            };
            
            this.show(
                'New Activity',
                `${actionEmoji[log.action]} ${log.user_name} ${log.action} ${log.model} #${log.model_id}`,
                log.action === 'deleted' ? 'warning' : 'info',
                6000
            );
        }
    };
    
    // Activity Feed Manager
    const ActivityFeed = {
        widget: document.getElementById('activityFeed'),
        content: document.getElementById('feedContent'),
        toggle: document.getElementById('feedToggle'),
        collapsed: false,
        
        init() {
            this.toggle.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleCollapse();
            });
            
            this.widget.querySelector('.feed-header').addEventListener('click', (e) => {
                if (e.target !== this.toggle && !this.toggle.contains(e.target)) {
                    this.toggleCollapse();
                }
            });
        },
        
        toggleCollapse() {
            this.collapsed = !this.collapsed;
            this.widget.classList.toggle('collapsed', this.collapsed);
            this.toggle.querySelector('i').className = this.collapsed ? 'bi bi-chevron-up' : 'bi bi-chevron-down';
        },
        
        addItem(log) {
            // Remove "empty" message if exists
            const empty = this.content.querySelector('.feed-empty');
            if (empty) empty.remove();
            
            const item = document.createElement('div');
            item.className = `feed-item action-${log.action}`;
            item.innerHTML = `
                <div class="feed-item-header">
                    <div class="feed-user">${log.user_name}</div>
                    <div class="feed-time">${log.relative_time}</div>
                </div>
                <div class="feed-message">
                    <span class="feed-action-badge ${log.action}">${log.action}</span>
                    ${log.model} #${log.model_id}
                </div>
            `;
            
            // Add to top of feed
            this.content.insertBefore(item, this.content.firstChild);
            
            // Keep only last 10 items
            const items = this.content.querySelectorAll('.feed-item');
            if (items.length > 10) {
                items[items.length - 1].remove();
            }
        }
    };
    
    // Polling Function
    async function pollForNewLogs() {
        try {
            const response = await fetch(`{{ route('admin.audit_logs.poll') }}?since_id=${latestLogId}`);
            const data = await response.json();
            
            if (data.success && data.logs.length > 0) {
                data.logs.reverse().forEach(log => {
                    // Show toast notification
                    ToastManager.newLog(log);
                    
                    // Add to activity feed
                    ActivityFeed.addItem(log);
                    
                    // Prepend to table (if on first page)
                    prependToTable(log);
                });
                
                latestLogId = data.latest_id;
            }
        } catch (error) {
            console.error('Polling error:', error);
        }
    }
    
    // Add new log row to table
    function prependToTable(log) {
        // Only add if we're on the first page (no filters active)
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.toString()) return; // Skip if filters are active
        
        const tbody = document.querySelector('.data-table tbody');
        if (!tbody) return;
        
        const actionColors = {
            created: 'status-created',
            updated: 'status-updated',
            deleted: 'status-deleted'
        };
        
        const row = document.createElement('tr');
        row.className = 'new-log-highlight';
        row.innerHTML = `
            <td><span class="cell-id">${log.id}</span></td>
            <td>
                <div class="user-cell">
                    <span class="user-name">${log.user_name}</span>
                    <span class="user-role">${log.user_role}</span>
                </div>
            </td>
            <td><span class="status-badge ${actionColors[log.action]}">${log.action.charAt(0).toUpperCase() + log.action.slice(1)}</span></td>
            <td><code class="model-label">${log.model}</code></td>
            <td><span class="model-id">${log.model_id}</span></td>
            <td><button class="btn-view-details">View Changes</button></td>
            <td><span class="ip-address">${log.ip_address}</span></td>
            <td>
                <div class="timestamp-cell">
                    <div class="timestamp-date">${log.created_at}</div>
                    <div class="timestamp-time">${log.created_time}</div>
                </div>
            </td>
        `;
        
        // Insert at top
        tbody.insertBefore(row, tbody.firstChild);
        
        // Add highlight animation
        setTimeout(() => row.classList.remove('new-log-highlight'), 2000);
        
        // Remove last row if we're showing 20 per page
        const rows = tbody.querySelectorAll('tr');
        if (rows.length > 20) {
            rows[rows.length - 1].remove();
        }
    }
    
    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        ActivityFeed.init();
        
        // Start polling
        pollingInterval = setInterval(pollForNewLogs, POLL_INTERVAL);
        
        // Initial welcome toast
        ToastManager.show(
            'Real-time Monitoring Active',
            'You will be notified of new audit log entries automatically',
            'success'
        );
    });
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        if (pollingInterval) clearInterval(pollingInterval);
    });
    
    // Undo Functionality
    window.confirmUndo = async function(id) {
        // 1. Prompt for password
        const { value: password } = await Swal.fire({
            title: 'Admin Verification Required',
            text: "Please enter your password to confirm this restoration.",
            input: 'password',
            inputPlaceholder: 'Enter your admin password',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            confirmButtonText: 'Verify & Restore',
            inputValidator: (value) => {
                if (!value) {
                    return 'You need to write something!'
                }
            }
        });

        if (password) {
            // 2. Show loading
            Swal.fire({
                title: 'Restoring Asset...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // 3. AJAX Request
            try {
                const response = await fetch(`/admin/audit-logs/${id}/restore`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ password: password })
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire('Restored!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'Something went wrong', 'error');
            }
        }
    }
    
})();
</script>

@endsection
