<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class MailSettingsTestMail extends Mailable
{
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('panel.mail.test_subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: '<p>'.e(__('panel.mail.test_body')).'</p>',
        );
    }
}
