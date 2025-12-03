@extends('layouts.admin')

@section('content')
<div class="container">
    <!-- ADD THIS: Error Display Block -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-header bg-primary text-white">Add New Facility</div>
        <div class="card-body">
            <form action="{{ route('admin.facilities.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Row 1: Building AND Name -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Building</label>
                        <select name="building_id" class="form-select" required>
                            <option value="" selected disabled>Select a Building</option>
                            @foreach($buildings as $building)
                                <option value="{{ $building->id }}">
                                    {{ $building->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- YOU WERE MISSING THIS PART BELOW -->
                    <div class="col-md-8">
                        <label class="form-label">Facility Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter facility name" required>
                    </div>
                    <!-- END MISSING PART -->
                </div>

                <!-- Row 2: Type and Capacity -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" required>
                            <option value="" selected disabled>Select Type</option>
                            <option value="Lab">Lab</option>
                            <option value="Room">Room</option>
                            <option value="Hall">Hall</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Capacity</label>
                        <input type="number" name="capacity" class="form-control" placeholder="Enter capacity" required>
                    </div>
                </div>

                <!-- Row 3: Operating Hours -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Start Time</label>
                        <select name="start_time" class="form-select" required>
                            <option value="" selected disabled>Select Start Time</option>
                            @foreach(range(8, 21) as $hour)
                                @php 
                                    $timeFull = sprintf('%02d:00', $hour); 
                                    $timeHalf = sprintf('%02d:30', $hour);
                                @endphp
                                <option value="{{ $timeFull }}">{{ $timeFull }}</option>
                                <option value="{{ $timeHalf }}">{{ $timeHalf }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">End Time</label>
                        <select name="end_time" class="form-select" required>
                            <option value="" selected disabled>Select End Time</option>
                            @foreach(range(8, 22) as $hour)
                                @php 
                                    $timeFull = sprintf('%02d:00', $hour); 
                                    $timeHalf = sprintf('%02d:30', $hour);
                                @endphp
                                @if($hour > 8) <option value="{{ $timeFull }}">{{ $timeFull }}</option> @endif
                                @if($hour < 22) <option value="{{ $timeHalf }}">{{ $timeHalf }}</option> @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Row 4: Status -->
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="Available" selected>Available</option>
                        <option value="Maintenance">Maintenance</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>
                
                <!-- Row 5: Image -->
                <div class="mb-3">
                    <label class="form-label">Facility Image</label>
                        <input onchange="previewImage(event)" type="file" name="image" class="form-control" accept="image/png, image/jpeg, image/jpg">
                        <small class="text-muted">Allowed formats: JPG, JPEG, PNG (Max 2MB)</small>

                    <!-- Image Preview Container (Hidden by default) -->
                    <div class="mt-3" id="previewContainer" style="display: none;">
                        <small class="fw-bold text-success">Preview:</small><br>
                        <img id="imagePreview" src="#" width="200" class="rounded border border-success">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Create Facility</button>
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