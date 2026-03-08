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
                <form method="GET" action="{{ route('staffresearchproject.index') }}" class="mb-4">
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
    </div>

    {{-- Projects Card --}}
    <div class="card shadow-sm">
        <div class="card-header card-header-custom">
            <h5 class="mb-0">All Research Projects</h5>
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
                            <strong>Due: </strong>
                                {{ optional($project->deadline)->format('M d, Y h:i A') ?? 'No Due Date' }}
                        </small>
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

@endsection
