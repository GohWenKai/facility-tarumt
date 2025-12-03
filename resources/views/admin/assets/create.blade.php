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
        <div class="card-header bg-primary text-white">Add New Asset</div>
        <div class="card-body">
            <form action="{{ route('admin.assets.store') }}" method="POST">
                @csrf

                <!-- Row 1: Facility Selection -->
                <div class="mb-3">
                    <label class="form-label">Assign to Facility</label>
                    <select name="facility_id" class="form-select" required>
                        <option value="" selected disabled>Select a Facility</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Row 2: Name and Type -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Asset Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Epson Projector" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" required>
                            <option value="" selected disabled>Select Type</option>
                            <option value="Electronics">Electronics</option>
                            <option value="Furniture">Furniture</option>
                            <option value="Equipment">Equipment</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <!-- Row 3: Serial Number and Condition -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Serial Number</label>
                        <input type="text" name="serial_number" class="form-control" placeholder="e.g. SN-2025-001" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Condition</label>
                        <select name="condition" class="form-select" id="conditionSelect" required>
                            <option value="Good" selected>Good</option>
                            <option value="Fair">Fair</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Damaged">Damaged</option>
                            <option value="Retired">Retired</option>
                        </select>
                    </div>
                </div>

                <!-- Row 4: Maintenance Note -->
                <div class="mb-3" id="maintenanceNoteContainer" style="display: none;">
                    <label class="form-label">Maintenance Note (Optional)</label>
                    <textarea name="maintenance_note" id="maintenanceNoteInput" class="form-control" rows="2" placeholder="Describe the issue (e.g., 'Bulb blown', 'Sent for repair')">{{ $asset->maintenance_note??"" }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.assets.manage') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary flex-grow-1">Add Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const conditionSelect = document.getElementById('conditionSelect');
        const noteContainer = document.getElementById('maintenanceNoteContainer');
        const noteInput = document.getElementById('maintenanceNoteInput');

        function toggleNoteField() {
            const status = conditionSelect.value;
            
            // Logic: Show only if Maintenance OR Damaged
            if (status === 'Maintenance' || status === 'Damaged') {
                noteContainer.style.display = 'block';
                // Optional: make it required if visible
                noteInput.required = true; 
            } else {
                noteContainer.style.display = 'none';
                noteInput.required = false;
                // Optional: Clear text if hiding (prevents sending data for 'Good' items)
                noteInput.value = ''; 
            }
        }

        // Run on page load (in case "Maintenance" is selected after a validation error reload)
        toggleNoteField();

        // Run whenever the user changes the dropdown
        conditionSelect.addEventListener('change', toggleNoteField);
    });
</script>
@endsection