<?php

namespace App\Mail;

use App\Models\Driver;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DriverVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $driver;

    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Akun Driver Anda Telah Diverifikasi - OMK OJOL',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.driver-verified',
        );
    }
}