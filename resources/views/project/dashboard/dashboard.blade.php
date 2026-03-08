@extends('project.app')

@section('title', 'Home')

@section('content')

@push('style')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
@endpush
<style>
:root {
    --primary-red: #dc2626;
    --primary-dark: #991b1b;
    --primary-light: #fecaca;
    --primary-bg: #fef2f2;
}

body {
    background-color: #f8fafc;
}

.section-title {
    color: var(--primary-dark);
}

.dashboard-card {
    border-radius: 0.75rem;
    border: none;
    transition: transform .2s ease, box-shadow .2s ease;
}

.dashboard-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 20px rgba(0,0,0,.08);
}

.table thead th {
    font-weight: 600;
    color: #6b7280;
    border-bottom: none;
}

.table tbody td {
    vertical-align: middle;
}

.table-hover tbody tr {
    transition: background-color .15s ease;
}

.badge {
    padding: .45em .65em;
    font-weight: 600;
    letter-spacing: .02em;
}

.chart-container {
    height: 350px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    h2, h5 {
        font-size: 1rem;
    }
}
</style>

<div class="container-fluid p-4">

    {{-- FILTER --}}
    <div class="card dashboard-card shadow-sm mb-4">
        <div class="card-body bg-light">
            <form method="GET" action="{{ route('dashboard.projects') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="fw-bold">Filter by Agency</label>
                        <select name="member_agencies_id" class="form-select" onchange="this.form.submit()">
                            <option value="">All Agencies</option>
                            @foreach($memberAgencies as $agency)
                                <option value="{{ $agency->id }}" @selected($memberAgencyId == $agency->id)>
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
                                <option value="{{ $cat }}" @selected($category == $cat)>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="row g-4 mb-4">
        @php
            $stats = [
                ['count' => $totalAccounts, 'label' => 'Total Accounts', 'icon' => 'people-outline'],
                ['count' => $totalProjects, 'label' => 'Total Projects', 'icon' => 'book-outline'],
                ['items' => $accountsByRole, 'label' => 'Accounts by Role', 'icon' => 'person-circle-outline'],
                ['items' => $projectsByStatus, 'label' => 'Projects by Status', 'icon' => 'stats-chart-outline'],
            ];
        @endphp

        @foreach($stats as $stat)
            <div class="col-md-6 col-lg-3">
                <div class="card dashboard-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <div>
                                @isset($stat['count'])
                                    <h2 class="fw-bold">{{ $stat['count'] }}</h2>
                                @endisset
                                <small class="text-muted">{{ $stat['label'] }}</small>
                            </div>
                            <ion-icon name="{{ $stat['icon'] }}" style="font-size:2rem;color:var(--primary-red)"></ion-icon>
                        </div>

                        @isset($stat['items'])
                            @foreach($stat['items'] as $key => $value)
                                <div class="d-flex justify-content-between small">
                                    <span>{{ ucfirst($key) }}</span>
                                    <strong>{{ $value }}</strong>
                                </div>
                            @endforeach
                        @endisset
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- CHARTS --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card dashboard-card shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Research Count by Category</h5>
                    <div class="chart-container">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card dashboard-card shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Project Status</h5>
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RECENT PROJECTS & EVENTS --}}
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card dashboard-card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <ion-icon name="book-outline"></ion-icon> Recent Projects
                    </h5>
                    <a href="{{ route('research_projects.index') }}" class="btn btn-sm btn-danger">View All</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width:110px;">Project No.</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Agency</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $categoryCounters = [];
                            @endphp
                            @forelse($recentProjects as $project)
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
                                <td>{{ Str::limit($project->title, 40) }}</td>
                                <td>{{ $project->category ?? 'N/A' }}</td>
                                <td>{{ $project->user->memberAgency->name ?? 'N/A' }}</td>
                                <td class="text-center">
                                    @php
                                        $statusColors = [
                                            'pending_review' => 'bg-warning text-dark',
                                            'needs_revision' => 'bg-danger',
                                            'complete'       => 'bg-success',
                                        ];
                                        $statusClass = $statusColors[$project->status] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $statusClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No recent projects</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card dashboard-card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="fw-bold mb-0">
                        <ion-icon name="calendar-outline"></ion-icon> Upcoming Events
                    </h5>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($events as $event)
                    <div class="list-group-item">
                        <strong>{{ $event->title }}</strong><br>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y g:i A') }}
                        </small>
                    </div>
                    @empty
                    <div class="list-group-item text-center text-muted">No upcoming events</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rawLabels = @json($projectsByCategory->keys()->values() ?? []);
    const rawData = @json($projectsByCategory->values()->values() ?? []);

    const minItems = 5;
    const paddedLabels = [...rawLabels];
    const paddedData = [...rawData];
    while (paddedLabels.length < minItems) {
        paddedLabels.push('');
        paddedData.push(0);
    }

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(@json($projectsByStatus)),
            datasets: [{
                data: Object.values(@json($projectsByStatus)),
                backgroundColor: ['#dc2626', '#f59e0b', '#b91c1c', '#6b7280']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    new Chart(document.getElementById('categoryChart'), {
        type: 'bar',
        data: {
            labels: paddedLabels,
            datasets: [{
                label: 'Number of Projects',
                data: paddedData,
                backgroundColor: '#dc2626',
                borderRadius: 6,
                categoryPercentage: 0.6,
                barPercentage: 1.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: '#666',
                        autoSkip: false,
                        callback: function(value) {
                            const label = this.getLabelForValue(value);
                            return label && label.length > 10 ? label.substring(0, 10) + '…' : label;
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    suggestedMin: 5,
                    ticks: { precision: 0, color: '#666' },
                    grid: { color: '#f0f0f0' }
                }
            },
            plugins: { legend: { display: false } }
        }
    });
});
</script>
@endsection