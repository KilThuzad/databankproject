@extends('project.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/agency.css') }}">
<div class="container-fluid py-4">
    <div class="row justify-content-center">
            <div class="card shadow-sm border-0 rounded-3">
                
                <div class="card-header" style="background-color: var(--primary-bg);">
                    <h4 class="mb-0" style="color: var(--primary-dark);">
                        <i class="fas fa-building me-2"></i> Add Member Agency
                    </h4>
                </div>
                <div class="card-body">

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('member-agencies.store') }}" 
                          method="POST" 
                          enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">Agency Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="name" 
                                   class="form-control form-control-lg" 
                                   value="{{ old('name') }}" 
                                   placeholder="Enter agency name"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Address</label>
                            <input type="text" 
                                   name="address" 
                                   class="form-control form-control-lg" 
                                   value="{{ old('address') }}" 
                                   placeholder="Enter agency address">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" 
                                   name="email" 
                                   class="form-control form-control-lg" 
                                   value="{{ old('email') }}" 
                                   placeholder="Enter agency email">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Agency Logo</label>
                            <input type="file" 
                                   name="logo" 
                                   class="form-control form-control-lg" 
                                   accept="image/*">
                            <small class="text-muted">Supported formats: jpg, png, gif. Max size: 2MB</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('member-agencies.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-save me-1"></i> Save Agency
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
