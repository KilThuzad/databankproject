@extends('project.app')

@section('title', 'Create Event')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/event.css') }}">

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0" style="color: var(--primary-dark);">
            <i class="fas fa-calendar-plus me-2"></i> Create New Event
        </h1>
        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Events
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0">
            <h2 class="h5 mb-0" style="color: var(--primary-dark)">
                <i class="fas fa-info-circle me-2"></i> Event Information
            </h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('events.store') }}">
                @csrf

                {{-- Title --}}
                <div class="mb-3 position-relative">
                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control border-start-0" id="title" name="title" 
                               placeholder="Enter event title" value="{{ old('title') }}" required>
                    </div>
                    @error('title')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="mb-3 position-relative">
                    <label for="description" class="form-label">Description</label>
                    <div class="input-group">
                        <textarea class="form-control border-start-0" id="description" name="description" 
                                  rows="3" placeholder="Enter event description">{{ old('description') }}</textarea>
                    </div>
                    @error('description')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Date & Time --}}
                <div class="mb-4 position-relative">
                    <label for="event_date" class="form-label">Date & Time <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="datetime-local" class="form-control border-start-0" id="event_date" name="event_date" 
                               value="{{ old('event_date') }}" required>
                    </div>
                    @error('event_date')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-danger me-2">
                        <i class="fas fa-calendar-check me-1"></i> Create Event
                    </button>
                    <button type="reset" class="btn btn-outline-secondary">
                        <i class="fas fa-eraser me-1"></i> Clear
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
