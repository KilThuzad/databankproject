@extends('project.app')

@section('title', 'Edit Research Project')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/researchproject.css') }}">

<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header" style="background-color: var(--primary-bg);">
            <h3 class="mb-0" style="color: var(--primary-dark);"><i class="fas fa-edit me-2"></i>Edit Research Project</h3>
        </div>
        <div class="card-body">

            {{-- Display Validation Errors --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('research_projects.update', $project->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Title --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control form-control-lg" value="{{ $project->title }}" required>
                </div>

                {{-- Description --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control" rows="5" placeholder="Enter project description..." required>{{ $project->description }}</textarea>
                </div>

                {{-- Category & Status --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->name }}" {{ $category->name == $project->category ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            @foreach(['pending_review', 'needs_revision', 'complete'] as $status)
                                <option value="{{ $status }}" {{ $project->status === $status ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Project File --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Project File</label>
                    <input type="file" name="file" class="form-control">
                    @if($project->file_path)
                        <div class="mt-2">
                            <small class="text-muted">
                                Current file: <strong>{{ basename($project->file_path) }}</strong>
                                <a href="{{ route('research_projects.download', $project->id) }}" class="btn btn-sm btn-outline-danger ms-2">
                                    <i class="fas fa-download me-1"></i> Download
                                </a>
                            </small>
                        </div>
                    @endif
                </div>

                {{-- Buttons --}}
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger"><i class="fas fa-save me-1"></i> Update Project</button>
                    <a href="{{ route('research_projects.index') }}" class="btn btn-outline-danger"><i class="fas fa-arrow-left me-1"></i> Cancel</a>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- Optional Custom CSS --}}
<style>
    .form-label { font-weight: 500; }
    .card-header h3 { font-size: 1.5rem; }
</style>
@endsection
