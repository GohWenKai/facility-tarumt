@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">Edit Facility: {{ $facility->name }}</div>
        <div class="card-body">
            <form action="{{ route('admin.facilities.update', $facility->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Row 1: ID and Name -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Building ID</label>
                        <input type="number" name="building_id" class="form-control" value="{{ $facility->building_id }}" disabled>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Facility Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $facility->name }}" required>
                    </div>
                </div>

                <!-- Row 2: Type and Capacity -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" required>
                            <option value="Lab" {{ $facility->type == 'Lab' ? 'selected' : '' }}>Lab</option>
                            <option value="Room" {{ $facility->type == 'Room' ? 'selected' : '' }}>Room</option>
                            <option value="Hall" {{ $facility->type == 'Hall' ? 'selected' : '' }}>Hall</option>
                            <option value="Sports" {{ $facility->type == 'Sports' ? 'selected' : '' }}>Sports</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Capacity</label>
                        <input type="number" name="capacity" class="form-control" value="{{ $facility->capacity }}" required>
                    </div>
                </div>

                <!-- Row 3: Operating Hours (Start & End Time) -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Start Time</label>
                        <select name="start_time" class="form-select" required>
                            <option value="" disabled>Select Start Time</option>
                            @foreach(range(8, 21) as $hour)
                                @php 
                                    $timeFull = sprintf('%02d:00', $hour); 
                                    $timeHalf = sprintf('%02d:30', $hour);
                                    $savedStart = \Carbon\Carbon::parse($facility->start_time)->format('H:i');
                                @endphp
                                <option value="{{ $timeFull }}" {{ $savedStart == $timeFull ? 'selected' : '' }}>{{ $timeFull }}</option>
                                <option value="{{ $timeHalf }}" {{ $savedStart == $timeHalf ? 'selected' : '' }}>{{ $timeHalf }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Operating hours starts (08:00 - 21:30)</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">End Time</label>
                        <select name="end_time" class="form-select" required>
                            <option value="" disabled>Select End Time</option>
                            @foreach(range(8, 22) as $hour)
                                @php 
                                    $timeFull = sprintf('%02d:00', $hour); 
                                    $timeHalf = sprintf('%02d:30', $hour);
                                    $savedEnd = \Carbon\Carbon::parse($facility->end_time)->format('H:i');
                                @endphp
                                @if($hour > 8)
                                    <option value="{{ $timeFull }}" {{ $savedEnd == $timeFull ? 'selected' : '' }}>{{ $timeFull }}</option>
                                @endif
                                @if($hour < 22)
                                    <option value="{{ $timeHalf }}" {{ $savedEnd == $timeHalf ? 'selected' : '' }}>{{ $timeHalf }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Row 4: Status -->
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="Available" {{ $facility->status == 'Available' ? 'selected' : '' }}>Available</option>
                        <option value="Maintenance" {{ $facility->status == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="Closed" {{ $facility->status == 'Closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <!-- Row 5: Image (UPDATED WITH PREVIEW) -->
                <div class="mb-3">
                    <label class="form-label">Update Image (Leave empty to keep current)</label>
                    
                    <!-- Added ID 'imageInput' and onchange event -->
                    <input type="file" name="image" id="imageInput" class="form-control" accept="image/png, image/jpeg, image/jpg" onchange="previewImage(event)">
                    <small class="text-muted">Allowed formats: JPG, JPEG, PNG (Max 2MB)</small>

                    <div class="row mt-3">
                        <!-- Current Image Display -->
                        @if($facility->image_path)
                            <div class="col-md-6">
                                <small class="fw-bold">Current Image:</small><br>
                                <img src="{{ asset('storage/' . $facility->image_path) }}" width="150" class="rounded border">
                            </div>
                        @endif

                        <!-- New Image Preview (Hidden by default) -->
                        <div class="col-md-6" id="previewContainer" style="display: none;">
                            <small class="fw-bold text-success">New Image Preview:</small><br>
                            <img id="imagePreview" src="#" width="150" class="rounded border border-success">
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.facilities.manage') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary w-100">Update Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for Image Preview -->
<script>
    function previewImage(event) {
        var reader = new FileReader();
        var imageField = document.getElementById("imagePreview");
        var container = document.getElementById("previewContainer");

        reader.onload = function(){
            if(reader.readyState == 2){
                imageField.src = reader.result;
                container.style.display = "block"; // Show the preview div
            }
        }

        // If a file is selected, read it. If cancelled, hide preview.
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        } else {
            container.style.display = "none";
        }
    }
</script>
@endsection