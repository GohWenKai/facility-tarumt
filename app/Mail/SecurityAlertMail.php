<?php

namespace App\Mail;

use App\Models\AuditLog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SecurityAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $alert;
    public $log;

    /**
     * Create a new message instance.
     */
    public function __construct(array $alert, AuditLog $log)
    {
        $this->alert = $alert;
        $this->log = $log;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $severity = strtoupper($this->alert['severity']);
        
        return new Envelope(
            subject: "[{$severity}] Security Alert - TARUMT Facility System",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.security_alert',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
