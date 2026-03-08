<?php

namespace App\Mail;

use App\Models\ResearchProject;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ReviewerAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public ResearchProject $project;
    public array $reviewers;
    public string $deadline;

    /**
     * Create a new message instance.
     */
    public function __construct(ResearchProject $project, array $reviewers, string $deadline)
    {
        $this->project = $project;
        $this->reviewers = $reviewers;
        $this->deadline = $deadline;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reviewers Assigned to Your Project',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reviewer-assigned',
            with: [
                'project' => $this->project,
                'reviewers' => $this->reviewers,
                'deadline' => $this->deadline,
            ],
        );
    }

    /**
     * Get the attachments.
     */
    public function attachments(): array
    {
        return [];
    }
}