@extends('project.app')

@section('content')
<div class="container">
    <h1>Add University</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('universities.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label class="form-label">University Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Address</label>
        <input type="text" name="address" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">University Logo</label>
        <input type="file" name="logo" class="form-control" accept="image/*">
    </div>

    <button type="submit" class="btn btn-success">Save</button>
    <a href="{{ route('universities.index') }}" class="btn btn-secondary">Back</a>
</form>

</div>
@endsection
