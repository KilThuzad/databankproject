@extends('project.myapp')

@section('title', 'Edit Research Project')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/researcher/style.css') }}">
@endpush
<div class="container my-4">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2" style="color: #333; font-weight: 700;">Edit Research Project</h1>
            <p class="text-muted mb-0">Update the project details below</p>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-left: 4px solid #e74a3b;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-3" style="font-size: 1.5rem;"></i>
                <div>
                    <h6 class="mb-1" style="font-weight: 600;">Please fix the following errors:</h6>
                    <ul class="mb-0 ps-3" style="list-style-type: disc;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Edit Form --}}
    <form id="editProjectForm" action="{{ route('userresearchproject.update', $project->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Project Details Card --}}
        <div class="card shadow-sm mb-4" style="border: none; border-radius: 8px;">
            <div class="card-header border-0 p-3" style="background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%); border-radius: 8px 8px 0 0;">
                <h5 class="mb-0 d-inline-flex align-items-center" style="color: white; font-weight: 600;">
                    <i class="fas fa-edit me-2"></i>
                    Project Details
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold" style="color: #2c3e50;">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ $project->title }}" required
                           style="border: 1px solid #ddd; border-radius: 6px; padding: 0.75rem;">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold" style="color: #2c3e50;">Description <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control" rows="4" required
                              style="border: 1px solid #ddd; border-radius: 6px; padding: 0.75rem;">{{ $project->description }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold" style="color: #2c3e50;">Category <span class="text-danger">*</span></label>
                    <select name="category" class="form-select" required
                            style="border: 1px solid #ddd; border-radius: 6px; padding: 0.75rem;">
                        @foreach ($categories as $category)
                            <option value="{{ $category->name }}" {{ $category->name == $project->category ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Project File Card --}}
        <div class="card shadow-sm mb-4" style="border: none; border-radius: 8px;">
            <div class="card-header border-0 p-3" style="background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%); border-radius: 8px 8px 0 0;">
                <h5 class="mb-0 d-inline-flex align-items-center" style="color: white; font-weight: 600;">
                    <i class="fas fa-file-upload me-2"></i>
                    Project File
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold" style="color: #2c3e50;">Project File</label>
                    <input type="file" name="file" class="form-control"
                           style="border: 1px solid #ddd; border-radius: 6px; padding: 0.75rem;">
                    @if($project->file_path)
                        <div class="mt-2">
                            <small class="text-muted d-block mb-1">Current file:</small>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-file" style="color: #e74a3b;"></i>
                                <span class="text-muted">{{ basename($project->file_path) }}</span>
                            </div>
                            <small class="text-muted">Leave empty to keep current file</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="d-flex justify-content-end gap-3 mb-4">
            <a href="{{ route('userresearchproject.index') }}" class="btn btn-lg" 
               style="background: #95a5a6; color: white; border: none; border-radius: 6px; padding: 0.75rem 1.5rem; font-weight: 500;">
                <i class="fas fa-times me-2"></i> Cancel
            </a>
            <button type="button" class="btn btn-lg" data-bs-toggle="modal" data-bs-target="#confirmUpdateModal"
                    style="background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%); color: white; border: none; border-radius: 6px; padding: 0.3rem 1.5rem; font-weight: 300;">
                <i class="fas fa-save me-2"></i> Update Project
            </button>
        </div>
    </form>
</div>

{{-- Confirmation Modal --}}
<div class="modal fade modal-top-center" id="confirmUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirm Update
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="mb-0">Are you sure you want to update this project?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmUpdateBtn">
                    <i class="fas fa-check me-1"></i> Yes, Update
                </button>
            </div>
        </div>
    </div>
</div>



@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editProjectForm');
    const confirmBtn = document.getElementById('confirmUpdateBtn');

    confirmBtn.addEventListener('click', function() {
        form.submit();
    });
});
</script>
@endpush

@endsection
