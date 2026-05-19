<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $type;
    public $validityMinutes;

    public function __construct(string $code, string $type, int $validityMinutes)
    {
        $this->code = $code;
        $this->type = $type;
        $this->validityMinutes = $validityMinutes;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your OTP Code - PhoneShop',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
