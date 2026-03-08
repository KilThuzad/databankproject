@extends('project.staff.layout.app')

@section('title', 'Create Event')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Create New Event</h1>
        <a href="{{ route('staffevents.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Events
        </a>
    </div>

    <div class="card">
        <div class="card-header bg-white border-0">
            <h2 class="h5 mb-0">Event Information</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('staffevents.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" 
                           placeholder="Enter event title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" 
                              rows="3" placeholder="Enter event description">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="event_date" class="form-label">Date & Time <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control" id="event_date" name="event_date" 
                           value="{{ old('event_date') }}" required>
                    @error('event_date')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-calendar-plus me-1"></i> Create Event
                    </button>
                    <button type="reset" class="btn btn-outline-secondary">
                        Clear
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection