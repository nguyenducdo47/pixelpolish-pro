<?php

namespace App\Services\MailCampaigns;

use App\Models\MailTemplate;
use App\Models\User;
use App\Models\UserMailPreference;
use App\Support\LocaleCatalog;
use App\Support\Studio;
use Illuminate\Support\Facades\URL;

class CampaignMailRenderer
{
    /**
     * @return array{subject: string, html: string, text: string|null}
     */
    public function render(MailTemplate $template, User $user): array
    {
        $replacements = $this->replacements($user, $template);

        return [
            'subject' => $this->replace($template->subject, $replacements),
            'html' => $this->replace($template->body_html, $replacements),
            'text' => $template->body_text
                ? $this->replace($template->body_text, $replacements)
                : null,
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function replacements(User $user, MailTemplate $template): array
    {
        $portfolio = $user->portfolio;
        $locale = $portfolio?->default_locale ?: LocaleCatalog::defaultCode();

        $portfolioUrl = $portfolio?->is_published
            ? $portfolio->publicUrl($locale)
            : '';

        $unsubscribe = '';

        if ($template->kind === \App\Enums\MailTemplateKind::Marketing) {
            $pref = UserMailPreference::forUser($user);
            $unsubscribe = URL::signedRoute('mail.unsubscribe', [
                'token' => $pref->unsubscribe_token,
            ]);
        }

        return [
            '{{user.name}}' => $user->name ?? '',
            '{{user.email}}' => $user->email ?? '',
            '{{user.username}}' => $user->username ?? '',
            '{{portfolio.url}}' => $portfolioUrl,
            '{{studio.url}}' => url(Studio::home()),
            '{{unsubscribe.url}}' => $unsubscribe,
        ];
    }

    /**
     * @param  array<string, string>  $replacements
     */
    protected function replace(string $content, array $replacements): string
    {
        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }
}
