@extends('project.myapp')

@section('content')
<link rel="stylesheet" href="{{ asset('css/search.css') }}">
<div class="search-container">

    <div class="search-title">
        🔍 Search results for: <strong>{{ $query }}</strong>
    </div>

    {{-- No results --}}
    @if($projects->isEmpty())
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

</div>
@endsection
