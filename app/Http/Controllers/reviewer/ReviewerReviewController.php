<?php

namespace App\Http\Controllers\reviewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\ResearchProject;
use App\Models\User;
use App\Models\ProjectTeam;
use App\Models\ProjectComment;
use App\Models\MemberAgency;
use App\Models\ProjectReview;


class ReviewerReviewController extends Controller
{
    public function index()
    {
        $reviews = ProjectReview::with('project')
            ->where('reviewer_id', auth()->id())
            ->orderByDesc('submitted_at')
            ->paginate(10);

        return view('project.reviewer.reviews.index', compact('reviews'));
    }

    
    public function show($id)
    {
        $review = ProjectReview::with('project')
            ->where('id', $id)
            ->where('reviewer_id', auth()->id())
            ->firstOrFail();

        return view('project.reviewer.reviews.show', compact('review'));
    }

}
