@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10"> <!-- Widened for better table display -->
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Asset Details: {{ $asset->name }}</h4>
                    <a href="{{ route('admin.assets.manage') }}" class="btn btn-sm btn-light">Back to List</a>
                </div>
                
                <div class="card-body">
                    <!-- DATABASE DETAILS SECTION -->
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold text-muted">Asset ID</div>
                        <div class="col-md-8">{{ $asset->id }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold text-muted">Facility</div>
                        <div class="col-md-8">{{ $asset->facility->name ?? 'Unassigned' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold text-muted">Type</div>
                        <div class="col-md-8">{{ $asset->type }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold text-muted">Serial Number</div>
                        <div class="col-md-8"><code>{{ $asset->serial_number }}</code></div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold text-muted">Condition</div>
                        <div class="col-md-8">
                            @php
                                $badgeClass = match($asset->condition) {
                                    'Good' => 'success',
                                    'Fair' => 'info',
                                    'Damaged', 'Retired' => 'danger',
                                    'Maintenance' => 'warning',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeClass }} fs-6">{{ $asset->condition }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold text-muted">Maintenance Note</div>
                        <div class="col-md-8">
                            @if($asset->maintenance_note)
                                <div class="alert alert-warning mb-0">
                                    {{ $asset->maintenance_note }}
                                </div>
                            @else
                                <span class="text-muted fst-italic">No maintenance notes available.</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold text-muted">DB Last Updated</div>
                        <div class="col-md-8">{{ $asset->updated_at->diffForHumans() }}</div>
                    </div>

                    <hr>

                    <!-- XML HISTORY SECTION -->
                    <div class="card shadow-sm mt-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">History Log (XML Records: {{ count($xmlRecords) }})</h5>
                        </div>
                        
                        <div class="card-body p-0">
                            @if(empty($xmlRecords))
                                <div class="alert alert-light m-3 text-center text-muted">
                                    No XML history records found.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover mb-0 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Name</th>
                                                <th>Type</th>
                                                <th>Serial No</th>
                                                <th>Condition</th>
                                                <th>Last Update</th>
                                                <th>Note</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($xmlRecords as $record)
                                            <tr>
                                                <td class="fw-bold">{{ $record['name'] }}</td>
                                                <td>{{ $record['type'] }}</td>
                                                <td class="font-monospace text-primary">
                                                    {{ $record['serial_number'] }}
                                                </td>
                                                <td>
                                                    @php
                                                        $recBadge = match($record['condition']) {
                                                            'Good' => 'success',
                                                            'Fair' => 'info',
                                                            'Damaged', 'Retired' => 'danger',
                                                            'Maintenance' => 'warning',
                                                            default => 'secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $recBadge }}">
                                                        {{ $record['condition'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $record['created_at'] }}
                                                </td>
                                                <td>
                                                    @if(!empty($record['maintenance_note']))
                                                        <span class="text-danger small fw-bold">
                                                            {{ $record['maintenance_note'] }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted small fst-italic">None</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- END XML SECTION -->
                </div>

                <div class="card-footer text-end">
                    <a href="{{ route('admin.assets.edit', $asset->id) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit Asset
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection