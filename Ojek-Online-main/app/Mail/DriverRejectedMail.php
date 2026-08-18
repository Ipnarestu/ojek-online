<?php

namespace App\Mail;

use App\Models\Driver;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DriverRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $driver;
    public $reason;

    public function __construct(Driver $driver, $reason)
    {
        $this->driver = $driver;
        $this->reason = $reason;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '❌ Status Pendaftaran Driver - OMK OJOL',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.driver-rejected',
        );
    }
}