<?php

namespace Database\Seeders;

use App\Enums\MailTemplateKind;
use App\Models\MailTemplate;
use Illuminate\Database\Seeder;

class MailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'slug' => 'product-update',
                'name' => 'Product update',
                'kind' => MailTemplateKind::Marketing,
                'subject' => 'News from Portfotilo, {{user.name}}',
                'body_html' => '<p>Hi {{user.name}},</p><p>We have updates for your portfolio. Visit <a href="{{portfolio.url}}">your site</a> or open <a href="{{studio.url}}">Studio</a>.</p><p><a href="{{unsubscribe.url}}">Unsubscribe</a> from marketing emails.</p>',
                'body_text' => "Hi {{user.name}},\n\nVisit your site: {{portfolio.url}}\nStudio: {{studio.url}}\n\nUnsubscribe: {{unsubscribe.url}}",
                'is_system' => true,
            ],
            [
                'slug' => 'account-reminder',
                'name' => 'Finish your portfolio',
                'kind' => MailTemplateKind::Transactional,
                'subject' => 'Complete your Portfotilo page',
                'body_html' => '<p>Hello {{user.name}},</p><p>Your portfolio is not published yet. Sign in to <a href="{{studio.url}}">Studio</a> and publish when you are ready.</p>',
                'body_text' => "Hello {{user.name}},\n\nOpen Studio: {{studio.url}}",
                'is_system' => true,
            ],
        ];

        foreach ($templates as $template) {
            MailTemplate::query()->updateOrCreate(
                ['slug' => $template['slug']],
                $template,
            );
        }
    }
}
