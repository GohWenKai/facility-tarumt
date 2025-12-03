@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold">Find a Facility</h2>
            <p class="text-muted">Search for available labs, halls, and rooms.</p>
        </div>
        <div class="col-md-4">
            <!-- Standard Search Form -->
            <form action="{{ route('facilities.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Search facilities..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        @forelse($facilities as $facility)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <!-- Fixed Image Logic -->
                <div style="height: 200px; overflow: hidden;">
                    @if($facility->image_path)
                        <img src="{{ asset('storage/' . $facility->image_path) }}" class="card-img-top h-100 object-fit-cover" alt="{{ $facility->name }}">
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100 bg-light text-muted">
                            <i class="bi bi-image fs-1"></i>
                        </div>
                    @endif
                </div>

                <div class="card-body">
                    <h5 class="card-title fw-bold">{{ $facility->name }}</h5>
                    <p class="card-text text-muted small mb-2">
                        <i class="bi bi-building"></i> {{ $facility->building->name }}
                    </p>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="badge bg-light text-dark border">{{ $facility->type }}</span>
                        <span class="badge bg-light text-dark border"><i class="bi bi-people"></i> {{ $facility->capacity }} Pax</span>
                    </div>

                    <!-- ✅ BROKEN ASSET WARNING (Requirement Fulfillment) -->
                    @if($facility->broken_assets_count > 0)
                        <div class="alert alert-warning py-2 px-3 d-flex align-items-center mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-2 fs-5 text-warning-emphasis"></i>
                            <div style="line-height: 1.2;">
                                <span class="fw-bold text-dark">Equipment Issue</span><br>
                                <small class="text-muted">{{ $facility->broken_assets_count }} item(s) unavailable</small>
                            </div>
                        </div>
                    @endif

                    <a href="{{ route('facilities.show', $facility->id) }}" class="btn btn-outline-primary w-100 stretched-link">
                        View Availability
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted">
                <i class="bi bi-search fs-1"></i>
                <p class="mt-2">No facilities found matching your criteria.</p>
            </div>
        </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-5">
        {{ $facilities->links() }}
    </div>
</div>
@endsection