<?php

namespace App\Enums;

enum MailTemplateKind: string
{
    case Transactional = 'transactional';
    case Marketing = 'marketing';

    public function label(): string
    {
        return __('panel.mail_campaigns.kinds.'.$this->value);
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
