@extends('project.staff.layout.app')

@section('title', 'Research Projects')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/staff/project.css') }}">
@endpush


<div class="container-fluid my-4">
    {{-- Filters & Actions --}}
    <div class="row mb-3 align-items-center">
        <div class="col-md-6">
                <form method="GET" action="{{ route('staffresearchproject.approved') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Filter by University</label>
                            <select name="member_agencies_id" onchange="this.form.submit()" class="form-select form-select-sm">
                                <option value="">All Member Agencies</option>
                                @foreach($memberAgencies as $agency)
                                    <option value="{{ $agency->id }}" {{ $memberAgencyId == $agency->id ? 'selected' : '' }}>
                                        {{ $agency->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold">Filter by Category</label>
                            <select name="category" class="form-select" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                @foreach($Projectcategories as $cat)
                                    <option value="{{ $cat }}" {{ (isset($category) && $category == $cat) ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
        </div>

        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#bulkDeadlineModal">
                <i class="fas fa-calendar-plus me-1"></i> Set Deadlines
            </button>
        </div>

    </div>

    {{-- Projects Card --}}
    <div class="card shadow-sm">
        <div class="card-header card-header-custom">
            <h5 class="mb-0">All Approved Research Projects</h5>
        </div>
        
        <div class="card-body">
            @forelse($projects as $project)
                <div class="project-card d-flex justify-content-between align-items-center"
                     onclick="window.location='{{ route('staffresearchproject.show', $project->id) }}'">
                     
                    {{-- Left Section --}}
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">{{ $project->title }}</h6>
                        <small class="text-muted d-flex align-items-center gap-3">
                            <i class="fas fa-folder-open me-1"></i>
                            {{ $project->category ?? 'Uncategorized' }}
                        </small>
                    </div>

                    {{-- Right Section --}}
                    <div class="d-flex align-items-center gap-3">

                        {{-- Date --}}
                        <small class="text-muted d-flex align-items-center">
                            <i class="fas fa-calendar-alt text-danger me-1"></i>
                            <strong>Due:</strong>
                                {{ optional($project->deadline)->format('M d, Y h:i A') ?? 'No Due Date' }}
                        </small>

                        {{-- Action Dropdown --}}
                        <div class="dropdown" onclick="event.stopPropagation();">
                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li>
                                    <button type="button" class="dropdown-item edit-project-btn"
                                            data-id="{{ $project->id }}"
                                            data-status="{{ $project->status }}">
                                        <i class="fas fa-edit me-2 text-warning"></i> Edit
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted">No research projects found.</div>
            @endforelse

            {{-- Pagination --}}
            @if(method_exists($projects, 'links'))
                <div class="mt-3">{{ $projects->links() }}</div>
            @endif
        </div>
    </div>
</div>

{{-- Bulk Deadline Modal --}}
<div class="modal fade" id="bulkDeadlineModal" tabindex="-1" aria-labelledby="bulkDeadlineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <form action="{{ route('projects.bulkDeadlineByProject') }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Modal Header --}}
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold text-dark">
                            <i class="fas fa-calendar-plus text-danger me-2"></i>
                            Set Project Deadlines
                        </h5>
                        <small class="text-muted">
                            Apply deadlines to selected projects
                        </small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body pt-3">

                    {{-- Projects List --}}
                    <div class="card mb-3 border-0 shadow-sm rounded-3">
                        <div class="card-header bg-light fw-semibold">
                            Select Projects
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="selectAllProjects">
                                <label class="form-check-label fw-semibold" for="selectAllProjects">
                                    Select All Projects
                                </label>
                            </div>

                            <div class="row g-2 modal-project-list" style="max-height: 300px; overflow-y: auto;">
                                @foreach($projects as $project)
                                    <div class="col-md-6">
                                        <label class="d-flex align-items-center border rounded-3 px-3 py-2 bg-white project-item">
                                            <input type="checkbox"
                                                   name="projects[]"
                                                   value="{{ $project->id }}"
                                                   class="form-check-input me-2 project-checkbox">
                                            <span class="text-dark">{{ $project->title }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Deadline Card --}}
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-light fw-semibold">
                            Deadline Settings
                        </div>
                        <div class="card-body">
                            <label for="deadline" class="form-label fw-semibold">Deadline Date & Time</label>
                            <input type="datetime-local"
                                   name="deadline"
                                   id="deadline"
                                   class="form-control"
                                   required>

                            <small class="text-muted d-block mt-2">
                                The selected projects will share this deadline.
                            </small>
                        </div>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="fas fa-check me-1"></i> Save Deadline
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


{{-- Edit Project Modal --}}
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

                    {{-- Existing File --}}
                    <div class="mb-3" id="existingFileContainer" style="display:none;">
                        <label class="form-label fw-semibold">Existing File:</label>
                        <div>
                            <a href="" id="existingFileLink" target="_blank" class="text-primary text-decoration-underline"></a>
                        </div>
                    </div>

                    {{-- File Upload --}}
                    <div class="mb-3">
                        <label for="projectFile" class="form-label fw-semibold">Upload New File</label>
                        <input type="file" name="file" id="projectFile" class="form-control">
                        <small class="text-muted d-block mt-1">
                            Upload a new file to replace the existing one (optional).
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

@push('scripts')
<script>
    document.querySelectorAll('.edit-project-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const projectId = this.dataset.id;
            const status = this.dataset.status;
            const file = this.dataset.file ?? '';

            document.getElementById('editProjectId').value = projectId;
            document.getElementById('projectStatus').value = status;

            const existingFileContainer = document.getElementById('existingFileContainer');
            const existingFileLink = document.getElementById('existingFileLink');
            if (file) {
                existingFileContainer.style.display = 'block';
                existingFileLink.href = file;
                existingFileLink.textContent = file.split('/').pop(); 
            } else {
                existingFileContainer.style.display = 'none';
                existingFileLink.href = '';
                existingFileLink.textContent = '';
            }

            new bootstrap.Modal(document.getElementById('editProjectModal')).show();
        });
    });
</script>
@endpush

@push('scripts')
<script>
    document.getElementById('selectAllCategories').addEventListener('change', function() {
        document.querySelectorAll('.category-checkbox').forEach(cb => cb.checked = this.checked);
    });
</script>
@endpush
@endsection
