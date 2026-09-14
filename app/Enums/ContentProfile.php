<?php

namespace App\Enums;

enum ContentProfile: string
{
    case It = 'it';
    case General = 'general';
    case Creative = 'creative';
    case Education = 'education';
    case Business = 'business';

    public function label(): string
    {
        return __('panel.content_profiles.'.$this->value);
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
