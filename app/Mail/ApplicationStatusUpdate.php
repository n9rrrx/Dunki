<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $status;

    /**
     * Create a new message instance.
     */
    public function __construct(Application $application, $status)
    {
        $this->application = $application;
        $this->status = $status;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->status) {
            'approved' => '🎉 Great News! Application Approved',
            'rejected' => '⚠️ Update regarding your Application',
            'visa_submitted' => '✈️ Visa Application Submitted',
            'visa_granted' => '🛂 Visa Granted! Packing time?',
            'travel_booked' => '🎫 Flight Tickets Confirmed',
            default => 'Application Status Update',
        };

        return new Envelope(
            subject: $subject . ' - ' . $this->application->university_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
        // ✅ We only need this line for Markdown emails
            markdown: 'emails.application.status',
        );
    }
}
