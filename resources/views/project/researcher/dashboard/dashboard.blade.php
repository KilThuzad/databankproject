@extends('project.researcher.layout.app')

@section('title', 'Researcher Dashboard')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/researcher/dashboard.css') }}">
@endpush

<div class="container-fluid p-4">
    {{-- ===== Dashboard Header ===== --}}
    <div class="dashboard-header">
        <h1>Dashboard</h1>
        <p>Projects Overview</p>
    </div>

    {{-- ===== Top Stat Cards ===== --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div>
                    <div class="stat-value">{{ $stats['total'] ?? 0 }}</div>
                    <div class="stat-label">Total Projects</div>
                </div>
                <i class="fas fa-folder-open stat-icon fs-1"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div>
                    <div class="stat-value">{{ $stats['revision'] ?? 0 }}</div>
                    <div class="stat-label">Needs Revision</div>
                </div>
                <i class="fas fa-calendar-check stat-icon fs-1"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div>
                    <div class="stat-value">{{ $stats['complete'] ?? 0 }}</div>
                    <div class="stat-label">completed</div>
                </div>
                <i class="fas fa-check stat-icon fs-1"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div>
                    <div class="stat-value">{{ $stats['pending'] ?? 0 }}</div>
                    <div class="stat-label">Pending</div>
                </div>
                <i class="fas fa-clock stat-icon fs-1"></i>
            </div>
        </div>
    </div>

    {{-- ===== Main Content Row ===== --}}
    <div class="row g-4">
        {{-- Recent Projects --}}
        <div class="col-lg-8">
            <div class="projects-card">
                <h5><i class="fas fa-clock me-2"></i> Recent Projects</h5>
                @forelse($recentProjects as $project)
                    <div class="project-item">
                        <div>
                            <div class="project-title">{{ $project->title ?? 'N/A' }}</div>
                            <div class="project-meta">
                                <span><i class="fas fa-tag"></i> {{ $project->category ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="project-due">
                            <i class="fas fa-calendar"></i>
                            <span>
                                Due: {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('M d, Y h:i A') : 'No Due Date' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-folder-open"></i>
                        <p>No projects found</p>
                    </div>
                @endforelse
                <a href="{{ route('userresearchproject.index') }}" class="btn-view-all">
                    <i class="fas fa-arrow-right me-1"></i> View All Projects
                </a>
            </div>
        </div>

        {{-- Upcoming Deadlines --}}
        <div class="col-lg-4">
            <div class="projects-card">
                <h5 class="fw-bold mb-3">Upcoming Events</h5>
                @forelse ($events as $deadline)
                    <div class="deadline-card">
                        <div class="deadline-title">{{ Str::limit($deadline->title, 50) }}</div>
                        <div class="deadline-date">
                            <i class="fas fa-calendar"></i>
                            {{ \Carbon\Carbon::parse($deadline->event_date)->format('M d, Y - h:i A') }}
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-calendar-alt"></i>
                        <p>No upcoming deadlines</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection