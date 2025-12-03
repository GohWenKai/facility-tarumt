@extends('layouts.admin')

@section('content')
<div class="container">
    <!-- Error Display -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-header bg-warning text-dark">Edit Building: {{ $building->name }}</div>
        <div class="card-body">
            <form action="{{ route('admin.buildings.update', $building->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Row 1: ID (Read-only) -->
                <div class="mb-3">
                    <label class="form-label">Building ID</label>
                    <input type="text" class="form-control" value="{{ $building->id }}" disabled>
                </div>

                <!-- Row 2: Name and Location -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Building Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $building->name }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" value="{{ $building->location }}" required>
                    </div>
                </div>

                <!-- Row 3: Image -->
                <div class="mb-3">
                    <label class="form-label">Update Image (Leave empty to keep current)</label>
                    
                    <input type="file" name="image" id="imageInput" class="form-control" accept="image/png, image/jpeg, image/jpg" onchange="previewImage(event)">
                    <small class="text-muted">Allowed formats: JPG, JPEG, PNG (Max 2MB)</small>

                    <div class="row mt-3">
                        <!-- Current Image Display -->
                        @if($building->image_path)
                            <div class="col-md-6">
                                <small class="fw-bold">Current Image:</small><br>
                                <img src="{{ asset('storage/' . $building->image_path) }}" width="200" class="rounded border">
                            </div>
                        @endif

                        <!-- New Image Preview (Hidden by default) -->
                        <div class="col-md-6" id="previewContainer" style="display: none;">
                            <small class="fw-bold text-success">New Image Preview:</small><br>
                            <img id="imagePreview" src="#" width="200" class="rounded border border-success">
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.buildings.manage') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary flex-grow-1">Update Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Preview Script (Same as Create) -->
<script>
    function previewImage(event) {
        var input = event.target;
        var imageField = document.getElementById("imagePreview");
        var container = document.getElementById("previewContainer");
        var file = input.files[0];

        if (!file) {
            container.style.display = "none";
            return;
        }

        if (!file.type.match('image.*')) {
            alert("Please select a valid image file (JPG, JPEG, PNG).");
            input.value = "";
            container.style.display = "none";
            return;
        }

        var maxSize = 2 * 1024 * 1024; // 2MB
        if (file.size > maxSize) {
            alert("File is too large! Maximum size is 2MB.");
            input.value = "";
            container.style.display = "none";
            return;
        }

        var reader = new FileReader();
        reader.onload = function(){
            if(reader.readyState == 2){
                imageField.src = reader.result;
                container.style.display = "block";
            }
        }
        reader.readAsDataURL(file);
    }
</script>
@endsection