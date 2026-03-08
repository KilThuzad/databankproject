@extends('project.app')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Notifications</h1>
        <form action="{{ route('notifications.markAllRead') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">
                <i class="fas fa-check-circle me-1"></i> Mark All as Read
            </button>
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            @forelse($notifications as $notification)
                <div class="mb-3 p-3 border-bottom {{ $notification->read_at ? 'bg-light' : 'bg-white' }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="mb-1">
                                <a href="{{ route('notifications.markRead', $notification) }}" 
                                   class="{{ $notification->read_at ? 'text-muted' : 'text-dark' }}">
                                    {{ $notification->data['message'] }}
                                </a>
                            </h5>
                            <small class="text-muted">
                                {{ $notification->created_at->diffForHumans() }}
                            </small>
                        </div>
                        @if(!$notification->read_at)
                            <span class="badge bg-primary">New</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class="far fa-bell fa-3x text-muted mb-3"></i>
                    <p class="h5 text-muted">No notifications found</p>
                </div>
            @endforelse

            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection