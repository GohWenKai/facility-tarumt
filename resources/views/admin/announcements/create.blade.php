@extends('layouts.admin')

@section('content')
<style>
    .form-card { background: white; border-radius: 16px; border: 1px solid #e2e8f0; max-width: 700px; margin: 0 auto; }
    .form-header { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; padding: 1.5rem 2rem; border-radius: 16px 16px 0 0; }
    .form-header h1 { font-size: 1.5rem; font-weight: 700; margin: 0; }
    .form-body { padding: 2rem; }
    .form-group { margin-bottom: 1.5rem; }
    .form-label { font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; display: block; }
    .form-control, .form-select { border: 2px solid #e2e8f0; border-radius: 10px; padding: 0.75rem 1rem; }
    .form-control:focus, .form-select:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15); }
    .btn-submit { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 0.875rem 2rem; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35); }
    .btn-cancel { background: #f1f5f9; color: #64748b; padding: 0.875rem 2rem; border: none; border-radius: 10px; font-weight: 600; text-decoration: none; }
    .type-options { display: flex; gap: 1rem; flex-wrap: wrap; }
    .type-option { flex: 1; min-width: 100px; }
    .type-option input { display: none; }
    .type-option label { display: block; padding: 0.75rem; text-align: center; border: 2px solid #e2e8f0; border-radius: 10px; cursor: pointer; transition: all 0.2s; }
    .type-option input:checked + label { border-color: #3b82f6; background: #eff6ff; }
</style>

<div class="container-fluid py-4">
    <div class="form-card">
        <div class="form-header">
            <h1><i class="bi bi-megaphone-fill me-2"></i> Create Announcement</h1>
        </div>
        <div class="form-body">
            <form action="{{ route('admin.announcements.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                           value="{{ old('title') }}" placeholder="Announcement title..." required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Content</label>
                    <textarea name="content" rows="4" class="form-control @error('content') is-invalid @enderror" 
                              placeholder="Write your announcement here..." required>{{ old('content') }}</textarea>
                    @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Type</label>
                    <div class="type-options">
                        <div class="type-option">
                            <input type="radio" name="type" id="type-info" value="info" checked>
                            <label for="type-info"><i class="bi bi-info-circle text-primary"></i><br>Info</label>
                        </div>
                        <div class="type-option">
                            <input type="radio" name="type" id="type-warning" value="warning">
                            <label for="type-warning"><i class="bi bi-exclamation-triangle text-warning"></i><br>Warning</label>
                        </div>
                        <div class="type-option">
                            <input type="radio" name="type" id="type-success" value="success">
                            <label for="type-success"><i class="bi bi-check-circle text-success"></i><br>Success</label>
                        </div>
                        <div class="type-option">
                            <input type="radio" name="type" id="type-danger" value="danger">
                            <label for="type-danger"><i class="bi bi-x-circle text-danger"></i><br>Danger</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Priority</label>
                    <select name="priority" class="form-select">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Start Date (Optional)</label>
                            <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">End Date (Optional)</label>
                            <input type="datetime-local" name="ends_at" class="form-control" value="{{ old('ends_at') }}">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.announcements.index') }}" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-check-lg me-2"></i> Create Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
