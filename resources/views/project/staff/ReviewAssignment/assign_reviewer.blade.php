@extends('project.staff.layout.app')

@section('title', 'Assign Reviewers')

@section('content')
<div class="container my-5">

    {{-- Alerts --}}
    @if (session('error') || $errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="fas fa-exclamation-circle me-1"></i>

            @if (session('error'))
                {{ session('error') }}
            @endif

            @if ($errors->any())
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card rounded-4 shadow-sm">

        {{-- Header --}}
        <div class="card-header bg-danger bg-gradient text-white rounded-top-4">
            <h4 class="mb-0 fw-bold">
                <i class="fas fa-users-check me-2"></i>
                Assign Reviewers
            </h4>
        </div>

        {{-- Body --}}
        <div class="card-body p-4">

            <p class="text-muted mb-4">
                Assign one or more reviewers for the research project:
                <span class="fw-semibold text-dark">
                    {{ $project->title }}
                </span>
            </p>

            <form action="{{ route('staffreviewproject.storeReviewerAssignment', $project->id) }}" method="POST">
                @csrf

                {{-- Reviewers --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold text-danger mb-2">
                        <i class="fas fa-user-check me-1"></i> Select Reviewers
                    </label>

                    <div class="border rounded-3 p-3" style="max-height: 260px; overflow-y: auto;">
                        @foreach($reviewers as $reviewer)
                            <div class="form-check mb-2">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="reviewer_ids[]"
                                       value="{{ $reviewer->id }}"
                                       id="reviewer_{{ $reviewer->id }}">

                                <label class="form-check-label" for="reviewer_{{ $reviewer->id }}">
                                    <strong>
                                        {{ $reviewer->firstname }} {{ $reviewer->lastname }}
                                    </strong>
                                    <span class="text-muted small">
                                        ({{ $reviewer->email }})
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <small class="text-muted">
                        You may assign multiple reviewers to the same project.
                    </small>
                </div>

                {{-- Deadline --}}
                <div class="mb-4">
                    <label for="deadline" class="form-label fw-semibold text-danger">
                        <i class="fas fa-calendar-alt me-1"></i> Review Deadline
                    </label>
                    <input type="date"
                           class="form-control form-control-lg"
                           id="deadline"
                           name="deadline"
                           required>
                </div>

                {{-- Actions --}}
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('staffresearchproject.show', $project->id) }}"
                       class="btn btn-outline-secondary px-4">
                        <i class="fas fa-arrow-left me-1"></i> Cancel
                    </a>

                    <button type="submit" class="btn btn-danger px-4">
                        <i class="fas fa-check-circle me-1"></i>
                        Assign Reviewers & Approve
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
