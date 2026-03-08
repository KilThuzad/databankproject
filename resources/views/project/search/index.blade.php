@extends('project.myapp')

@section('title', 'Research Projects')

@section('content')
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

<div class="container my-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">All Research Projects</h2>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="30%">Title</th>
                                    <th width="20%">Category</th>
                                    <th width="10%">Status</th>
                                    <th width="15%">Date</th>
                                    <th width="13%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($projects as $project)
                                    <tr>
                                        <td>{{ Str::limit($project->title, 50) }}</td>
                                        <td>{{ $project->category ?? 'N/A' }}</td>
                                        <td class="text-light">
                                           @php
                                                $statusClasses = [
                                                    'complete'       => 'bg-success',
                                                    'needs_revision'  => 'bg-danger',
                                                    'pending_review'  => 'bg-warning', 
                                                ];

                                                $statusClass = $statusClasses[$project->status] ?? 'bg-secondary';
                                            @endphp
                                            <span class="badge {{ $statusClass }}">
                                                {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $project->status)) }}
                                            </span>

                                        </td>
                                        <td>{{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y, h:i A') : 'Not set' }}</td>
                                        <td class="text-center">
                                            <div class="d-flex gap-1 flex-wrap">
                                                <a href="{{ route('userresearchproject.show', $project->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="View Project Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <a href="{{ route('userresearchproject.edit', $project->id) }}" 
                                                   class="btn btn-sm btn-outline-warning"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <form action="{{ route('userresearchproject.destroy', $project->id) }}" method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this project?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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
                    
                    {{-- Only show pagination if it's a paginator instance --}}
                    @if(method_exists($projects, 'links'))
                        <div class="mt-3">
                            {{ $projects->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection