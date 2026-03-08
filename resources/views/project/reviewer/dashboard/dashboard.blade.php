@extends('project.reviewer.myapp')

@section('title', 'Dashboard')

@section('content')
<link rel="stylesheet" href="{{ asset('css/reviewer/dashboard.css') }}">

<div class="container-fluid px-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2">Dashboard</h1>
            <p class="mb-0 text-muted">Your Assigned Project Reviews & Analytics</p>
        </div>
        <div class="text-muted">
            <i class="fas fa-calendar-alt me-2"></i>
            {{ now()->format('l, F j, Y') }}
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <form action="{{ route('reviewer.dashboard.projects') }}" method="GET" class="d-flex gap-2 flex-wrap">

                <select class="form-select" name="member_agencies_id" style="width:auto;">
                    <option value="">All Member Agencies</option>
                    @foreach($memberAgencies as $agency)
                        <option value="{{ $agency->id }}" {{ (isset($memberAgencyId) && $memberAgencyId == $agency->id) ? 'selected' : '' }}>
                            {{ $agency->name }}
                        </option>
                    @endforeach
                </select>
                <div class="col-md-2">
                <select class="form-select" name="category" style="width:200px;">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ (isset($category) && $category == $cat) ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
</div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Apply
                </button>

                <a href="{{ route('reviewer.dashboard.projects') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>

            </form>
        </div>
    </div>

    <div class="row mb-4">
        @foreach([
            ['title'=>'Total Assigned Projects','value'=>$stats['total_projects'],'icon'=>'fa-project-diagram','color'=>'primary'],
            ['title'=>'Avg. Review Score','value'=>number_format($stats['average_score'],2),'icon'=>'fa-star','color'=>'success'],
            ['title'=>'Projects Approved','value'=>$stats['approved_projects'],'icon'=>'fa-check-circle','color'=>'info'],
            ['title'=>'Pending Reviews','value'=>$stats['pending_projects'],'icon'=>'fa-clock','color'=>'warning'],
        ] as $card)
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-{{ $card['color'] }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">
                                {{ $card['title'] }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                {{ $card['value'] }}
                            </div>
                        </div>
                        <div>
                            <i class="fas {{ $card['icon'] }} fa-2x text-muted"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>


    <div class="row">
        <div class="col-xl-12 mb-4">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0">Your Recent Reviews</h6>
                    <a href="{{ route('reviews.index') }}" class="btn btn-sm btn-danger">
                        <i class="fas fa-eye"></i> View All
                    </a>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>Score</th>
                                <th>Recommendation</th>
                                <th>Date Submitted</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentReviews as $review)
                            <tr data-project-id="{{ $review->project_id }}">
                                <td>
                                    <strong>PRJ-{{ str_pad($review->project_id,3,'0',STR_PAD_LEFT) }}</strong><br>
                                    <small class="text-muted">{{ $review->project->title ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $review->overall_score >= 8 ? 'success' : ($review->overall_score >= 6 ? 'warning' : 'danger') }}">
                                        {{ number_format($review->overall_score,2) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = ['accept'=>'success','revise'=>'warning','reject'=>'danger'][$review->recommendation] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">
                                        {{ ucfirst($review->recommendation) }}
                                    </span>
                                </td>
                                <td>{{ optional($review->submitted_at)->format('M d, Y') ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    No reviews assigned to you yet
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    new Chart(document.getElementById('scoreChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($stats['score_distribution'] ?? [])) !!},
            datasets: [{
                data: {!! json_encode(array_values($stats['score_distribution'] ?? [])) !!},
                backgroundColor: [
                    'rgba(220,38,38,0.7)',
                    'rgba(239,68,68,0.7)',
                    'rgba(248,113,113,0.7)',
                    'rgba(252,165,165,0.7)'
                ]
            }]
        },
        options: {
            responsive:true,
            plugins:{ legend:{ display:false } },
            scales:{ y:{ beginAtZero:true } }
        }
    });

    const categories = {!! json_encode($stats['projects_by_category'] ?? []) !!};

    new Chart(document.getElementById('categoryChart'), {
        type: 'pie',
        data: {
            labels: Object.keys(categories),
            datasets: [{
                data: Object.values(categories),
                backgroundColor:[
                    'rgba(220,38,38,0.7)',
                    'rgba(239,68,68,0.7)',
                    'rgba(248,113,113,0.7)',
                    'rgba(252,165,165,0.7)'
                ]
            }]
        },
        options:{
            responsive:true,
            plugins:{ legend:{ position:'bottom' } }
        }
    });

});
</script>
@endpush

@endsection
