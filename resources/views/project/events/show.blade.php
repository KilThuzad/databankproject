@extends('project.app')

@section('title', 'Event Details')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/event.css') }}">
@endpush

@section('content')
<div class="container-fluid p-4">

    {{-- Header with red gradient --}}
    <div class="event-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="h2 mb-1">
                    <i class="fas fa-calendar-alt me-2"></i>Event Details
                </h1>
                <p class="mb-0 opacity-75">View and manage event information</p>
            </div>
            <a href="{{ route('events.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left me-1"></i> Back to Events
            </a>
        </div>
    </div>

    {{-- Main content card --}}
    <div class="event-meta-card">
        {{-- Title row --}}
        <div class="meta-item">
            <div class="meta-icon">
                <i class="fas fa-heading"></i>
            </div>
            <div class="meta-content">
                <div class="meta-label">Event Title</div>
                <div class="meta-value">{{ $event->title }}</div>
            </div>
        </div>

        {{-- Date & Time row --}}
        <div class="meta-item">
            <div class="meta-icon">
                <i class="far fa-calendar-alt"></i>
            </div>
            <div class="meta-content">
                <div class="meta-label">Scheduled Date & Time</div>
                <div class="meta-value">
                    {{ \Carbon\Carbon::parse($event->event_date)->format('l, F d, Y') }}
                    <span class="mx-2 text-muted">•</span>
                    {{ \Carbon\Carbon::parse($event->event_date)->format('h:i A') }}
                    <small>({{ \Carbon\Carbon::parse($event->event_date)->diffForHumans() }})</small>
                </div>
            </div>
        </div>

        {{-- Optional: add location if exists in model --}}
        @if(isset($event->location) && $event->location)
        <div class="meta-item">
            <div class="meta-icon">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="meta-content">
                <div class="meta-label">Location</div>
                <div class="meta-value">{{ $event->location }}</div>
            </div>
        </div>
        @endif
    </div>

    {{-- Description section --}}
    <div class="description-card">
        <div class="description-title">
            <i class="fas fa-align-left"></i>
            <span>Description</span>
        </div>
        <div class="description-content">
            {!! $event->description ? nl2br(e($event->description)) : '<span class="text-muted fst-italic">No description provided.</span>' !!}
        </div>
    </div>

    {{-- Action buttons --}}
    <div class="action-buttons mt-4">
        <form action="{{ route('events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this event? This action cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-red">
                <i class="fas fa-trash-alt me-1"></i> Delete Event
            </button>
        </form>
        <a href="{{ route('events.edit', $event->id) }}" class="btn btn-red">
            <i class="fas fa-edit me-1"></i> Edit Event
        </a>
    </div>

</div>
@endsection