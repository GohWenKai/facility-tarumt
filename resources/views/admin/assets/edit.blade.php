@extends('layouts.admin')

@section('content')
<div class="container">
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
        <div class="card-header bg-warning text-dark">Edit Asset: {{ $asset->name }}</div>
        <div class="card-body">
            <form action="{{ route('admin.assets.update', $asset->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Row 1: Facility Selection -->
                <div class="mb-3">
                    <label class="form-label">Assigned Facility</label>
                    <select name="facility_id" class="form-select" required>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->id }}" {{ $asset->facility_id == $facility->id ? 'selected' : '' }}>
                                {{ $facility->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Row 2: Name and Type -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Asset Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $asset->name }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" required>
                            <option value="Electronics" {{ $asset->type == 'Electronics' ? 'selected' : '' }}>Electronics</option>
                            <option value="Furniture" {{ $asset->type == 'Furniture' ? 'selected' : '' }}>Furniture</option>
                            <option value="Equipment" {{ $asset->type == 'Equipment' ? 'selected' : '' }}>Equipment</option>
                            <option value="Other" {{ $asset->type == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>

                <!-- Row 3: Serial Number and Condition -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Serial Number</label>
                        <input type="text" name="serial_number" class="form-control" value="{{ $asset->serial_number }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Condition</label>
                        <select name="condition" class="form-select" id="conditionSelect" required>
                            <option value="Good" {{ $asset->condition == 'Good' ? 'selected' : '' }}>Good</option>
                            <option value="Fair" {{ $asset->condition == 'Fair' ? 'selected' : '' }}>Fair</option>
                            <option value="Maintenance" {{ $asset->condition == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="Damaged" {{ $asset->condition == 'Damaged' ? 'selected' : '' }}>Damaged</option>
                            <option value="Retired" {{ $asset->condition == 'Retired' ? 'selected' : '' }}>Retired</option>
                        </select>
                    </div>
                </div>

                <!-- Row 4: Maintenance Note -->
                <div class="mb-3" id="maintenanceNoteContainer" style="display: none;">
                    <label class="form-label">Maintenance Note (Optional)</label>
                    <textarea name="maintenance_note" id="maintenanceNoteInput" class="form-control" rows="2" placeholder="Describe the issue (e.g., 'Bulb blown', 'Sent for repair')">{{ $asset->maintenance_note }}</textarea>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.assets.manage') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary flex-grow-1">Update Changes</button>
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
            }
        }

        // Run on page load (in case "Maintenance" is selected after a validation error reload)
        toggleNoteField();

        // Run whenever the user changes the dropdown
        conditionSelect.addEventListener('change', toggleNoteField);
    });
</script>
@endsection