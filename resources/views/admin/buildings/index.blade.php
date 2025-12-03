@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Buildings</h2>
        <a href="{{ route('admin.buildings.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add New</a>
    </div>

    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Img</th>
                <th>Name</th>
                <th>Location</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($buildings as $building)
            <tr>
                <td>{{ $building->id }}</td>
                <td>
                    @if($building->image_path)
                        <img src="{{ asset('storage/' . $building->image_path) }}" alt="Building Image" width="50" height="50" class="rounded object-fit-cover">
                    @else
                        <span class="text-muted">No Image</span>
                    @endif
                </td>
                <td class="fw-bold">{{ $building->name }}</td>
                <td>{{ $building->location }}</td>
                <td>
                    <a href="{{ route('admin.buildings.edit', $building->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    
                    <form action="{{ route('admin.buildings.destroy', $building->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this building?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection