@extends('project.reviewer.myapp')

@section('content')


<style>
.research-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.research-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

.research-img {
    height: 200px;
    object-fit: cover;
}

.research-article {
    line-height: 1.8;
    font-size: 1.05rem;
}

.abstract-box {
    background: #f8f9fa;
    border-left: 4px solid #0d6efd;
    padding: 15px 20px;
    border-radius: 6px;
}

</style>
<div class="container py-5">

    <div class="text-center mb-5">
        <h1 class="fw-bold">Research Showcase</h1>
        <p class="text-muted">
            Approved research projects and academic studies
        </p>
        <hr class="w-25 mx-auto">
    </div>

    <div class="row g-4">
        @foreach($blogs as $blog)
            <div class="col-lg-4 col-md-6">
                <div class="card research-card h-100 shadow-sm border-0">

                    @if($blog->file_path)
                        <img src="{{ asset('storage/'.$blog->file_path) }}"
                             class="card-img-top research-img">
                    @endif

                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-primary mb-2 align-self-start">
                            {{ $blog->category }}
                        </span>

                        <h5 class="fw-semibold">
                            {{ $blog->title }}
                        </h5>

                        <p class="text-muted small mb-3">
                            {{ Str::limit(strip_tags($blog->description), 130) }}
                        </p>

                        <div class="mt-auto">
                            <div class="d-flex justify-content-between small text-muted mb-2">
                                <span>
                                    <i class="fas fa-user"></i>
                                    {{ $blog->author->name ?? 'Researcher' }}
                                </span>
                                <span>
                                    <i class="fas fa-calendar"></i>
                                    {{ $blog->created_at->format('Y') }}
                                </span>
                            </div>

                            <a href="{{ route('blog.show', $blog->id) }}"
                               class="btn btn-outline-primary btn-sm w-100">
                                View Research
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-5">
        {{ $blogs->links() }}
    </div>

</div>
@endsection
