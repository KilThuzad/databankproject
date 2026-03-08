@extends('project.app')

@section('content')
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

<div class="container">
    <h1>Universities</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('universities.create') }}" class="btn btn-primary mb-3">Add University</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Logo</th>
                <th>Name</th>
                <th>Address</th>
                <th>Email</th>
                <th>Phone</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($universities as $uni)
            <tr>
                <td>
                    @if($uni->logo)
                        <img src="{{ asset('storage/'.$uni->logo) }}" alt="Logo" style="height:50px;">
                    @else
                        <span class="text-muted">No Logo</span>
                    @endif
                </td>
                <td>{{ $uni->name }}</td>
                <td>{{ $uni->address }}</td>
                <td>{{ $uni->email }}</td>
                <td>{{ $uni->phone }}</td>
                <td class="text-center">
                    <a href="{{ route('universities.edit', $uni->id) }}" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></a>

                    <form action="{{ route('universities.destroy', $uni->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this university?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
