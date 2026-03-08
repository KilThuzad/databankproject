@extends('project.reviewer.myapp')

@section('title', 'Events')

@section('content')
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/reviewer/style.css') }}">
@endpush

<div class="container-fluid p-4">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2" style="color: #333; font-weight: 700;">Events</h1>
            <p class="text-muted mb-0">View all scheduled events and activities</p>
        </div>
    </div>

    {{-- Events Card --}}
    <div class="card shadow-sm" style="border: none; border-radius: 8px;">
        <div class="card-header border-0 p-3 card-header-gradient" style="border-radius: 8px 8px 0 0;">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <h2 class="h5 mb-0 d-inline-flex align-items-center" style="color: white; font-weight: 600;">
                    <i class="fas fa-calendar-alt me-2"></i>
                    All Events ({{ $events->count() }})
                </h2>
                
                <div class="d-flex gap-2">
                    <a href="{{ route('reviewerEvents.calendar') }}" class="btn btn-sm btn-outline-light">
                        <i class="far fa-calendar-alt me-1"></i> Calendar View
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="border-collapse: separate; border-spacing: 0;">
                    <thead class="table-light" style="background: #f8f9fa;">
                        <tr>
                            <th style="border: none; padding: 1rem; color: #666; font-weight: 600;">Event Title</th>
                            <th style="border: none; padding: 1rem; color: #666; font-weight: 600;">Description</th>
                            <th style="border: none; padding: 1rem; color: #666; font-weight: 600;">Date & Time</th>
                            <th style="border: none; padding: 1rem; color: #666; font-weight: 600;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($events as $event)
                        <tr style="border-bottom: 1px solid #f0f0f0;">
                            <td style="border: none; padding: 1rem;">
                                <h6 class="mb-1" style="color: #2c3e50; font-weight: 600;">{{ $event->title }}</h6>
                                @if($event->location)
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt me-1" style="color: #e74a3b;"></i>
                                    {{ $event->location }}
                                </small>
                                @endif
                            </td>
                            <td style="border: none; padding: 1rem; color: #666;">
                                {{ Str::limit($event->description, 50) }}
                            </td>
                            <td style="border: none; padding: 1rem;">
                                @php
                                    $eventDate = $event->event_date instanceof \Carbon\Carbon 
                                        ? $event->event_date 
                                        : \Carbon\Carbon::parse($event->event_date);
                                @endphp
                                
                                <div class="d-flex flex-column">
                                    <span class="event-date">
                                        <i class="far fa-calendar me-1"></i>
                                        {{ $eventDate->format('l, jS F Y') }}
                                    </span>
                                    <span class="text-muted mt-1">
                                        <i class="far fa-clock me-1"></i>
                                        {{ $eventDate->format('g:i A') }}
                                    </span>
                                    @if($eventDate->isPast())
                                    <span class="badge badge-past mt-2 align-self-start" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                                        Past Event
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center" style="border: none; padding: 1rem;">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('reviewer-events.show', $event->id) }}" 
                                       class="btn btn-sm btn-outline-primary action-btn" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5" style="border: none;">
                                <div style="color: #666;">
                                    <i class="fas fa-calendar-times fa-2x mb-3" style="color: #ddd;"></i>
                                    <h6 class="mb-2" style="color: #888;">No events found</h6>
                                    <p class="text-muted mb-0">There are no scheduled events at the moment</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            @if(method_exists($events, 'links') && method_exists($events, 'hasPages') && $events->hasPages())
                <div class="card-footer border-0 p-3" style="background: #f8f9fa; border-radius: 0 0 8px 8px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            @if($events->total() > 0)
                                Showing {{ $events->firstItem() }} to {{ $events->lastItem() }} of {{ $events->total() }} events
                            @else
                                Showing 0 events
                            @endif
                        </div>
                        <div>
                            {{ $events->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
       
        const searchInput = document.getElementById('searchInput');
        const searchBtn = document.getElementById('searchBtn');
        
        searchBtn.addEventListener('click', performSearch);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') performSearch();
        });
        
        function performSearch() {
            const query = searchInput.value.trim();
            if (query) {
                window.location = `{{ route('userevents.index') }}?search=${encodeURIComponent(query)}`;
            }
        }
    });
</script>
@endpush
@endsection