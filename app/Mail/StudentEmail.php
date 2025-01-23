<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Public property for student data.
     */
    public function __construct(
        public object $student // Laravel automatically makes this available in the Blade view
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Student QR Code',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.student_email', // Blade view for the email
        );
    }

    /**
     * Generate email address based on the student's full name.
     */
    public function generateEmail(): string
    {
        // Split full name into components (Assumes "Last, First Middle")
        [$lastName, $rest] = explode(',', $this->student->full_name);

        // Remove extra whitespace and split the "First Middle" part
        $restParts = explode(' ', trim($rest));

        // Get all first names (all parts except the last one)
        $firstNames = array_slice($restParts, 0, count($restParts) - 1);

        // Join all first names and convert to lowercase
        $firstNamesCombined = strtolower(implode('', $firstNames));

        // Extract the last name and convert to lowercase
        $lastName = strtolower(trim($lastName));

        return "{$firstNamesCombined}.{$lastName}@g.msuiit.edu.ph";
    }
}
