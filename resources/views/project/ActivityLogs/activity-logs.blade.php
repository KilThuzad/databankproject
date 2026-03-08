@extends('project.app')

@section('title', 'Activity Logs')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/activity-logger.css') }}">
@endpush

<div class="container-fluid p-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: var(--primary-dark);">
                <i class="fas fa-history me-2" style="color: var(--primary-red);"></i> Activity Logs
            </h2>
            <p class="text-muted small mb-0">Track all system activities and user actions</p>
        </div>
        <span class="badge bg-light text-dark p-3 rounded-3">
            Total: {{ $logs->total() }} logs
        </span>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table logs-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Subject</th>
                            <th>Details</th>
                            <th class="text-end">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $index => $log)
                            <tr>
                                <td>{{ $logs->firstItem() + $index }}</td>
                                <td>
                                    <div class="user-name">
                                        {{ $log->user->firstname ?? 'N/A' }}
                                        {{ $log->user->lastname ?? '' }}
                                    </div>
                                    <span class="badge-role">
                                        {{ ucfirst($log->user->role ?? 'unknown') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-action">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $log->subject_type ? class_basename($log->subject_type) . ' #' . $log->subject_id : '—' }}
                                </td>
                                <td>
                                    <span class="details-text">
                                        {{ $log->details ?: '—' }}
                                    </span>
                                </td>
                                <td class="text-end date-col">
                                    {{ $log->created_at->format('M d, Y • h:i A') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center empty-state">
                                    No activity logs found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($logs->hasPages())
        <div class="card border-0 mt-4">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center simple-pagination">

                <div class="text-muted small mb-2 mb-md-0">
                    Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }}
                    of {{ $logs->total() }} results
                </div>

                <div>
                    {{ $logs->withQueryString()->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    @endif

</div>
@endsection