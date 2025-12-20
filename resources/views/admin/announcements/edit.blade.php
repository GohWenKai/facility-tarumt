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
            <h1><i class="bi bi-pencil-fill me-2"></i> Edit Announcement</h1>
        </div>
        <div class="form-body">
            <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                           value="{{ old('title', $announcement->title) }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Content</label>
                    <textarea name="content" rows="4" class="form-control @error('content') is-invalid @enderror" required>{{ old('content', $announcement->content) }}</textarea>
                    @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Type</label>
                    <div class="type-options">
                        @foreach(['info', 'warning', 'success', 'danger'] as $type)
                        <div class="type-option">
                            <input type="radio" name="type" id="type-{{ $type }}" value="{{ $type }}" 
                                   {{ $announcement->type == $type ? 'checked' : '' }}>
                            <label for="type-{{ $type }}">
                                <i class="bi bi-{{ $type == 'info' ? 'info-circle text-primary' : ($type == 'warning' ? 'exclamation-triangle text-warning' : ($type == 'success' ? 'check-circle text-success' : 'x-circle text-danger')) }}"></i><br>{{ ucfirst($type) }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Priority</label>
                    <select name="priority" class="form-select">
                        <option value="low" {{ $announcement->priority == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ $announcement->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ $announcement->priority == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" 
                               {{ $announcement->is_active ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">Active</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Start Date</label>
                            <input type="datetime-local" name="starts_at" class="form-control" 
                                   value="{{ $announcement->starts_at ? $announcement->starts_at->format('Y-m-d\TH:i') : '' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">End Date</label>
                            <input type="datetime-local" name="ends_at" class="form-control" 
                                   value="{{ $announcement->ends_at ? $announcement->ends_at->format('Y-m-d\TH:i') : '' }}">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.announcements.index') }}" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-check-lg me-2"></i> Update Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
