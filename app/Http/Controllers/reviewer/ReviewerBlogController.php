<?php

namespace App\Http\Controllers\reviewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;

class ReviewerBlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::where('is_staff_approved', 'approved')
            ->where('status', 'complete')
            ->latest()
            ->paginate(6);

        return view('project.reviewer.blog-style.index', compact('blogs'));
    }

    public function show($id)
    {
        $blog = Blog::where('id', $id)
            ->where('is_staff_approved', 'approved')
            ->firstOrFail();

        return view('project.reviewer.blog-style.show', compact('blog'));
    }
}
