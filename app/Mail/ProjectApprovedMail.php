<?php

namespace App\Mail;

use App\Models\ResearchProject;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ProjectApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public ResearchProject $project;

    /**
     * Create a new message instance.
     */
    public function __construct(ResearchProject $project)
    {
        $this->project = $project;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Research Project Has Been Approved 🎉',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.project-approved',
            with: [
                'project' => $this->project,
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