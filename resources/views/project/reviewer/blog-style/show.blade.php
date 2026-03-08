@extends('project.reviewer.myapp')

@section('content')
<div class="container py-5">

    <a href="{{ route('blog.index') }}" class="btn btn-sm btn-outline-secondary mb-4">
        ← Back to Research Showcase
    </a>

    <div class="row">
        <div class="col-lg-9 mx-auto">

            <article class="research-article">

                <h1 class="fw-bold mb-2">
                    {{ $blog->title }}
                </h1>

                <div class="text-muted small mb-3">
                    <span class="me-3">
                        <i class="fas fa-tag"></i> {{ $blog->category }}
                    </span>
                    <span class="me-3">
                        <i class="fas fa-user"></i>
                        {{ $blog->author->name ?? 'Researcher' }}
                    </span>
                    <span>
                        <i class="fas fa-calendar"></i>
                        {{ $blog->created_at->format('F d, Y') }}
                    </span>
                </div>

                @if($blog->file_path)
                    <img src="{{ asset('storage/'.$blog->file_path) }}"
                         class="img-fluid rounded mb-4">
                @endif

                <!-- Abstract -->
                <div class="abstract-box mb-4">
                    <h6 class="fw-bold">Abstract</h6>
                    <p class="mb-0">
                        {{ Str::limit(strip_tags($blog->description), 300) }}
                    </p>
                </div>

                <!-- Full Content -->
                <div class="research-content">
                    {!! $blog->description !!}
                </div>

                @if($blog->deadline)
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-clock"></i>
                        Research Deadline:
                        <strong>{{ \Carbon\Carbon::parse($blog->deadline)->format('F d, Y') }}</strong>
                    </div>
                @endif

            </article>

        </div>
    </div>

</div>
@endsection
