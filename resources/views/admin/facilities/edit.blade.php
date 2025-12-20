@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.facilities.manage') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="page-title mb-1">Edit Facility</h1>
                <p class="page-subtitle text-muted mb-0">Update details for <strong>{{ $facility->name }}</strong></p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card form-card">
        <div class="card-body p-4">
            <form action="{{ route('admin.facilities.update', $facility->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Section: Basic Info -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="bi bi-building"></i></div>
                        <h3 class="section-title">Basic Information</h3>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label-custom">Building <span class="text-muted">(Read Only)</span></label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="bi bi-geo-alt-fill"></i></span>
                                <input type="text" class="form-control-custom" value="{{ $facility->building->name ?? 'N/A' }}" disabled>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label-custom">Facility Name <span class="required">*</span></label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="bi bi-tag-fill"></i></span>
                                <input type="text" name="name" class="form-control-custom" value="{{ $facility->name }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-custom">Type <span class="required">*</span></label>
                            <div class="type-selector">
                                @foreach(['Lab', 'Room', 'Hall', 'Sports'] as $type)
                                <label class="type-option">
                                    <input type="radio" name="type" value="{{ $type }}" {{ $facility->type == $type ? 'checked' : '' }} required>
                                    <div class="type-card">
                                        <i class="bi bi-{{ $type == 'Lab' ? 'pc-display' : ($type == 'Room' ? 'door-open' : ($type == 'Hall' ? 'building' : 'dribbble')) }}"></i>
                                        <span>{{ $type }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-custom">Capacity <span class="required">*</span></label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="bi bi-people-fill"></i></span>
                                <input type="number" name="capacity" class="form-control-custom" value="{{ $facility->capacity }}" min="1" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Operating Hours -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon time"><i class="bi bi-clock"></i></div>
                        <h3 class="section-title">Operating Hours</h3>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">Start Time <span class="required">*</span></label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="bi bi-sunrise"></i></span>
                                <select name="start_time" class="form-control-custom" required>
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
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-custom">End Time <span class="required">*</span></label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="bi bi-sunset"></i></span>
                                <select name="end_time" class="form-control-custom" required>
                                    @foreach(range(8, 22) as $hour)
                                        @php 
                                            $timeFull = sprintf('%02d:00', $hour); 
                                            $timeHalf = sprintf('%02d:30', $hour);
                                            $savedEnd = \Carbon\Carbon::parse($facility->end_time)->format('H:i');
                                        @endphp
                                        @if($hour > 8) <option value="{{ $timeFull }}" {{ $savedEnd == $timeFull ? 'selected' : '' }}>{{ $timeFull }}</option> @endif
                                        @if($hour < 22) <option value="{{ $timeHalf }}" {{ $savedEnd == $timeHalf ? 'selected' : '' }}>{{ $timeHalf }}</option> @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Status -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon status"><i class="bi bi-toggles"></i></div>
                        <h3 class="section-title">Status & Image</h3>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">Availability Status <span class="required">*</span></label>
                            <div class="status-selector">
                                @foreach(['Available', 'Maintenance', 'Closed'] as $status)
                                <label class="status-option">
                                    <input type="radio" name="status" value="{{ $status }}" {{ $facility->status == $status ? 'checked' : '' }} required>
                                    <div class="status-card {{ strtolower($status) }}">
                                        <i class="bi bi-{{ $status == 'Available' ? 'check-circle-fill' : ($status == 'Maintenance' ? 'tools' : 'x-circle-fill') }}"></i>
                                        <span>{{ $status }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-custom">Facility Image</label>
                            <div class="image-upload-area">
                                <input type="file" name="image" id="imageInput" accept="image/png, image/jpeg, image/jpg" onchange="previewImage(event)">
                                <div class="upload-placeholder">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                    <span>Click or drag to upload</span>
                                    <small>JPG, PNG (Max 2MB)</small>
                                </div>
                            </div>
                            
                            <div class="image-previews mt-3">
                                @if($facility->image_path)
                                <div class="preview-item current">
                                    <small>Current:</small>
                                    <img src="{{ asset('storage/' . $facility->image_path) }}" alt="Current">
                                </div>
                                @endif
                                <div class="preview-item new" id="previewContainer" style="display: none;">
                                    <small>New:</small>
                                    <img id="imagePreview" src="#" alt="Preview">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('admin.facilities.manage') }}" class="btn btn-cancel">
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
.page-header { border-bottom: 1px solid #e2e8f0; padding-bottom: 1.5rem; }
.btn-back { display: flex; align-items: center; justify-content: center; width: 42px; height: 42px; border-radius: 12px; background: #f1f5f9; color: #64748b; text-decoration: none; transition: all 0.2s ease; }
.btn-back:hover { background: #e2e8f0; color: #1e293b; }
.page-title { font-size: 1.75rem; font-weight: 700; color: #0f172a; }
.form-card { border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
.form-section { padding: 1.5rem 0; border-bottom: 1px solid #f1f5f9; }
.form-section:last-of-type { border-bottom: none; }
.section-header { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; }
.section-icon { display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; font-size: 1rem; }
.section-icon.time { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.section-icon.status { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.section-title { font-size: 1.1rem; font-weight: 600; color: #1e293b; margin: 0; }
.form-label-custom { display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem; }
.required { color: #ef4444; }
.input-group-custom { position: relative; display: flex; align-items: center; }
.input-icon { position: absolute; left: 14px; color: #94a3b8; font-size: 1rem; z-index: 1; }
.form-control-custom { width: 100%; padding: 0.875rem 1rem 0.875rem 2.75rem; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 0.95rem; transition: all 0.2s ease; background: #fff; }
.form-control-custom:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
select.form-control-custom { cursor: pointer; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; background-size: 20px; padding-right: 40px; }

/* Type Selector */
.type-selector { display: flex; gap: 0.75rem; flex-wrap: wrap; }
.type-option { cursor: pointer; }
.type-option input { display: none; }
.type-card { display: flex; flex-direction: column; align-items: center; gap: 0.375rem; padding: 1rem 1.25rem; border: 2px solid #e2e8f0; border-radius: 12px; background: white; transition: all 0.2s ease; min-width: 80px; }
.type-card i { font-size: 1.5rem; color: #64748b; }
.type-card span { font-size: 0.8rem; font-weight: 500; color: #64748b; }
.type-option input:checked + .type-card { border-color: #3b82f6; background: #eff6ff; }
.type-option input:checked + .type-card i, .type-option input:checked + .type-card span { color: #1d4ed8; }

/* Status Selector */
.status-selector { display: flex; gap: 0.75rem; }
.status-option { cursor: pointer; flex: 1; }
.status-option input { display: none; }
.status-card { display: flex; flex-direction: column; align-items: center; gap: 0.375rem; padding: 1rem; border: 2px solid #e2e8f0; border-radius: 12px; background: white; transition: all 0.2s ease; text-align: center; }
.status-card i { font-size: 1.5rem; }
.status-card.available i { color: #10b981; }
.status-card.maintenance i { color: #f59e0b; }
.status-card.closed i { color: #ef4444; }
.status-option input:checked + .status-card.available { border-color: #10b981; background: #ecfdf5; }
.status-option input:checked + .status-card.maintenance { border-color: #f59e0b; background: #fffbeb; }
.status-option input:checked + .status-card.closed { border-color: #ef4444; background: #fef2f2; }

/* Image Upload */
.image-upload-area { position: relative; border: 2px dashed #e2e8f0; border-radius: 12px; padding: 2rem; text-align: center; cursor: pointer; transition: all 0.2s ease; }
.image-upload-area:hover { border-color: #3b82f6; background: #f8fafc; }
.image-upload-area input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
.upload-placeholder { display: flex; flex-direction: column; align-items: center; gap: 0.5rem; color: #64748b; }
.upload-placeholder i { font-size: 2rem; color: #94a3b8; }
.image-previews { display: flex; gap: 1rem; }
.preview-item { text-align: center; }
.preview-item small { display: block; font-weight: 600; color: #64748b; margin-bottom: 0.5rem; }
.preview-item img { width: 120px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid #e2e8f0; }
.preview-item.new img { border-color: #10b981; }

/* Form Actions */
.form-actions { display: flex; justify-content: flex-end; gap: 1rem; padding-top: 2rem; margin-top: 1rem; border-top: 1px solid #f1f5f9; }
.btn-cancel { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 1.5rem; border: 2px solid #e2e8f0; border-radius: 12px; background: white; color: #64748b; font-weight: 600; text-decoration: none; transition: all 0.2s ease; }
.btn-cancel:hover { border-color: #cbd5e1; background: #f8fafc; color: #475569; }
.btn-submit { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 2rem; border: none; border-radius: 12px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; font-weight: 600; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
.btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4); }
</style>

<script>
function previewImage(event) {
    var reader = new FileReader();
    var imageField = document.getElementById("imagePreview");
    var container = document.getElementById("previewContainer");
    reader.onload = function(){
        if(reader.readyState == 2){
            imageField.src = reader.result;
            container.style.display = "block";
        }
    }
    if(event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    } else {
        container.style.display = "none";
    }
}
</script>
@endsection