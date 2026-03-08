@extends('project.staff.layout.app')

@section('title', 'Edit Research Project')

@section('content')
<div class="container-fluid my-4">
    <h2>Edit Research Project</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
<div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <form action="{{ route('staffresearchproject.updateStatusFile') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="hidden" name="project_id" id="editProjectId">

                {{-- Modal Header --}}
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold text-dark">
                            <i class="fas fa-edit text-warning me-2"></i>
                            Edit Project
                        </h5>
                        <small class="text-muted">
                            Update project status and upload new file
                        </small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body pt-3">

                    {{-- Status --}}
                    <div class="mb-3">
                        <label for="projectStatus" class="form-label fw-semibold">Project Status</label>
                        <select name="status" id="projectStatus" class="form-select" required>
                            <option value="pending_review">Pending Review</option>
                            <option value="needs_revision">Needs Revision</option>
                            <option value="complete">Approved</option>
                        </select>
                    </div>

                    {{-- File Upload --}}
                    <div class="mb-3">
                        <label for="projectFile" class="form-label fw-semibold">Upload File</label>
                        <input type="file" name="file" id="projectFile" class="form-control">
                        <small class="text-muted d-block mt-1">
                            Upload a new file for this project (optional).
                        </small>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-warning px-4">
                        <i class="fas fa-check me-1"></i> Save Changes
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection
