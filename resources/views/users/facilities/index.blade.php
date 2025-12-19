@extends('layouts.app')

@section('content')

{{-- CUSTOM STYLES for Portal Look --}}
<style>
    /* Hero Search Section */
    .portal-hero {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        padding: 4rem 1rem;
        color: white;
        border-radius: 0 0 24px 24px;
        margin-top: -1.5rem; /* Offset plain container margin */
        margin-bottom: 3rem;
        position: relative;
    }
    .search-box {
        background: white;
        border-radius: 50rem;
        padding: 0.5rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border: 4px solid rgba(255,255,255,0.2);
    }
    .search-input {
        border: none;
        box-shadow: none;
        padding-left: 1.5rem;
        font-size: 1.1rem;
    }
    .search-input:focus {
        box-shadow: none;
    }
    
    /* Facility Card */
    .facility-card {
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        transition: transform 0.3s, box-shadow 0.3s;
        overflow: hidden;
        background: white;
        position: relative;
    }
    .facility-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: #cbd5e1;
    }
    
    /* Maintenance Overlay */
    .maintenance-overlay {
        background: #fff3cd;
        color: #856404;
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
        border-top: 1px solid #ffeeba;
    }

    /* Badges */
    .card-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(255,255,255,0.95);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
        color: #1e293b;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        z-index: 2;
    }
    .capacity-pill {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 0.8rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* HCI: Feedback & Affordance */
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: scale(1.05); }

    /* HCI: Accessibility Focus Ring */
    .focus-ring:focus-within {
        box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.3);
    }
</style>

<div class="container-fluid p-0">
    {{-- 1. HERO SEARCH SECTION --}}
    <div class="portal-hero text-center mb-5">
        <div class="container" style="max-width: 800px;">
            <h1 class="fw-bold mb-2">Find a Facility</h1>
            <p class="opacity-75 mb-4 fs-5">Reserve discussion rooms, halls, and labs instantly.</p>
            
            <form action="{{ route('facilities.index') }}" method="GET" class="mb-3">
                <div class="search-box d-flex align-items-center focus-ring">
                    <label for="search-input" class="visually-hidden">Search Facilities</label>
                    <input type="text" id="search-input" name="search" class="form-control search-input" 
                           placeholder="Search by name, block (e.g. 'Lab', 'H204')..." 
                           value="{{ request('search') }}"
                           aria-label="Search facilities">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm" aria-label="Submit Search">
                        Search
                    </button>
                </div>
            </form>

            {{-- HCI: Recognition rather than Recall (Quick Tags) --}}
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <small class="text-white opacity-75 fw-bold text-uppercase" style="letter-spacing: 1px;">Quick Filters:</small>
                <a href="{{ route('facilities.index', ['search' => 'Lab']) }}" class="badge bg-white text-primary text-decoration-none rounded-pill px-3 py-2 border border-white border-opacity-25 hover-scale">
                    <i class="bi bi-pc-display me-1"></i> Labs
                </a>
                <a href="{{ route('facilities.index', ['search' => 'Hall']) }}" class="badge bg-white text-primary text-decoration-none rounded-pill px-3 py-2 border border-white border-opacity-25 hover-scale">
                    <i class="bi bi-people-fill me-1"></i> Halls
                </a>
                <a href="{{ route('facilities.index', ['search' => 'Room']) }}" class="badge bg-white text-primary text-decoration-none rounded-pill px-3 py-2 border border-white border-opacity-25 hover-scale">
                    <i class="bi bi-chat-square-text me-1"></i> Rooms
                </a>
            </div>
        </div>
    </div>

    {{-- 2. FACILITY GRID --}}
    <div class="container mb-5">
        
        {{-- HCI: Visibility of System Status --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-secondary m-0">
                @if(request('search'))
                    Found <span class="text-dark">{{ $facilities->total() }}</span> results for "<span class="text-primary">{{ request('search') }}</span>"
                @else
                    All Facilities <span class="badge bg-secondary rounded-pill ms-1">{{ $facilities->total() }}</span>
                @endif
            </h5>
            
            @if(request('search'))
                <a href="{{ route('facilities.index') }}" class="btn btn-sm btn-outline-danger rounded-pill" data-bs-toggle="tooltip" title="Clear current search filters">
                    <i class="bi bi-x-lg"></i> Clear Search
                </a>
            @endif
        </div>

        <div class="row g-4">
            @forelse($facilities as $facility)
            <div class="col-lg-4 col-md-6">
                <!-- Wrapper Link -->
                <a href="{{ route('facilities.show', $facility->id) }}" class="text-decoration-none text-dark">
                    <div class="facility-card h-100">
                        
                        <!-- Image Container -->
                        <div style="height: 220px; position: relative;">
                            {{-- Type Badge --}}
                            <span class="card-badge">
                                {{ $facility->type }}
                            </span>

                            @if($facility->image_path)
                                <img src="{{ asset('storage/' . $facility->image_path) }}" class="w-100 h-100 object-fit-cover">
                            @else
                                <div class="w-100 h-100 bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-card-image fs-1 text-muted opacity-50"></i>
                                </div>
                            @endif
                            
                            {{-- Gradient Overlay --}}
                            <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 80px; background: linear-gradient(to top, rgba(0,0,0,0.4), transparent);"></div>
                        </div>

                        <!-- Content -->
                        <div class="p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="fw-bold mb-0 text-primary">{{ $facility->name }}</h5>
                            </div>
                            
                            <p class="text-muted small mb-3">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $facility->building->name }}
                            </p>

                            <div class="d-flex gap-2 flex-wrap mb-3">
                                <span class="capacity-pill">
                                    <i class="bi bi-people-fill text-secondary"></i> {{ $facility->capacity }} Pax
                                </span>
                                <span class="capacity-pill">
                                    <i class="bi bi-clock text-secondary"></i> 8AM - 10PM
                                </span>
                            </div>

                            <!-- Maintenance Alert (If any) -->
                            @if($facility->broken_assets_count > 0)
                                <div class="maintenance-overlay rounded d-flex align-items-center mt-3">
                                    <i class="bi bi-cone-striped me-2 fs-5"></i>
                                    <div>
                                        <strong>Maintenance Alert</strong><br>
                                        <span class="small">{{ $facility->broken_assets_count }} equipment reported</span>
                                    </div>
                                </div>
                            @else 
                                <div class="d-grid mt-3">
                                    <button class="btn btn-light text-primary fw-bold border">
                                        Check Availability
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <div class="opacity-25 mb-3">
                    <i class="bi bi-search" style="font-size: 5rem;"></i>
                </div>
                <h3>No facilities found</h3>
                <p class="text-muted">Try adjusting your search terms or view all facilities.</p>
                <a href="{{ route('facilities.index') }}" class="btn btn-outline-primary rounded-pill px-4">Clear Search</a>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-5">
            {{ $facilities->links() }}
        </div>
    </div>
</div>
@endsection