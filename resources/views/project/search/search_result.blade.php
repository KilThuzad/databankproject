@extends('project.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/search.css') }}">
<div class="search-container">

    <div class="search-title">
        🔍 Search results for: <strong>{{ $query }}</strong>
    </div>

    {{-- No results --}}
    @if($projects->isEmpty() && $users->isEmpty() && $universities->isEmpty() && $categories->isEmpty())
        <div class="no-results-box">
            <strong>No results found.</strong> Try searching for something else.
        </div>
    @endif

    {{-- Research Projects --}}
    @if($projects->isNotEmpty())
        <div class="result-section-title">📘 Research Projects</div>

        @foreach ($projects as $p)
        <div class="result-card">
            <a href="{{ route('research_projects.show', $p->id) }}">
                {{ $p->title }}
            </a>
            <p>{{ Str::limit($p->description, 120) }}</p>
        </div>
        @endforeach
    @endif

    {{-- Users --}}
    @if($users->isNotEmpty())
        <div class="result-section-title">👤 Users</div>

        @foreach ($users as $u)
        <div class="result-card">
            <a href="{{ route('all.users') }}">
            <strong>{{ $u->firstname }} {{ $u->lastname }}</strong><br>
            <span>{{ $u->email }}</span>
            </a>
        </div>
        @endforeach
    @endif

    {{-- Universities --}}
    @if($universities->isNotEmpty())
        <div class="result-section-title">🏫 Universities</div>

        @foreach ($universities as $uni)
        <div class="result-card">
            <a href="{{ url('/universities') }}">
            <strong>{{ $uni->name }}</strong><br>
            <span>{{ $uni->address }}</span>
            </a>
        </div>
        @endforeach
    @endif

    {{-- Categories --}}
    @if($categories->isNotEmpty())
        <div class="result-section-title">📂 Categories</div>

        @foreach ($categories as $cat)
        <div class="result-card">
            <a href="{{ url('/categories') }}"><strong>{{ $cat->name }}</strong></a>
        </div>
        @endforeach
    @endif

</div>
@endsection
