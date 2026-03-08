@extends('project.staff.layout.app')

@section('title', 'Events')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .card-header {
        font-weight: 600;
        background-color: #f8f9fa !important;
        border-bottom: 1px solid #dee2e6;
    }
    .table-hover tbody tr:hover {
        background-color: #f1f3f5;
    }
    .btn-outline-primary, .btn-outline-secondary, .btn-outline-danger {
        border-radius: 6px;
        padding: 0.375rem 0.75rem;
    }
    .badge {
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 6px;
        padding: 0.35em 0.65em;
    }
</style>
@endpush

{{-- resources/views/partials/alerts.blade.php --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="container-fluid my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">Events</h1>
        <a href="{{ route('staffevents.create') }}" class="btn btn-danger rounded-3">
            <i class="fas fa-plus me-1"></i> Create Event
        </a>
    </div>

    <div class="card shadow-sm rounded-4 border-0">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <h2 class="h5 mb-0 text-center fw-semibold">All Events</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('staffevents.calendar') }}" class="btn btn-sm btn-outline-secondary rounded-3">
                    <i class="far fa-calendar-alt me-1"></i> Calendar View
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="20%">Event Title</th>
                            <th width="30%">Description</th>
                            <th width="25%">Date & Time</th>
                            <th width="10%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($events as $event)
                        <tr>
                            <td>
                                <h6 class="mb-1 fw-semibold">{{ $event->title }}</h6>
                                @if($event->location)
                                <small class="text-muted">{{ $event->location }}</small>
                                @endif
                            </td>
                            <td>
                                <p class="text-muted mb-0">{{ Str::limit($event->description, 50) }}</p>
                            </td>
                            <td>
                                @php
                                    $eventDate = $event->event_date instanceof \Carbon\Carbon 
                                        ? $event->event_date 
                                        : \Carbon\Carbon::parse($event->event_date);
                                @endphp
                                <div class="d-flex flex-column">
                                    <span class="text-muted">{{ $eventDate->format('l, jS F Y') }}</span>
                                    <span class="text-secondary fw-semibold">{{ $eventDate->format('g:i A') }}</span>
                                    @if($eventDate->isPast())
                                    <span class="badge bg-secondary mt-1 align-self-start">Past Event</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('staffevents.show', $event->id) }}" 
                                       class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('staffevents.edit', $event->id) }}" 
                                       class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="far fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger delete-event" 
                                            data-url="{{ route('staffevents.destroy', $event->id) }}"
                                            title="Delete">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                No events found. <a href="{{ route('staffevents.create') }}" class="text-decoration-underline">Create your first event</a>.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Confirm Delete Modal --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-sm">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-semibold">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this event? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-3">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
 document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
    const deleteForm = document.getElementById('deleteForm');

    document.querySelectorAll('.delete-event').forEach(button => {
        button.addEventListener('click', function() {
            deleteForm.action = this.dataset.url; 
            deleteModal.show();
        });
    });
});
</script>
@endpush
@endsection
