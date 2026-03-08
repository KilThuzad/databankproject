@extends('project.staff.layout.app')

@section('title', 'Assign Reviewer')

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


    <div class="card rounded-4">

        {{-- Header --}}
        <div class="card-header bg-danger bg-gradient text-white rounded-top-4">
            <h4 class="mb-0 fw-bold">
                <i class="fas fa-user-check me-2"></i>
                Assign Reviewer
            </h4>
        </div>

        {{-- Body --}}
        <div class="card-body p-4">

            <p class="text-muted mb-4">
                Assign a reviewer for the research project:
                <span class="fw-semibold text-dark">
                    {{ $project->title }}
                </span>
            </p>

            <form action="{{ route('staffreviewproject.storeReviewerAssignment', $project->id) }}" method="POST">
                @csrf

                {{-- Reviewer --}}
                <div class="mb-4">
                    <label for="reviewer_id" class="form-label fw-semibold text-danger">
                        <i class="fas fa-user me-1"></i> Reviewer
                    </label>
                    <select class="form-select form-select-lg" id="reviewer_id" name="reviewer_id" required>
                        <option value="">-- Choose Reviewer --</option>
                        @foreach($reviewers as $reviewer)
                            <option value="{{ $reviewer->id }}">
                                {{ $reviewer->firstname }} {{ $reviewer->lastname }} ({{ $reviewer->email }})
                            </option>
                        @endforeach
                    </select>
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
                        Assign Reviewer & Approve
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
