@extends('project.researcher.layout.app')

@section('title', 'Event Details')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Event Details</h1>
        <div>
            <a href="{{ route('userevents.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Events
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white border-0">
            <h2 class="h5 mb-0">{{ $event->title }}</h2>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <h6 class="text-muted">Title</h6>
                    <p class="mb-0">{{ $event->title }}</p>
                </div>
                <div class="col-md-8">
                    <h6 class="text-muted">Scheduled Date</h6>
                    <p class="mb-0">
                        <i class="far fa-calendar-alt me-2"></i>
                        {{ \Carbon\Carbon::parse($event->event_date)->format('l, F d, Y') }}
                        <span class="mx-2">•</span>
                        <i class="far fa-clock me-2"></i>
                        {{ \Carbon\Carbon::parse($event->event_date)->format('h:i A') }}
                    </p>
                </div>
            </div>

            <div class="mb-3">
                <h6 class="text-muted">Description</h6>
                <div class="p-3 bg-light rounded">
                    {!! $event->description ? nl2br(e($event->description)) : '<span class="text-muted">No description provided</span>' !!}
                </div>
            </div>

            
        </div>
    </div>
</div>
@endsection