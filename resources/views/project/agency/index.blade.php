@extends('project.app')

@section('content')
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin/agency.css') }}">
@endpush

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3" style="color: var(--primary-dark);"><i class="fas fa-building"></i>  Member Agencies</h1>
        <a href="{{ route('member-agencies.create') }}" class="btn btn-danger">
            <i class="fas fa-plus me-1"></i> Add Member Agency
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-card table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 90px;" class="text-center">Logo</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th class="text-center" style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($memberAgencies as $agency)
                    <tr>
                        <td class="text-center">
                            @if($agency->logo)
                                <img src="{{ asset('storage/'.$agency->logo) }}" 
                                     alt="Logo" class="logo-img">
                            @else
                                <span class="text-muted small">No Logo</span>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ $agency->name }}</td>
                        <td>{{ $agency->address ?? '—' }}</td>
                        <td>{{ $agency->email ?? '—' }}</td>
                        <td class="text-center">
                            <a href="{{ route('member-agencies.edit', $agency->id) }}"
                               class="btn btn-sm btn-outline-warning btn-icon me-1"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            {{-- Delete button triggers modal --}}
                            <button type="button" 
                                    class="btn btn-sm btn-outline-danger btn-icon" 
                                    title="Delete"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteAgencyModal"
                                    data-form-id="delete-agency-form-{{ $agency->id }}">
                                <i class="fas fa-trash"></i>
                            </button>

                            {{-- Hidden delete form --}}
                            <form id="delete-agency-form-{{ $agency->id }}" 
                                  action="{{ route('member-agencies.destroy', $agency->id) }}" 
                                  method="POST" 
                                  class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No member agencies found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteAgencyModal" tabindex="-1" aria-labelledby="deleteAgencyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAgencyModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this member agency? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteAgency">Delete</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteAgencyModal');
    if (deleteModal) {
        const confirmDeleteBtn = document.getElementById('confirmDeleteAgency');
        let activeDeleteFormId = null;

        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            activeDeleteFormId = button.getAttribute('data-form-id');
        });

        confirmDeleteBtn.addEventListener('click', function() {
            if (activeDeleteFormId) {
                const form = document.getElementById(activeDeleteFormId);
                if (form) {
                    form.submit();
                }
            }
        });

        deleteModal.addEventListener('hidden.bs.modal', function() {
            activeDeleteFormId = null;
        });
    }
});
</script>
@endpush
@endsection