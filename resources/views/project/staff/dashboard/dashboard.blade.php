@extends('project.staff.layout.app')

@section('title', 'Dashboard')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('/css/staff/dashboard.css') }}">
<div class="container-fluid">
    {{-- FILTERS --}}
    <form method="GET" action="{{ route('staff.dashboard') }}" class="mb-4">
        <div class="row g-3">

            <div class="col-md-4">
                <label class="fw-bold">Filter by Agency</label>
                <select name="member_agencies_id" onchange="this.form.submit()" class="form-select form-select-sm">
                        <option value="">All Member Agencies</option>
                        @foreach($memberAgencies as $agency)
                            <option value="{{ $agency->id }}" {{ $memberAgencyId == $agency->id ? 'selected' : '' }}>
                                {{ $agency->name }}
                            </option>
                        @endforeach
                    </select>
            </div>

            <div class="col-md-4">
                <label class="fw-bold">Filter by Category</label>
                <select name="category" class="form-select" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    {{-- STATS --}}
    <div class="cards">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3>{{ $totalProjects }}</h3>
                    <p>Total Projects</p>
                </div>
                <div class="card-icon"><i class="fas fa-folder"></i></div>
            </div>
        </div>

        <div class="card attendance-card">
            <div class="card-header">
                <div>
                    <h3>On Duty</h3>
                    <p>Active Now</p>
                </div>
                <div class="card-icon"><i class="fas fa-calendar-check"></i></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <h3>{{ $approvedPercentage }}%</h3>
                    <p>Approved</p>
                </div>
                <div class="card-icon"><i class="fas fa-check"></i></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <h3>{{ $pendingPercentage }}%</h3>
                    <p>Pending</p>
                </div>
                <div class="card-icon"><i class="fas fa-clock"></i></div>
            </div>
        </div>
    </div>

    {{-- RECENT PROJECTS --}}
    <h4 class="section-title">
        <ion-icon name="time-outline"></ion-icon>
        Recent Projects
    </h4>

    @if($recentProjects->count())
        <div class="projects-list">
            @foreach($recentProjects as $project)
                <div class="project-item">
                    <div class="project-info">
                        <h6>{{ $project->title }}</h6>
                        <p>
                            <ion-icon name="person-outline"></ion-icon>
                            {{ $project->submittedBy->firstname }}
                            •
                            <ion-icon name="pricetag-outline"></ion-icon>
                            {{ $project->category ?? 'No Category' }}
                        </p>
                    </div>

                    <div class="project-meta">
                        <!-- <span class="project-status is_staff_approved-{{ $project->is_staff_approved }}">
                            {{ $project->is_staff_approved }}
                        </span> -->
                        <span class="project-date">
                            <ion-icon name="calendar-clear-outline"></ion-icon>
                            <strong>Due:</strong>
                            {{ optional($project->deadline)->format('M d, Y h:i A') ?? 'No Due Date' }}
                        </span>

                    </div>
                </div>
            @endforeach
        </div>

        <a href="{{ url('/staffresearchproject') }}" class="view-all-btn">
            View All Projects
        </a>
    @else
        <p class="text-muted">No projects found.</p>
    @endif

</div>

@endsection
