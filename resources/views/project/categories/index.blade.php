@extends('project.app')

@section('title', 'Categories')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/category.css') }}">
@endpush

<div class="container-fluid my-4">
    {{-- Success Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="page-title">
            <i class="fas fa-tags"></i> Categories List
        </h2>
        <a href="{{ route('categories.create') }}" class="btn btn-danger btn-sm">
            <i class="fas fa-plus"></i> Add New Category
        </a>
    </div>     

    {{-- Categories Table --}}
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="30%">Category Name</th>
                            <th width="55%">Description</th>    
                            <th width="5%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>
                                    @php
                                        $name = $category->name ?? 'N/A';
                                        $words = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY);
                                        
                                        if (count($words) >= 2) {
                                            $code = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                                        } else {
                                            $code = strtoupper(substr($name, 0, 2));
                                        }
                                        
                                        $displayCode = $code . '-' . $category->id;
                                    @endphp
                                    {{ $displayCode }}
                                </td>
                                <td>{{ $category->name ?? 'N/A' }}</td>
                                <td>{{ $category->description ?? 'No description' }}</td>
                                <td class="text-center">
                                    <div class="btn-group gap-1" role="group">
                                        {{-- View Button triggers modal --}}
                                        <button type="button" class="btn btn-sm btn-outline-info" title="View"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewModal"
                                                data-category-name="{{ $category->name }}"
                                                data-category-description="{{ $category->description }}"
                                                data-category-code="{{ $displayCode }}">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Delete Button triggers modal --}}
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal"
                                                data-form-id="delete-form-{{ $category->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        {{-- Hidden Delete Form --}}
                                        <form id="delete-form-{{ $category->id }}" 
                                              action="{{ route('categories.destroy', $category->id) }}" 
                                              method="POST" 
                                              class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- View Category Modal --}}
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Category Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Code:</strong> <span id="view-category-code"></span>
                </div>
                <div class="mb-3">
                    <strong>Name:</strong> <span id="view-category-name"></span>
                </div>
                <div class="mb-3">
                    <strong>Description:</strong>
                    <p id="view-category-description" class="mb-0"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this category? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    const viewModal = document.getElementById('viewModal');
    if (viewModal) {
        viewModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const name = button.getAttribute('data-category-name');
            const description = button.getAttribute('data-category-description');
            const code = button.getAttribute('data-category-code');
            
            document.getElementById('view-category-code').textContent = code;
            document.getElementById('view-category-name').textContent = name;
            document.getElementById('view-category-description').textContent = description || 'No description';
        });
    }

    const deleteModal = document.getElementById('deleteModal');
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    let activeFormId = null;

    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            activeFormId = button.getAttribute('data-form-id');
        });

        confirmDeleteBtn.addEventListener('click', function() {
            if (activeFormId) {
                const form = document.getElementById(activeFormId);
                if (form) {
                    form.submit();
                }
            }
        });

        deleteModal.addEventListener('hidden.bs.modal', function() {
            activeFormId = null;
        });
    }
});
</script>
@endpush
@endsection