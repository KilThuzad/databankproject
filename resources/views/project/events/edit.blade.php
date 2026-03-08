@extends('project.app')

@section('title', 'Edit Event')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/event.css') }}">

<div class="container-fluid p-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0" style="color: var(--primary-dark)">
            <i class="fas fa-edit me-2"></i> Edit Event
        </h1>
        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Events
        </a>
    </div>

    {{-- Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-header border-0" style="background-color: var(--primary-bg)">
            <h2 class="h5 mb-0" style="color: var(--primary-dark)">
                <i class="fas fa-info-circle me-2"></i> Event Details
            </h2>
        </div>
        <div class="card-body">
            <form id="updateEventForm" method="POST" action="{{ route('events.update', $event->id) }}">
                @csrf
                @method('PUT')

                {{-- Title --}}
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control"
                           id="title" name="title"
                           value="{{ old('title', $event->title) }}"
                           placeholder="Enter event title" required>
                    @error('title')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control"
                              id="description" name="description"
                              rows="3"
                              placeholder="Enter event description">{{ old('description', $event->description) }}</textarea>
                    @error('description')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Event Date & Time --}}
                <div class="mb-4">
                    <label for="event_date" class="form-label">Event Date & Time</label>
                    <input type="datetime-local" class="form-control"
                           id="event_date" name="event_date"
                           value="{{ old('event_date', \Carbon\Carbon::parse($event->event_date)->format('Y-m-d\TH:i')) }}"
                           required>
                    @error('event_date')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#confirmUpdateModal">
                        <i class="fas fa-save me-1"></i> Update Event
                    </button>

                    <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Confirmation Modal --}}
<div class="modal fade" id="confirmUpdateModal" tabindex="-1" aria-labelledby="confirmUpdateLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmUpdateLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i> Confirm Update
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to update this event?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-danger" onclick="submitUpdateForm()">
                    Yes, Update
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Script --}}
<script>
    function submitUpdateForm() {
        document.getElementById('updateEventForm').submit();
    }
</script>

@endsection