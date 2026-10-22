<?php

namespace App\Enums;

enum CvListType: string
{
    case Certification = 'certification';
    case CareerTarget = 'career_target';
    case Strength = 'strength';

    public function label(): string
    {
        return __('panel.cv_list_types.'.$this->value);
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
