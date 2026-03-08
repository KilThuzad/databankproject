<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ResearchProject;
use App\Models\ProjectReview;

class ReviewSubmittedMail extends Mailable
{
    use SerializesModels;

    public $project;
    public $review;

    public function __construct(ResearchProject $project, ProjectReview $review)
    {
        $this->project = $project;
        $this->review = $review;
    }

    public function build()
    {
        return $this->subject('Your Research Project Has Been Reviewed')
                    ->view('emails.review-submitted');
    }
}