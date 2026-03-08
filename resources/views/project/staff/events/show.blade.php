@extends('project.staff.layout.app')

@section('title', 'Event Details')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Event Details</h1>
        <a href="{{ route('staffevents.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Events
        </a>
    </div>

    <div class="card">
        <div class="card-header bg-white border-0">
            <h2 class="h5 mb-0">{{ $event->title }}</h2>
        </div>
        <div class="card-body">
            @php
                $eventDate = \Carbon\Carbon::parse($event->event_date);
            @endphp

            <div class="row mb-3">
                <div class="col-md-4">
                    <h6 class="text-muted">Title</h6>
                    <p class="mb-0">{{ $event->title }}</p>
                </div>
                <div class="col-md-8">
                    <h6 class="text-muted">Scheduled Date</h6>
                    <p class="mb-0">
                        <i class="far fa-calendar-alt me-2"></i> {{ $eventDate->format('l, F d, Y') }}
                        <span class="mx-2">•</span>
                        <i class="far fa-clock me-2"></i> {{ $eventDate->format('h:i A') }}
                    </p>
                </div>
            </div>

            <div class="mb-3">
                <h6 class="text-muted">Description</h6>
                <div class="p-3 bg-light rounded">
                    @if($event->description)
                        {!! nl2br(e($event->description)) !!}
                    @else
                        <span class="text-muted">No description provided</span>
                    @endif
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <form action="{{ route('staffevents.destroy', $event->id) }}" method="POST" class="me-2" onsubmit="return confirm('Are you sure you want to delete this event?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fas fa-trash-alt me-1"></i> Delete Event
                    </button>
                </form>
                <a href="{{ route('staffevents.edit', $event->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Edit Event
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
