@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Assets</h2>
        <a href="{{ route('admin.assets.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add New</a>
    </div>

    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Facility</th>
                <th>Asset Name</th>
                <th>Type</th>
                <th>Serial No.</th>
                <th>Condition</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $asset)
            <tr>
                <td>{{ $asset->id }}</td>
                <td class="text-muted">{{ $asset->facility->name ?? 'Unassigned' }}</td>
                
                <!-- Make Name Clickable -->
                <td class="fw-bold">
                    <a href="{{ route('admin.assets.show', $asset->id) }}" class="text-decoration-none text-dark">
                        {{ $asset->name }}
                    </a>
                </td>
                
                <td>{{ $asset->type }}</td>
                <td><code>{{ $asset->serial_number }}</code></td>
                <td>
                    @php
                        $badgeClass = match($asset->condition) {
                            'Good' => 'success',
                            'Fair' => 'info',
                            'Damaged', 'Retired' => 'danger',
                            'Maintenance' => 'warning',
                            default => 'secondary'
                        };
                    @endphp
                    <span class="badge bg-{{ $badgeClass }}">{{ $asset->condition }}</span>
                </td>
                <td>
                    <!-- View Button -->
                    <a href="{{ route('admin.assets.show', $asset->id) }}" class="btn btn-sm btn-info text-white">View</a>

                    <a href="{{ route('admin.assets.edit', $asset->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    
                    <form action="{{ route('admin.assets.destroy', $asset->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this asset?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="d-flex justify-content-center">
        {{ $assets->links() }}
    </div>
</div>
@endsection