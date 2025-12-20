@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.buildings.manage') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="page-title mb-1">
                    <i class="bi bi-pencil-square header-icon"></i>
                    Edit Building
                </h1>
                <p class="page-subtitle text-muted mb-0">Update details for <strong>{{ $building->name }}</strong></p>
            </div>
        </div>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="alert-card error mb-4">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Form Card -->
    <div class="card form-card">
        <div class="card-body p-4">
            <form action="{{ route('admin.buildings.update', $building->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Section: Basic Info -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="bi bi-building"></i></div>
                        <h3 class="section-title">Building Information</h3>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-3">
                            <label class="form-label-custom">Building ID</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="bi bi-hash"></i></span>
                                <input type="text" class="form-control-custom" value="{{ $building->id }}" disabled>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label-custom">Building Name <span class="required">*</span></label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="bi bi-building"></i></span>
                                <input type="text" name="name" class="form-control-custom" value="{{ $building->name }}" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label-custom">Location <span class="required">*</span></label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="bi bi-geo-alt-fill"></i></span>
                                <input type="text" name="location" class="form-control-custom" value="{{ $building->location }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Image -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon image"><i class="bi bi-image"></i></div>
                        <h3 class="section-title">Building Image</h3>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">Upload New Image</label>
                            <div class="image-upload-area">
                                <input type="file" name="image" id="imageInput" accept="image/png, image/jpeg, image/jpg" onchange="previewImage(event)">
                                <div class="upload-placeholder">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                    <span>Click or drag to upload</span>
                                    <small>JPG, PNG (Max 2MB) - Leave empty to keep current</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="image-previews">
                                @if($building->image_path)
                                <div class="preview-item current">
                                    <small>Current Image:</small>
                                    <img src="{{ asset('storage/' . $building->image_path) }}" alt="Current">
                                </div>
                                @endif
                                <div class="preview-item new" id="previewContainer" style="display: none;">
                                    <small>New Image:</small>
                                    <img id="imagePreview" src="#" alt="Preview">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('admin.buildings.manage') }}" class="btn btn-cancel">
                        <i class="bi bi-x-lg"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-submit">
                        <i class="bi bi-check-lg"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* ========================================
   EDIT BUILDING - PURPLE/AMBER THEME (Edit = Amber accent)
   ======================================== */

.page-header { border-bottom: 1px solid #e2e8f0; padding-bottom: 1.5rem; }
.btn-back { display: flex; align-items: center; justify-content: center; width: 42px; height: 42px; border-radius: 12px; background: #fef3c7; color: #d97706; text-decoration: none; transition: all 0.2s ease; }
.btn-back:hover { background: #fde68a; color: #92400e; }
.page-title { font-size: 1.75rem; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 0.75rem; }
.header-icon { color: #d97706; }

.alert-card { display: flex; align-items: flex-start; gap: 1rem; padding: 1rem 1.25rem; border-radius: 12px; }
.alert-card.error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }

.form-card { border: 1px solid #fde68a; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(217, 119, 6, 0.1); }
.form-section { padding: 1.5rem 0; border-bottom: 1px solid #fef3c7; }
.form-section:last-of-type { border-bottom: none; }
.section-header { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; }
.section-icon { display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%); color: white; font-size: 1rem; }
.section-icon.image { background: linear-gradient(135deg, #a78bfa 0%, #7c3aed 100%); }
.section-title { font-size: 1.1rem; font-weight: 600; color: #1e293b; margin: 0; }

.form-label-custom { display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem; }
.required { color: #ef4444; }
.input-group-custom { position: relative; display: flex; align-items: center; }
.input-icon { position: absolute; left: 14px; color: #d97706; font-size: 1rem; z-index: 1; }
.form-control-custom { width: 100%; padding: 0.875rem 1rem 0.875rem 2.75rem; border: 2px solid #fde68a; border-radius: 12px; font-size: 0.95rem; transition: all 0.2s ease; background: #fff; }
.form-control-custom:focus { outline: none; border-color: #f59e0b; box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1); }
.form-control-custom:disabled { background: #fef3c7; color: #92400e; }

/* Image Upload */
.image-upload-area { position: relative; border: 2px dashed #fde68a; border-radius: 12px; padding: 2rem; text-align: center; cursor: pointer; transition: all 0.2s ease; }
.image-upload-area:hover { border-color: #f59e0b; background: #fffbeb; }
.image-upload-area input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
.upload-placeholder { display: flex; flex-direction: column; align-items: center; gap: 0.5rem; color: #92400e; }
.upload-placeholder i { font-size: 2rem; color: #f59e0b; }
.image-previews { display: flex; gap: 1rem; flex-wrap: wrap; }
.preview-item { text-align: center; }
.preview-item small { display: block; font-weight: 600; color: #64748b; margin-bottom: 0.5rem; }
.preview-item img { width: 150px; height: 100px; object-fit: cover; border-radius: 8px; border: 2px solid #e2e8f0; }
.preview-item.new img { border-color: #10b981; }

/* Form Actions */
.form-actions { display: flex; justify-content: flex-end; gap: 1rem; padding-top: 2rem; margin-top: 1rem; border-top: 1px solid #fef3c7; }
.btn-cancel { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 1.5rem; border: 2px solid #e2e8f0; border-radius: 12px; background: white; color: #64748b; font-weight: 600; text-decoration: none; transition: all 0.2s ease; }
.btn-cancel:hover { border-color: #cbd5e1; background: #f8fafc; color: #475569; }
.btn-submit { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 2rem; border: none; border-radius: 12px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; font-weight: 600; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); }
.btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4); }
</style>

<script>
function previewImage(event) {
    var input = event.target;
    var imageField = document.getElementById("imagePreview");
    var container = document.getElementById("previewContainer");
    var file = input.files[0];
    if (!file) { container.style.display = "none"; return; }
    if (!file.type.match('image.*')) { alert("Please select a valid image file."); input.value = ""; container.style.display = "none"; return; }
    if (file.size > 2 * 1024 * 1024) { alert("File too large! Max 2MB."); input.value = ""; container.style.display = "none"; return; }
    var reader = new FileReader();
    reader.onload = function(){ if(reader.readyState == 2){ imageField.src = reader.result; container.style.display = "block"; }}
    reader.readAsDataURL(file);
}
</script>
@endsection