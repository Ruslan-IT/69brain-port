<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InquiryReceived extends Mailable
{
    use SerializesModels;

    public function __construct(public Inquiry $inquiry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Новая заявка с сайта КБ «Прочность»',
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'mail.inquiry-received',
        );
    }
}
