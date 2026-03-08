@extends('project.reviewer.myapp')

@section('title', 'Assigned Research Projects')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/reviewer/style.css') }}">

@endpush

@section('content')
<div class="container-fluid my-4">

    {{-- 🔹 Header & Filters --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <form method="GET" action="{{ route('reviewer.Researches') }}" class="d-flex gap-2">
            <select name="member_agencies_id" onchange="this.form.submit()" class="form-select form-select-sm filter-select">
                <option value="">All Agencies</option>
                @foreach($memberAgencies as $agency)
                    <option value="{{ $agency->id }}" {{ ($memberAgencyId ?? '') == $agency->id ? 'selected' : '' }}>
                        {{ $agency->name }}
                    </option>
                @endforeach
            </select>

            <select name="category" onchange="this.form.submit()" class="form-select form-select-sm filter-select">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ ($category ?? '') == $cat ? 'selected' : '' }}>
                        {{ $cat }}
                    </option>
                @endforeach
            </select>
        </form>

        <h5 class="page-title mb-0">
            <i class="fas fa-book-open me-1"></i> Assigned Research Projects
        </h5>
    </div>

    {{-- 🔹 Table Card --}}
    <div class="card card-custom">
        <div class="card-header card-header-custom">
            <h6 class="mb-0 text-light">Projects Awaiting Review</h6>
        </div>

        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:110px;">Project No.</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Assignment</th>
                            <th>Project Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php
                        $categoryCounters = [];
                    @endphp

                    @forelse($assignedProjects as $project)

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
                            <td class="fw-semibold">
                                {{ Str::limit($project->title, 55) }}
                            </td>

                            <td>
                                <span class="text-muted">{{ $project->category ?? 'N/A' }}</span>
                            </td>

                            {{-- Assignment Status --}}
                            <td>
                                @php
                                    $statusClasses = [
                                        'assigned' => 'badge-assigned',
                                        'in_review' => 'badge-in_review',
                                        'completed' => 'badge-completed'
                                    ];
                                    $assignmentClass = $statusClasses[$project->assignment_status] ?? 'badge-pending_review';
                                @endphp
                                <span class="badge {{ $assignmentClass }}">
                                    {{ Str::title(str_replace('_',' ',$project->assignment_status)) }}
                                </span>
                            </td>

                            {{-- Project Status --}}
                            <td>
                                @php
                                    $projectClasses = [
                                        'complete' => 'badge-completed',
                                        'approved' => 'badge-completed',
                                        'needs_revision' => 'badge-needs_revision',
                                        'pending_review' => 'badge-pending_review',
                                        'in_review' => 'badge-in_review'
                                    ];
                                    $projectClass = $projectClasses[$project->project_status] ?? 'badge-secondary';
                                @endphp
                                <span class="badge {{ $projectClass }}">
                                    {{ Str::title(str_replace('_',' ',$project->project_status)) }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="text-center">
                                <a href="{{ route('reviewer.Researches.show',$project->project_id) }}"
                                   class="btn btn-sm btn-outline-danger btn-icon">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                No assigned research projects found.
                            </td>
                        </tr>
                    @endforelse

                    </tbody>
                </table>
            </div>

            @if(method_exists($assignedProjects,'links'))
                <div class="mt-3">
                    {{ $assignedProjects->links() }}
                </div>
            @endif
        </div>
    </div>
</div>


@endsection
