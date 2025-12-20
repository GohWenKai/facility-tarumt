@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.assets.manage') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="page-title mb-1">Edit Asset</h1>
                <p class="page-subtitle text-muted mb-0">Update details for <strong>{{ $asset->name }}</strong></p>
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
            <form action="{{ route('admin.assets.update', $asset->id) }}" method="POST" id="editAssetForm">
                @csrf
                @method('PUT')

                <!-- Section: Assignment -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <h3 class="section-title">Facility Assignment</h3>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label-custom">Assigned Facility <span class="required">*</span></label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="bi bi-geo-alt-fill"></i></span>
                                <select name="facility_id" class="form-control-custom" required>
                                    @foreach($facilities as $facility)
                                        <option value="{{ $facility->id }}" {{ $asset->facility_id == $facility->id ? 'selected' : '' }}>
                                            {{ $facility->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Asset Details -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <h3 class="section-title">Asset Details</h3>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">Asset Name <span class="required">*</span></label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="bi bi-tag-fill"></i></span>
                                <input type="text" name="name" class="form-control-custom" value="{{ $asset->name }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-custom">Type <span class="required">*</span></label>
                            <div class="type-selector">
                                <label class="type-option">
                                    <input type="radio" name="type" value="Equipment" {{ $asset->type == 'Equipment' ? 'checked' : '' }} required>
                                    <div class="type-card">
                                        <i class="bi bi-projector-fill"></i>
                                        <span>Equipment</span>
                                    </div>
                                </label>
                                <label class="type-option">
                                    <input type="radio" name="type" value="Furniture" {{ $asset->type == 'Furniture' ? 'checked' : '' }}>
                                    <div class="type-card">
                                        <i class="bi bi-lamp-fill"></i>
                                        <span>Furniture</span>
                                    </div>
                                </label>
                                <label class="type-option">
                                    <input type="radio" name="type" value="Electronics" {{ $asset->type == 'Electronics' ? 'checked' : '' }}>
                                    <div class="type-card">
                                        <i class="bi bi-display-fill"></i>
                                        <span>Electronics</span>
                                    </div>
                                </label>
                                <label class="type-option">
                                    <input type="radio" name="type" value="Other" {{ $asset->type == 'Other' ? 'checked' : '' }}>
                                    <div class="type-card">
                                        <i class="bi bi-box-fill"></i>
                                        <span>Other</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-custom">Serial Number <span class="required">*</span></label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="bi bi-upc-scan"></i></span>
                                <input type="text" name="serial_number" class="form-control-custom bg-light" value="{{ $asset->serial_number }}" readonly required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Condition -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon condition">
                            <i class="bi bi-heart-pulse"></i>
                        </div>
                        <h3 class="section-title">Condition Status</h3>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label-custom">Current Condition <span class="required">*</span></label>
                            <div class="condition-selector">
                                <label class="condition-option">
                                    <input type="radio" name="condition" value="Good" id="condGood" {{ $asset->condition == 'Good' ? 'checked' : '' }} required>
                                    <div class="condition-card good">
                                        <i class="bi bi-check-circle-fill"></i>
                                        <span class="condition-name">Good</span>
                                        <span class="condition-desc">Working perfectly</span>
                                    </div>
                                </label>
                                <label class="condition-option">
                                    <input type="radio" name="condition" value="Fair" id="condFair" {{ $asset->condition == 'Fair' ? 'checked' : '' }}>
                                    <div class="condition-card fair">
                                        <i class="bi bi-dash-circle-fill"></i>
                                        <span class="condition-name">Fair</span>
                                        <span class="condition-desc">Minor wear</span>
                                    </div>
                                </label>
                                <label class="condition-option">
                                    <input type="radio" name="condition" value="Maintenance" id="condMaint" {{ $asset->condition == 'Maintenance' ? 'checked' : '' }}>
                                    <div class="condition-card maintenance">
                                        <i class="bi bi-tools"></i>
                                        <span class="condition-name">Maintenance</span>
                                        <span class="condition-desc">Needs repair</span>
                                    </div>
                                </label>
                                <label class="condition-option">
                                    <input type="radio" name="condition" value="Damaged" id="condDamaged" {{ $asset->condition == 'Damaged' ? 'checked' : '' }}>
                                    <div class="condition-card damaged">
                                        <i class="bi bi-x-circle-fill"></i>
                                        <span class="condition-name">Damaged</span>
                                        <span class="condition-desc">Not usable</span>
                                    </div>
                                </label>
                                <label class="condition-option">
                                    <input type="radio" name="condition" value="Retired" id="condRetired" {{ $asset->condition == 'Retired' ? 'checked' : '' }}>
                                    <div class="condition-card retired">
                                        <i class="bi bi-archive-fill"></i>
                                        <span class="condition-name">Retired</span>
                                        <span class="condition-desc">Decommissioned</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Maintenance Note (Conditional) -->
                        <div class="col-12" id="maintenanceNoteContainer" style="display: none;">
                            <div class="maintenance-note-card">
                                <label class="form-label-custom">
                                    <i class="bi bi-chat-left-text"></i> Maintenance Note <span class="required">*</span>
                                </label>
                                <textarea name="maintenance_note" id="maintenanceNoteInput" class="form-control-custom" rows="3" 
                                          placeholder="Describe the issue (e.g., 'Bulb blown', 'Sent for repair on Dec 15')">{{ $asset->maintenance_note }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('admin.assets.manage') }}" class="btn btn-cancel">
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
/* Page Header */
.page-header {
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 1.5rem;
}

.btn-back {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: #f1f5f9;
    color: #64748b;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-back:hover {
    background: #e2e8f0;
    color: #1e293b;
}

.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #0f172a;
}

.page-subtitle {
    font-size: 0.95rem;
}

/* Alert Card */
.alert-card {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem 1.25rem;
    border-radius: 12px;
}

.alert-card.error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
}

.alert-card.error i {
    font-size: 1.25rem;
    margin-top: 2px;
}

/* Form Card */
.form-card {
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

/* Form Sections */
.form-section {
    padding: 1.5rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.form-section:last-of-type {
    border-bottom: none;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.section-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    font-size: 1rem;
}

.section-icon.condition {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

/* Form Controls */
.form-label-custom {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.required {
    color: #ef4444;
}

.input-group-custom {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 14px;
    color: #94a3b8;
    font-size: 1rem;
    z-index: 1;
}

.form-control-custom {
    width: 100%;
    padding: 0.875rem 1rem 0.875rem 2.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    background: #ffffff;
}

.form-control-custom:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

select.form-control-custom {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 20px;
    padding-right: 40px;
}

/* Type Selector */
.type-selector {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.type-option {
    cursor: pointer;
}

.type-option input {
    display: none;
}

.type-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.375rem;
    padding: 1rem 1.25rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    background: white;
    transition: all 0.2s ease;
    min-width: 90px;
}

.type-card i {
    font-size: 1.5rem;
    color: #64748b;
}

.type-card span {
    font-size: 0.8rem;
    font-weight: 500;
    color: #64748b;
}

.type-option input:checked + .type-card {
    border-color: #3b82f6;
    background: #eff6ff;
}

.type-option input:checked + .type-card i,
.type-option input:checked + .type-card span {
    color: #1d4ed8;
}

/* Condition Selector */
.condition-selector {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.condition-option {
    cursor: pointer;
    flex: 1;
    min-width: 140px;
}

.condition-option input {
    display: none;
}

.condition-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.375rem;
    padding: 1.25rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    background: white;
    transition: all 0.2s ease;
    text-align: center;
}

.condition-card i {
    font-size: 1.75rem;
}

.condition-name {
    font-weight: 600;
    font-size: 0.9rem;
}

.condition-desc {
    font-size: 0.75rem;
    color: #94a3b8;
}

.condition-card.good { border-color: #d1fae5; }
.condition-card.good i { color: #10b981; }
.condition-option input:checked + .condition-card.good {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border-color: #10b981;
}

.condition-card.fair { border-color: #dbeafe; }
.condition-card.fair i { color: #3b82f6; }
.condition-option input:checked + .condition-card.fair {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-color: #3b82f6;
}

.condition-card.maintenance { border-color: #fef3c7; }
.condition-card.maintenance i { color: #f59e0b; }
.condition-option input:checked + .condition-card.maintenance {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    border-color: #f59e0b;
}

.condition-card.damaged { border-color: #fecaca; }
.condition-card.damaged i { color: #ef4444; }
.condition-option input:checked + .condition-card.damaged {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    border-color: #ef4444;
}

.condition-card.retired { border-color: #e2e8f0; }
.condition-card.retired i { color: #64748b; }
.condition-option input:checked + .condition-card.retired {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-color: #64748b;
}

/* Maintenance Note */
.maintenance-note-card {
    background: #fef3c7;
    border: 1px solid #fde68a;
    border-radius: 12px;
    padding: 1.25rem;
}

.maintenance-note-card .form-label-custom {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #92400e;
}

.maintenance-note-card textarea.form-control-custom {
    padding-left: 1rem;
    background: white;
}

/* Form Actions */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    padding-top: 2rem;
    margin-top: 1rem;
    border-top: 1px solid #f1f5f9;
}

.btn-cancel {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 1.5rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    background: white;
    color: #64748b;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-cancel:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
    color: #475569;
}

.btn-submit {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 2rem;
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
}

/* Responsive */
@media (max-width: 768px) {
    .type-selector, .condition-selector {
        flex-direction: column;
    }
    
    .condition-option {
        min-width: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const conditionInputs = document.querySelectorAll('input[name="condition"]');
    const noteContainer = document.getElementById('maintenanceNoteContainer');
    const noteInput = document.getElementById('maintenanceNoteInput');

    function toggleNoteField() {
        const selected = document.querySelector('input[name="condition"]:checked');
        if (selected && (selected.value === 'Maintenance' || selected.value === 'Damaged')) {
            noteContainer.style.display = 'block';
            noteInput.required = true;
        } else {
            noteContainer.style.display = 'none';
            noteInput.required = false;
        }
    }

    // Run on page load
    toggleNoteField();

    // Run whenever condition changes
    conditionInputs.forEach(input => {
        input.addEventListener('change', toggleNoteField);
    });

    // ==========================================
    // Auto-Generate Serial Number Logic (Edit Mode)
    // ==========================================
    // ==========================================
    // Auto-Generate Serial Number Logic (Edit Mode)
    // ==========================================
    // ==========================================
    // Auto-Generate Serial Number Logic (Edit Mode)
    // ==========================================
    // Note: Type is a set of radio buttons, not a select
    const typeInputs = document.querySelectorAll('input[name="type"]');
    const serialInput = document.querySelector('input[name="serial_number"]');

    if(typeInputs.length > 0 && serialInput) {
        // Store the initial checked value to allow reverting
        let previousValue = document.querySelector('input[name="type"]:checked')?.value;

        typeInputs.forEach(input => {
            // Function to handle the change
            input.addEventListener('change', function() {
                const selectedType = this.value;
                
                if(selectedType) {
                    if(!confirm('Changing the type will generate a NEW serial number. Continue?')) {
                        // User clicked CANCEL: Revert to previous value
                        // We must uncheck check current and re-check previous
                        this.checked = false;
                        const prevInput = document.querySelector(`input[name="type"][value="${previousValue}"]`);
                        if(prevInput) {
                            prevInput.checked = true; 
                        }
                        return; 
                    }
                    
                    // User clicked OK: Update previous value for next time
                    previousValue = selectedType;

                    // Show loading state
                    serialInput.value = 'Generating...';

                    fetch(`{{ route('admin.assets.next_serial') }}?type=${selectedType}`)
                        .then(response => {
                            if (!response.ok) throw new Error('Network response not ok');
                            return response.json();
                        })
                        .then(data => {
                            if(data.serial_number) {
                                serialInput.value = data.serial_number;
                            }
                        })
                        .catch(err => {
                            console.error('Error fetching serial:', err);
                            serialInput.value = 'Error generating serial';
                        });
                }
            });
        });
    }
});
</script>
@endsection