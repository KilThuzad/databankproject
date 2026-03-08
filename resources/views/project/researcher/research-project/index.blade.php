@extends('project.researcher.layout.app')

@section('title', 'Research Projects')

@section('content')
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/researcher/project.css') }}">
@endpush

<div class="container-fluid my-4">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Research Projects</h2>
            <p class="text-muted mb-0">Manage your research projects</p>
        </div>
        <div>
            <a href="{{ route('userresearchproject.create') }}" class="btn btn-danger">
                <i class="fas fa-plus me-1"></i> Add Project
            </a>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Projects Table --}}
    <div class="card">
        <div class="card-header bg-danger text-light">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                All Projects 
                @if(method_exists($projects, 'total'))
                    ({{ $projects->total() }})
                @else
                    ({{ $projects->count() }})
                @endif
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:110px;">Project No.</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th style="width: 100px;">Status</th>
                            <th>Approval Status</th>
                            <th style="width: 130px;" class="text-center">Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $categoryCounters = [];
                        @endphp

                        @forelse($projects as $project)
                            @php
                                    // Generate category prefix
                                $categoryName = $project->category ?? 'GEN';

                                // Get initials (Country Development → CD)
                                $prefix = collect(explode(' ', $categoryName))
                                            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                                            ->join('');

                                // Initialize counter for category
                                if (!isset($categoryCounters[$prefix])) {
                                    $categoryCounters[$prefix] = 1;
                                } else {
                                    $categoryCounters[$prefix]++;
                                }

                                $projectNumber = $prefix . '-' . $categoryCounters[$prefix];

                                $statusColors = [
                                    'complete' => 'success',
                                    'needs_revision' => 'danger',
                                    'pending_review' => 'warning',
                                ];

                                $approvalColors = [
                                    'pending' => 'secondary',
                                    'approved' => 'success',
                                    'in_progress' => 'warning',
                                    'complete' => 'primary',
                                ];

                                $isLocked = in_array($project->is_staff_approved, ['approved', 'in_progress', 'complete']);
                            @endphp

                            <tr>
                                 <td>
                                    <span class="badge bg-dark">
                                        {{ $projectNumber }}
                                    </span>
                                </td>

                                <td>{{ Str::limit($project->title, 50) }}</td>

                                <td class="text-nowrap">
                                    <span class="badge bg-light text-dark">
                                        {{ $project->category ?? 'N/A' }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-{{ $statusColors[$project->status] ?? 'secondary' }}">
                                        {{ Str::title(str_replace('_', ' ', $project->status)) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-{{ $approvalColors[$project->is_staff_approved] ?? 'secondary' }}">
                                        {{ Str::title(str_replace('_', ' ', $project->is_staff_approved)) }}
                                    </span>
                                </td>

                                <td class="text-center text-nowrap">
                                    @if($project->deadline)
                                        {{ \Carbon\Carbon::parse($project->deadline)->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="btn-group btn-group-sm gap-2" role="group">
                                        {{-- View --}}
                                        <a href="{{ route('userresearchproject.show', $project->id) }}"
                                           class="btn btn-outline-primary"
                                           title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- Edit --}}
                                        <a href="{{ $isLocked ? '#' : route('userresearchproject.edit', $project->id) }}"
                                           class="btn btn-outline-warning {{ $isLocked ? 'disabled' : '' }}"
                                           title="{{ $isLocked ? 'Editing disabled after staff approval' : 'Edit' }}"
                                           @if($isLocked) aria-disabled="true" @endif>
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Delete --}}
                                        @if(!$isLocked)
                                            <button type="button"
                                                    class="btn btn-outline-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    data-project-id="{{ $project->id }}"
                                                    data-project-title="{{ $project->title }}"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-outline-danger disabled"
                                                    title="Deletion disabled after staff approval">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-folder-open fa-2x mb-2"></i>
                                        <p class="mb-0">No projects found</p>
                                        <small>Click "Add Project" to create your first project</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if(method_exists($projects, 'hasPages') && $projects->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $projects->firstItem() }} to {{ $projects->lastItem() }} of {{ $projects->total() }} projects
                    </div>
                    <div>
                        {{ $projects->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Delete Confirmation Modal (Top Center) --}}
<div class="modal fade modal-top-center" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content shadow">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirm Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center py-4">
                <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                <h5 class="fw-bold">Delete Project</h5>
                <p class="mb-0">
                    Are you sure you want to delete
                    <strong id="projectTitle"></strong>?
                </p>
                <small class="text-muted">This action cannot be undone.</small>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>

                <form id="deleteForm" action="/userresearchproject/ID" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">
                        <span class="btn-text">
                            <i class="fas fa-trash me-1"></i> Delete Project
                        </span>
                        <span class="btn-spinner d-none">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            Deleting...
                        </span>
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    const deleteForm = document.getElementById('deleteForm');
    const deleteBtn = document.getElementById('confirmDeleteBtn');

    deleteForm.addEventListener('submit', function () {
        deleteBtn.querySelector('.btn-text').classList.add('d-none');
        deleteBtn.querySelector('.btn-spinner').classList.remove('d-none');
        deleteBtn.disabled = true;
    });

    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const projectId = button.getAttribute('data-project-id');
        const projectTitle = button.getAttribute('data-project-title');

        const modalTitle = deleteModal.querySelector('#projectTitle');
        modalTitle.textContent = projectTitle;

        deleteForm.action = `/userresearchproject/${projectId}`;
    });
</script>
@endpush

@endsection
