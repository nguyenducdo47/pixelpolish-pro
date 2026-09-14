<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\HtmlString;

class CampaignMessageMail extends Mailable
{
    public function __construct(
        public string $subjectLine,
        public string $bodyHtml,
        public ?string $bodyText = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: $this->bodyHtml,
        );
    }

    /**
     * @return array<string, \Illuminate\Support\HtmlString>|string|array<int, string>
     */
    protected function buildView()
    {
        if (filled($this->bodyText) && isset($this->html)) {
            return [
                'html' => new HtmlString($this->html),
                'text' => new HtmlString($this->bodyText),
            ];
        }

        return parent::buildView();
    }
}
