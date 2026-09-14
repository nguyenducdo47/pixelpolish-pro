<?php

namespace App\Filament\Support;

use App\Support\ContentProfileConfig;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class ProjectFormSchema
{
    /**
     * @param  callable(): mixed  $profileResolver
     * @return array<int, array<string, mixed>>
     */
    public static function localeTabFieldDefinitions(callable $profileResolver): array
    {
        $config = fn (): ContentProfileConfig => ContentProfileConfig::forValue($profileResolver());
        $visible = fn (string $field): \Closure => fn (): bool => $config()->showsProjectField($field);
        $label = fn (string $field): \Closure => fn (): string => $config()->panelFieldLabel($field);

        $tabs = [
            [
                'name' => 'title',
                'label' => $label('title'),
                'required' => true,
            ],
            [
                'name' => 'subtitle',
                'label' => $label('subtitle'),
            ],
            [
                'name' => 'summary',
                'label' => $label('summary'),
                'type' => 'editor',
            ],
            [
                'name' => 'highlights',
                'label' => $label('highlights'),
                'type' => 'tags',
            ],
            [
                'name' => 'complexity',
                'label' => $label('complexity'),
                'visible' => $visible('complexity'),
            ],
            [
                'name' => 'problem',
                'label' => $label('problem'),
                'type' => 'editor',
                'visible' => $visible('problem'),
            ],
            [
                'name' => 'solution',
                'label' => $label('solution'),
                'type' => 'editor',
                'visible' => $visible('solution'),
            ],
            [
                'name' => 'learned',
                'label' => $label('learned'),
                'type' => 'editor',
                'visible' => $visible('learned'),
            ],
        ];

        return $tabs;
    }

    /**
     * @param  callable(): mixed|null  $profileResolver
     * @return array<int, TextInput|TagsInput>
     */
    public static function extraFields(?callable $profileResolver = null): array
    {
        $resolve = $profileResolver ?? fn () => auth()->user()?->portfolio?->content_profile;
        $config = fn (): ContentProfileConfig => ContentProfileConfig::forValue($resolve());
        $visible = fn (string $field): \Closure => fn (): bool => $config()->showsProjectField($field);

        return [
            TextInput::make('period')->label(fn (): string => $config()->panelFieldLabel('period')),
            TextInput::make('demo_url')->label(fn (): string => $config()->panelFieldLabel('demo_url'))->url(),
            TextInput::make('demo_label')->label(fn (): string => $config()->panelFieldLabel('demo_label')),
            TextInput::make('github_url')
                ->label(fn (): string => $config()->panelFieldLabel('github_url'))
                ->url()
                ->visible($visible('github_url')),
            TagsInput::make('tech_stack')
                ->label(fn (): string => $config()->panelFieldLabel('tech_stack'))
                ->visible($visible('tech_stack')),
        ];
    }

    public static function skillLevelVisible(Get $get, ?callable $profileResolver = null): bool
    {
        $resolve = $profileResolver ?? fn () => auth()->user()?->portfolio?->content_profile;
        $fromForm = $get('content_profile');
        $profile = $fromForm ?? $resolve();

        return ContentProfileConfig::forValue($profile)->usesSkillPercent();
    }
}
