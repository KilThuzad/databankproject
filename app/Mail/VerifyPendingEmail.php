<?php

namespace App\Mail;

use App\Models\PendingUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyPendingEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $pendingUser;

    public function __construct(PendingUser $pendingUser)
    {
        $this->pendingUser = $pendingUser;
    }

    public function build()
    {
        $verificationUrl = route('verification.verify.pending', [
            'token' => $this->pendingUser->verification_token,
            'email' => $this->pendingUser->email,
        ]);

        return $this->subject('Verify Your Email Address')
                    ->markdown('emails.verify-pending')
                    ->with([
                        'verificationUrl' => $verificationUrl,
                        'name' => $this->pendingUser->firstname,
                    ]);
    }
}