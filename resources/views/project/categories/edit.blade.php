@extends('project.app')

@section('title', 'Edit Category')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/category.css') }}">
@endpush

<div class="container my-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            {{-- Card --}}
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header" style="background-color: var(--primary-bg);">
                    <h3 class="mb-0" style="color: var(--primary-dark);">
                        <i class="fas fa-edit me-2"></i> Edit Category
                    </h3>
                </div>
                <div class="card-body">
                    <form id="editCategoryForm" action="{{ route('categories.update', $category->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Category Name --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $category->name) }}" 
                                   placeholder="Enter category name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Enter category description (optional)">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Buttons --}}
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Categories
                            </a>
                            {{-- Update button triggers modal --}}
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#updateConfirmModal">
                                <i class="fas fa-save"></i> Update Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Update Confirmation Modal --}}
<div class="modal fade" id="updateConfirmModal" tabindex="-1" aria-labelledby="updateConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateConfirmModalLabel">Confirm Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to update this category?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                {{-- Hidden submit button to trigger form submission --}}
                <button type="button" class="btn btn-danger" id="confirmUpdate">Yes, Update</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmUpdateBtn = document.getElementById('confirmUpdate');
    const form = document.getElementById('editCategoryForm');

    confirmUpdateBtn.addEventListener('click', function() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('updateConfirmModal'));
        if (modal) modal.hide();
        
        form.submit();
    });
});
</script>
@endpush
@endsection