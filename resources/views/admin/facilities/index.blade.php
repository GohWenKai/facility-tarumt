@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Facilities</h2>
        <a href="{{ route('admin.facilities.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add New</a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Error Message (e.g. Cannot delete because of bookings) -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Img</th>
                <th>Building</th>
                <th>Name</th>
                <th>Type</th>
                <th>Pax</th>
                <th>Hours</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facilities as $facility)
            <tr>
                <td>{{ $facility->id }}</td>
                <td>
                    @if($facility->image_path)
                        <img src="{{ asset('storage/' . $facility->image_path) }}" alt="Facility Image" width="50" height="50" class="rounded object-fit-cover">
                    @else
                        <span class="text-muted">No Image</span>
                    @endif
                </td>
                <td>{{ $facility->building->name ?? 'N/A' }}</td>
                <td class="fw-bold">{{ $facility->name }}</td>
                <td>{{ $facility->type }}</td>
                <td>{{ $facility->capacity }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($facility->start_time)->format('H:i') }} 
                    - 
                    {{ \Carbon\Carbon::parse($facility->end_time)->format('H:i') }}
                </td>
                <td>
                    <!-- IMPROVED BADGE COLORS -->
                    @php
                        $badgeClass = match($facility->status) {
                            'Available' => 'success',
                            'Maintenance' => 'warning', // Yellow for Maintenance
                            'Closed' => 'danger',       // Red for Closed
                            default => 'secondary'
                        };
                    @endphp
                    <span class="badge bg-{{ $badgeClass }} text-{{ $badgeClass == 'warning' ? 'dark' : 'white' }}">
                        {{ $facility->status }}
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.facilities.edit', $facility->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        
                        <form action="{{ route('admin.facilities.destroy', $facility->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this facility? This action cannot be undone.')">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="d-flex justify-content-center mt-3">
        {{ $facilities->links() }}
    </div>
</div>
@endsection