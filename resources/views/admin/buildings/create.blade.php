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
        <div class="card-header bg-primary text-white">Add New Building</div>
        <div class="card-body">
            <form action="{{ route('admin.buildings.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Row 1: Name and Location -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Building Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Block A" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" placeholder="e.g. West Campus, Main Road" required>
                    </div>
                </div>

                <!-- Row 2: Image -->
                <div class="mb-3">
                    <label class="form-label">Building Image</label>
                    
                    <!-- File Input with JS Validation -->
                    <input type="file" name="image" id="imageInput" class="form-control" accept="image/png, image/jpeg, image/jpg" onchange="previewImage(event)">
                    <small class="text-muted">Allowed formats: JPG, JPEG, PNG (Max 2MB)</small>

                    <!-- Image Preview Container (Hidden by default) -->
                    <div class="mt-3" id="previewContainer" style="display: none;">
                        <small class="fw-bold text-success">Preview:</small><br>
                        <img id="imagePreview" src="#" width="200" class="rounded border border-success">
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.buildings.manage') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary flex-grow-1">Create Building</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Preview Script -->
<script>
    function previewImage(event) {
        var input = event.target;
        var imageField = document.getElementById("imagePreview");
        var container = document.getElementById("previewContainer");
        var file = input.files[0];

        // 1. If no file selected, hide preview
        if (!file) {
            container.style.display = "none";
            return;
        }

        // 2. VALIDATE FILE TYPE (Must be Image)
        if (!file.type.match('image.*')) {
            alert("Please select a valid image file (JPG, JPEG, PNG).");
            input.value = ""; // Clear input
            container.style.display = "none";
            return;
        }

        // 3. VALIDATE FILE SIZE (Max 2MB)
        var maxSize = 2 * 1024 * 1024; // 2MB in bytes
        if (file.size > maxSize) {
            alert("File is too large! Maximum size is 2MB.");
            input.value = ""; // Clear input
            container.style.display = "none";
            return;
        }

        // 4. If valid, show preview
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