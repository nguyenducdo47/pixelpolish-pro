<?php

namespace App\Filament\Forms;

use App\Support\LocaleCatalog;
use App\Support\RichText;
use App\Support\UiLocale;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;

class LocaleTabs
{
    /**
     * @param  array<int, array{name: string, label: string, type?: string, rows?: int, required?: bool}>  $fields
     */
    public static function make(array $fields): Group
    {
        $locales = LocaleCatalog::enabled();

        if ($locales->isEmpty()) {
            $locales = collect([(object) [
                'code' => 'vi',
                'native_name' => 'Tiếng Việt',
            ]]);
        }

        $catalogDefault = LocaleCatalog::defaultCode();
        $uiLocale = UiLocale::current();

        $components = [
            Select::make('_locale')
                ->label(__('panel.fields.locale'))
                ->options($locales->mapWithKeys(
                    fn ($locale) => [$locale->code => $locale->native_name.' ('.$locale->code.')']
                )->all())
                ->default($uiLocale)
                ->selectablePlaceholder(false)
                ->native(false)
                ->live()
                ->dehydrated(false)
                ->prefixIcon(Heroicon::OutlinedLanguage)
                ->helperText(__('panel.fields.locale_helper'))
                ->extraFieldWrapperAttributes(['class' => 'max-w-xs'])
                ->afterStateHydrated(function (Select $component) use ($uiLocale): void {
                    if (filled($component->getState()) && LocaleCatalog::isEnabled($component->getState())) {
                        return;
                    }

                    $component->state($uiLocale);
                })
                ->afterStateUpdated(function (mixed $state, mixed $old, Get $get, Set $set) use ($fields): void {
                    if (! is_string($state) || $state === $old) {
                        return;
                    }

                    foreach ($fields as $field) {
                        $name = $field['name'];
                        $current = self::currentPath($name);
                        $bag = self::bag($get($name));

                        if (is_string($old) && $old !== '') {
                            $bag[$old] = self::normalizeValue($get($current), $field);
                        }

                        $set($name, $bag);
                        $set($current, self::displayValue($bag[$state] ?? null, $field));
                    }
                })
                ->columnSpanFull(),
        ];

        foreach ($fields as $field) {
            $name = $field['name'];
            $current = self::currentPath($name);

            $components[] = Hidden::make($name)
                ->default([])
                ->hiddenLabel()
                ->visible(false)
                ->dehydratedWhenHidden()
                ->afterStateHydrated(function (Hidden $component): void {
                    $component->state(self::bag($component->getState()));
                })
                ->dehydrateStateUsing(function (mixed $state, Get $get) use ($name, $field): array {
                    $bag = self::bag($state);
                    $locale = $get('_locale');

                    if (filled($locale)) {
                        $bag[$locale] = self::normalizeValue($get(self::currentPath($name)), $field);
                    }

                    return $bag;
                });

            $visible = self::input($current, $field)
                ->label($field['label'])
                ->dehydrated(false)
                ->afterStateHydrated(function (TextInput|Textarea|TagsInput|RichEditor $component, Get $get) use ($name, $field, $uiLocale): void {
                    $locale = $get('_locale') ?: $uiLocale;
                    $bag = self::bag($get($name));
                    $component->state(self::displayValue($bag[$locale] ?? null, $field));
                });

            if ($field['required'] ?? false) {
                $visible->required(fn (Get $get) => ($get('_locale') ?: $uiLocale) === $catalogDefault);
            }

            $components[] = $visible;
        }

        return Group::make($components)->columnSpanFull();
    }

    /**
     * @param  array{name: string, label: string, type?: string, rows?: int, required?: bool}  $field
     */
    private static function input(string $name, array $field): TextInput|Textarea|TagsInput|RichEditor
    {
        $type = $field['type'] ?? 'text';

        return match ($type) {
            'editor' => FullRichEditor::make($name),
            'textarea' => Textarea::make($name)->rows($field['rows'] ?? 4),
            'tags' => TagsInput::make($name),
            default => TextInput::make($name),
        };
    }

    private static function currentPath(string $name): string
    {
        return '_current.'.$name;
    }

    /**
     * @param  array{type?: string}  $field
     */
    private static function displayValue(mixed $value, array $field): mixed
    {
        $value = self::normalizeValue($value, $field);

        if (($field['type'] ?? 'text') === 'editor' && trim((string) $value) === '') {
            return '<p></p>';
        }

        return $value;
    }

    /**
     * @param  array{type?: string}  $field
     */
    private static function isList(array $field): bool
    {
        return ($field['type'] ?? 'text') === 'tags';
    }

    /**
     * @param  array{type?: string}  $field
     */
    private static function normalizeValue(mixed $value, array $field): mixed
    {
        if (self::isList($field)) {
            return array_values(array_filter(is_array($value) ? $value : [], fn ($item) => filled($item)));
        }

        if (($field['type'] ?? 'text') === 'editor') {
            return RichText::toStoredString($value);
        }

        return is_array($value) ? RichText::toStoredString($value) : ($value ?? '');
    }

    /**
     * @return array<string, mixed>
     */
    private static function bag(mixed $state): array
    {
        return is_array($state) ? $state : [];
    }
}
