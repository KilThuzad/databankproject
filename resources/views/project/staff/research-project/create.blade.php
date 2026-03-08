@extends('project.staff.layout.app')

@section('title', 'Add New Research Project')

@section('content')
<div class="container my-4">
    <h2>Add Research Project</h2>

@if($errors->any())
    <div class="alert alert-danger fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const alertBox = document.querySelector('.alert');
        if (alertBox) {
            setTimeout(() => {
                alertBox.classList.remove('show'); 
                setTimeout(() => {
                    alertBox.remove(); 
                    location.reload(); 
                }, 500);
            }, 3000);
        }
    });
</script>

    <form action="{{ route('userresearchproject.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Project Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">File (PDF, DOCX, etc.)</label>
                    <input type="file" name="file_path" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Team Assignment</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Project Leader</label>
                        <select name="leader_id" class="form-select" required>
                            <option value="">Select Leader</option>
                            @foreach($researchers as $researcher)
                                <option value="{{ $researcher->id }}" {{ old('leader_id') == $researcher->id ? 'selected' : '' }}>
                                    {{ $researcher->firstname }} {{ $researcher->lastname }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Project Adviser</label>
                        <select name="adviser_id" class="form-select" required>
                            <option value="">Select Adviser</option>
                            @foreach($advisers as $adviser)
                                <option value="{{ $adviser->id }}" {{ old('adviser_id') == $adviser->id ? 'selected' : '' }}>
                                    {{ $adviser->firstname }} {{ $adviser->lastname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Team Members (Select 2)</label>
                    <select name="member_ids[]" class="form-select" multiple required size="5">
                        @foreach($researchers as $researcher)
                            <option value="{{ $researcher->id }}" 
                                {{ in_array($researcher->id, (array)old('member_ids')) ? 'selected' : '' }}>
                                {{ $researcher->firstname }} {{ $researcher->lastname }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Hold Ctrl/Cmd to select multiple members</small>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <a href="{{ route('research_projects.index') }}" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-success">Submit Project</button>
        </div>
    </form>
</div>

<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        const memberSelect = document.querySelector('select[name="member_ids[]"]');
        if (memberSelect.selectedOptions.length !== 2) {
            e.preventDefault();
            alert('Please select exactly 2 team members');
            memberSelect.focus();
        }
    });
</script>
@endsection