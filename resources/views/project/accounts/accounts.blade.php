@extends('project.app')

@section('title', 'Accounts')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/account.css') }}">
@endpush

<div class="container-fluid p-4">
    <div class="card shadow-sm border-0 rounded-3">
        
        {{-- Header --}}
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-bold" style="color: var(--primary-dark)">
                <i class="fas fa-users me-2 text-danger"></i> All Users
            </h4>

            {{-- Filters --}}
            <form method="GET" action="{{ route('all.users') }}" class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light">
                        <i class="fas fa-building"></i>
                    </span>
                    <select name="member_agencies_id" onchange="this.form.submit()" class="form-select">
                        <option value="">All Agencies</option>
                        @foreach($memberAgencies as $agency)
                            <option value="{{ $agency->id }}" @selected($memberAgencyId == $agency->id)>
                                {{ $agency->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        {{-- Body --}}
        <div class="card-body">
            @if($users->isEmpty())
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-1"></i> No users found.
                </div>
            @else

                {{-- Role Summary --}}
                <div class="mb-3">
                    <h6 class="fw-semibold mb-2">User Counts by Role</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach($accountsByRole as $role => $count)
                            <span class="badge rounded-pill
                                @if($role === 'admin') bg-danger
                                @elseif($role === 'staff') bg-primary
                                @else bg-secondary
                                @endif">
                                {{ ucfirst($role) }}: {{ $count }}
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Agency</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-semibold">
                                        {{ $user->firstname.' '.$user->lastname }}
                                    </td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge
                                            @if($user->role === 'admin') bg-danger
                                            @elseif($user->role === 'staff') bg-primary
                                            @else bg-secondary
                                            @endif">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>{{ $user->memberAgency->name ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            
                                            {{-- Edit --}}
                                            <a href="{{ route('users.edit', $user->id) }}"
                                               class="btn btn-sm btn-outline-warning"
                                               title="Edit User">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            {{-- Delete --}}
                                            <form action="{{ route('delete.user', $user->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="Delete User">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @endif
        </div>
    </div>
</div>
@endsection
