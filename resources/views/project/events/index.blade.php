@extends('project.app')

@section('title', 'Events')

@section('content')
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin/event.css') }}">
@endpush

<div class="container-fluid p-4">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h1 class="h2 mb-0">Events</h1>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('events.create') }}" class="btn btn-danger">
                <i class="fas fa-plus me-1"></i> Create Event
            </a>
            <a href="{{ route('events.calendar') }}" class="btn btn-outline-secondary">
                <i class="far fa-calendar-alt me-1"></i> Calendar View
            </a>
        </div>
    </div>

    {{-- Events Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: var(--primary-bg)">
            <h2 class="h5 mb-0" style="color: var(--primary-dark)">All Events</h2>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="20%">Event Title</th>
                            <th width="35%">Description</th>
                            <th width="25%">Date & Time</th>
                            <th width="20%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($events as $event)
                        <tr>
                            <td>
                                <h6 class="mb-0">{{ $event->title }}</h6>
                                @if($event->location)
                                <small class="text-muted">{{ $event->location }}</small>
                                @endif
                            </td>
                            <td>
                                <p class="text-muted mb-0">{{ Str::limit($event->description, 70) }}</p>
                            </td>
                            <td>
                                @php
                                    $eventDate = $event->event_date instanceof \Carbon\Carbon 
                                        ? $event->event_date 
                                        : \Carbon\Carbon::parse($event->event_date);
                                @endphp
                                <div class="d-flex flex-column">
                                    <span class="text-muted">{{ $eventDate->format('l, jS F Y') }}</span>
                                    <span class="text-danger">{{ $eventDate->format('g:i A') }}</span>
                                    @if($eventDate->isPast())
                                    <span class="badge bg-secondary mt-1 align-self-start">Past Event</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('events.show', $event->id) }}" 
                                       class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('events.edit', $event->id) }}" 
                                       class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="far fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger delete-event" 
                                            title="Delete"
                                            data-id="{{ $event->id }}">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                No events found. <a href="{{ route('events.create') }}">Create your first event</a>.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this event? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
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
                const eventId = this.dataset.id;
                deleteForm.action = `/events/${eventId}`;
                deleteModal.show();
            });
        });

        const searchInput = document.getElementById('searchInput');
        const searchBtn = document.getElementById('searchBtn');

        function performSearch() {
            const query = searchInput.value.trim();
            if(query) {
                window.location.href = `{{ route('events.index') }}?search=${encodeURIComponent(query)}`;
            } else {
                window.location.href = `{{ route('events.index') }}`;
            }
        }

        searchBtn.addEventListener('click', performSearch);
        searchInput.addEventListener('keypress', function(e) {
            if(e.key === 'Enter') performSearch();
        });
    });
</script>
@endpush
@endsection
