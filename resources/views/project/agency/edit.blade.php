@extends('project.app')
@section('title', 'Edit Agency')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/agency.css') }}">
<div class="container-fluid py-4">

    <div class="row justify-content-center">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header" style="background-color: var(--primary-bg);">
                <h3 class="mb-0" style="color: var(--primary-dark);"><i class="fas fa-edit me-2"></i> Edit Agency</h3>
            </div>

            <div class="card-body">
                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form id="editAgencyForm" action="{{ route('member-agencies.update', $agency->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Name --}}
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" name="name" id="name" 
                               value="{{ old('name', $agency->name) }}" required>
                    </div>

                    {{-- Address --}}
                    <div class="mb-3">
                        <label for="address" class="form-label fw-bold">Address</label>
                        <input type="text" class="form-control" name="address" id="address" 
                               value="{{ old('address', $agency->address) }}">
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email</label>
                        <input type="email" class="form-control" name="email" id="email" 
                               value="{{ old('email', $agency->email) }}">
                    </div>

                    {{-- Logo --}}
                    <div class="mb-4">
                        <label for="logo" class="form-label fw-bold">Logo</label>
                        <input type="file" class="form-control" name="logo" id="logo">
                        @if($agency->logo)
                            <div class="mt-3">
                                <img src="{{ asset('storage/'.$agency->logo) }}" alt="Logo" 
                                     class="img-thumbnail" style="height: 100px; width: auto;">
                            </div>
                        @endif
                    </div>

                    {{-- Buttons --}}
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('member-agencies.index') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                        {{-- Update button triggers modal --}}
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmUpdateModal">
                            Update Agency
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Update Confirmation Modal --}}
<div class="modal fade" id="confirmUpdateModal" tabindex="-1" aria-labelledby="confirmUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmUpdateModalLabel">Confirm Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to update this agency?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmUpdateBtn">Confirm Update</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmUpdateBtn = document.getElementById('confirmUpdateBtn');
    const form = document.getElementById('editAgencyForm');

    confirmUpdateBtn.addEventListener('click', function() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('confirmUpdateModal'));
        if (modal) modal.hide();
        
        form.submit();
    });
});
</script>
@endpush

@endsection