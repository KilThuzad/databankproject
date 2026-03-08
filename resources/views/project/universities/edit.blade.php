@extends('project.app')
@section('title', 'Edit University')
@section('content')

<div class="container py-5">
    <h2>Edit University</h2>

    <form action="{{ route('universities.update', $university->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $university->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" class="form-control" name="address" id="address" value="{{ old('address', $university->address) }}">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $university->email) }}">
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone', $university->phone) }}">
        </div>

        <div class="mb-3">
            <label for="logo" class="form-label">Logo</label>
            <input type="file" class="form-control" name="logo" id="logo">
            @if($university->logo)
                <img src="{{ asset('storage/'.$university->logo) }}" alt="Logo" class="mt-2" style="height: 80px;">
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Update University</button>
        <a href="{{ route('universities.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

@endsection
