@extends('project.reviewer.myapp')

@section('title', 'Project Reviews')

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ ('css/reviewer/style.css') }}">
@endpush
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<div class="container-fluid my-4">
<div class="review-card mt-4">

    <div class="review-header">
        <h6>Project Reviews</h6>
    </div>

    <div class="p-3">
        <table class="review-table">
            <thead>
                <tr>
                    <th align="left">Project</th>
                    <th align="center">Score</th>
                    <th align="center">Recommendation</th>
                    <th align="left">Date Submitted</th>
                    <th align="left">Action</th>
                </tr>
            </thead>
            <tbody>

                @forelse($reviews as $review)
                <tr class="review-row">
                    <td>
                        <div class="project-code">
                            PRJ-{{ str_pad($review->project_id, 3, '0', STR_PAD_LEFT) }}
                        </div>
                        <div class="project-title">
                            {{ $review->project->title ?? 'N/A' }}
                        </div>
                    </td>

                    <td align="center">
                        <span class="score-badge">
                            {{ number_format($review->overall_score, 2) }}
                        </span>
                    </td>

                    <td align="center">
                        @if($review->recommendation === 'accept')
                            <span class="badge-accept">Accept</span>
                        @elseif($review->recommendation === 'revise')
                            <span class="badge-revise">Revision</span>
                        @else
                            <span class="badge-reject">Reject</span>
                        @endif
                    </td>

                    <td>
                        {{ optional($review->submitted_at)->format('M d, Y') }}
                    </td>
                    <td>
                        <a href="{{ route('reviews.show', $review->id) }}"
                            class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-eye"></i> View
                        </a>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        No reviews found
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

</div>
</div>
@endsection