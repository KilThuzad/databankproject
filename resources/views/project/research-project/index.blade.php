@extends('project.app')

@section('title', 'Research Projects')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/researchproject.css') }}">
<style>
    .darkred-select {
        color: var(--primary-dark);
        border: 1px solid var(--primary-dark);
        border-radius: 6px;
        padding: 0.25rem 0.5rem;
    }

    .darkred-select option {
        background-color: white;   
        color: var(--dark);       
    }

    .darkred-select:focus {
        border-color: var(--primary-dark);
        outline: none;
        box-shadow: 0 0 5px rgba(153, 27, 27, 0.5);
    }

    .sortable {
        cursor: pointer;
        user-select: none;
    }

    .sortable i {
        font-size: 0.8rem;
        margin-left: 4px;
    }
</style>
@endpush

<div class="container-fluid my-4">
    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> Please check the form for errors.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-3 align-items-center">
        {{-- Filters --}}
        <div class="col-md-6">
            <form method="GET" action="{{ route('research_projects.index') }}" class="d-flex gap-2">
                <select name="member_agencies_id" onchange="this.form.submit()" class="form-select form-select-sm darkred-select">
                    <option value="">All Member Agencies</option>
                    @foreach($memberAgencies as $agency)
                        <option value="{{ $agency->id }}" @selected($memberAgencyId == $agency->id)>
                            {{ $agency->name }}
                        </option>
                    @endforeach
                </select>

                <select name="category" onchange="this.form.submit()" class="form-select form-select-sm darkred-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" @selected($category == $cat)>{{ $cat }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        {{-- Bulk Deadline Button --}}
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#bulkDeadlineModal">
                <i class="fas fa-calendar-plus me-1"></i> Set Deadlines
            </button>
        </div>
    </div>

    {{-- Projects Table --}}
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header" style="background-color: var(--primary-bg);">
            <h2 class="mb-0" style="color: var(--primary-dark);">
                <i class="fas fa-book me-2"></i> All Research Projects
            </h2>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" style="table-layout: fixed;">
                    <thead class="table-light">
                        <tr>
                            <th style="width:110px;">Project No.</th>
                            <th width="25%">Title</th>

                            {{-- Sortable Category --}}
                            <th width="20%" class="sortable"
                                onclick="window.location='{{ route('research_projects.index', array_merge(request()->all(), ['sort' => 'category', 'order' => (request('sort')=='category' && request('order')=='asc') ? 'desc' : 'asc'])) }}'">
                                Category
                                @if(request('sort') == 'category')
                                    <i class="fas fa-sort-{{ request('order') == 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </th>

                            <th width="10%">Status</th>

                            {{-- Sortable Deadline --}}
                            <th width="15%" class="sortable"
                                onclick="window.location='{{ route('research_projects.index', array_merge(request()->all(), ['sort' => 'deadline', 'order' => (request('sort')=='deadline' && request('order')=='asc') ? 'desc' : 'asc'])) }}'">
                                Deadline
                                @if(request('sort') == 'deadline')
                                    <i class="fas fa-sort-{{ request('order') == 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </th>
                            <th width="10%">Duration</th>
                            <th width="12%" class="text-center">Actions</th>
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
                            @endphp
                            <tr>
                                <td>
                                    <span class="badge bg-dark">
                                        {{ $projectNumber }}
                                    </span>
                                </td>
                                <td title="{{ $project->title }}">{{ Str::limit($project->title, 50) }}</td>
                                <td>{{ $project->category ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'complete' => 'var(--primary-dark)',
                                            'needs_revision' => 'var(--primary-red)',
                                            'pending_review' => 'var(--primary-light)',
                                        ];
                                        $color = $statusColors[$project->status] ?? '#858796';
                                    @endphp
                                    <span class="badge" style="background-color: {{ $color }}; color: white;">
                                        {{ Str::title(str_replace('_', ' ', $project->status)) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('M d, Y h:i A') : 'Not set' }}
                                </td>
                                <td>
                                    @if($project->deadline)
                                        @php
                                            $submitted = \Carbon\Carbon::parse($project->created_at);
                                            $deadline = \Carbon\Carbon::parse($project->deadline);
                                            $diffDays = $submitted->diffInDays($deadline);
                                        @endphp
                                        {{ $diffDays }} day{{ $diffDays > 1 ? 's' : '' }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 flex-wrap justify-content-center">
                                        <a href="{{ route('research_projects.show', $project->id) }}" 
                                           class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('research_projects.edit', $project->id) }}" 
                                           class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Delete button triggers modal --}}
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteProjectModal"
                                                data-form-id="delete-project-form-{{ $project->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        {{-- Hidden delete form --}}
                                        <form id="delete-project-form-{{ $project->id }}" 
                                              action="{{ route('research_projects.destroy', $project->id) }}" 
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
                                <td colspan="6" class="text-center py-4">No research projects found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if(method_exists($projects, 'links'))
                <div class="mt-3">
                    {{ $projects->appends(request()->all())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Bulk Deadline Modal --}}
<div class="modal fade" id="bulkDeadlineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-3">
            <form id="bulkDeadlineForm" action="{{ route('projects.bulkDeadline') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header" style="color: var(--primary-dark); background-color: var(--primary-bg);">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-alt me-2"></i> Set Deadlines
                    </h5> 
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button> 
                </div>

                <div class="modal-body">
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="selectAllProjects">
                        <label class="form-check-label fw-bold" for="selectAllProjects">Select All Projects</label>
                    </div>

                    <div class="list-group mb-3 overflow-auto" style="max-height: 300px;">
                        @foreach($projects as $project)
                            <label class="list-group-item d-flex align-items-center">
                                <input type="checkbox" name="project_ids[]" value="{{ $project->id }}" 
                                       class="form-check-input me-2 project-checkbox">
                                <span>{{ $project->title }} <small class="text-muted">({{ $project->category }})</small></span>
                            </label>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <label for="deadline" class="form-label fw-bold">Deadline</label>
                        <input type="datetime-local" name="deadline" id="deadline" class="form-control" required>
                        <div class="form-text">The selected projects will be updated with this deadline.</div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="openConfirmBulkModal">
                        <i class="fas fa-check me-1"></i> Save Deadlines
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Confirm Bulk Deadline Modal --}}
<div class="modal fade" id="confirmBulkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Bulk Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>You are about to set the deadline for <strong id="selectedCount">0</strong> selected project(s) to:</p>
                <p class="text-danger fw-bold" id="confirmDeadline"></p>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmBulkSubmit">Confirm & Update</button>
            </div>
        </div>
    </div>
</div>

{{-- Delete Project Confirmation Modal --}}
<div class="modal fade" id="deleteProjectModal" tabindex="-1" aria-labelledby="deleteProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteProjectModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this research project? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteProject">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAllProjects');
    const checkboxes = document.querySelectorAll('.project-checkbox');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        });
    }

    const openConfirmBtn = document.getElementById('openConfirmBulkModal');
    if (openConfirmBtn) {
        const bulkModalElement = document.getElementById('bulkDeadlineModal');
        const bulkModal = bootstrap.Modal.getInstance(bulkModalElement) || new bootstrap.Modal(bulkModalElement);
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmBulkModal'));
        const confirmSubmitBtn = document.getElementById('confirmBulkSubmit');
        const deadlineInput = document.getElementById('deadline');
        const selectedCountSpan = document.getElementById('selectedCount');
        const confirmDeadlineSpan = document.getElementById('confirmDeadline');

        let pendingData = null;

        openConfirmBtn.addEventListener('click', function() {
            const selected = Array.from(checkboxes).filter(cb => cb.checked);
            const deadline = deadlineInput.value;

            if (selected.length === 0) {
                alert('Please select at least one project.');
                return;
            }
            if (!deadline) {
                alert('Please set a deadline.');
                return;
            }

            pendingData = {
                selectedCount: selected.length,
                deadline: deadline,
                formattedDeadline: new Date(deadline).toLocaleString(undefined, {
                    year: 'numeric', month: 'long', day: 'numeric',
                    hour: '2-digit', minute: '2-digit'
                })
            };

            bulkModal.hide();
        });

        bulkModalElement.addEventListener('hidden.bs.modal', function() {
            if (pendingData) {
                selectedCountSpan.textContent = pendingData.selectedCount;
                confirmDeadlineSpan.textContent = pendingData.formattedDeadline;
                confirmModal.show();
            }
        });

        confirmSubmitBtn.addEventListener('click', function() {
            document.getElementById('bulkDeadlineForm').submit();
        });

        document.getElementById('confirmBulkModal').addEventListener('hidden.bs.modal', function() {
            pendingData = null;
        });
    }

    const deleteModal = document.getElementById('deleteProjectModal');
    if (deleteModal) {
        const confirmDeleteBtn = document.getElementById('confirmDeleteProject');
        let activeDeleteFormId = null;

        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            activeDeleteFormId = button.getAttribute('data-form-id');
        });

        confirmDeleteBtn.addEventListener('click', function() {
            if (activeDeleteFormId) {
                const form = document.getElementById(activeDeleteFormId);
                if (form) {
                    form.submit();
                }
            }
        });

        deleteModal.addEventListener('hidden.bs.modal', function() {
            activeDeleteFormId = null;
        });
    }
});
</script>

@endsection