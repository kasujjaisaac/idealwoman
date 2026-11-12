<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PartnershipEnquiry extends Mailable
{
    public function __construct(public array $enquiry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->enquiry['email'], $this->enquiry['name'])],
            subject: 'Ideal Woman: '.$this->enquiry['enquiry_type'],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.enquiry');
    }
}
