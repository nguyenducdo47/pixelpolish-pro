<?php

namespace App\Support;

trait HasLocaleText
{
    public function localeText(string $field, ?string $locale = null): string
    {
        return $this->stringifyLocaleValue($this->pickLocaleValue($this->localeBag($field), $locale), $locale);
    }

    public function localeHtml(string $field, ?string $locale = null): string
    {
        return RichText::html($this->localeText($field, $locale));
    }

    public function localeList(string $field, ?string $locale = null): array
    {
        $items = $this->pickLocaleValue($this->localeBag($field), $locale);

        if (! is_array($items) || RichText::isEditorState($items)) {
            return [];
        }

        return array_values(array_filter($items, fn ($item) => filled($item)));
    }

    /**
     * @param  array<string, mixed>  $bag
     */
    private function pickLocaleValue(array $bag, ?string $locale = null): mixed
    {
        if ($bag === []) {
            return '';
        }

        $locale ??= app()->getLocale();

        return $bag[$locale]
            ?? $bag[LocaleCatalog::defaultCode()]
            ?? reset($bag)
            ?: '';
    }

    private function stringifyLocaleValue(mixed $value, ?string $locale = null): string
    {
        if (is_array($value) && $value !== [] && ! RichText::isEditorState($value) && $this->looksLikeLocaleBag($value)) {
            return $this->stringifyLocaleValue($this->pickLocaleValue($value, $locale), $locale);
        }

        return RichText::toStoredString($value);
    }

    /**
     * @param  array<string, mixed>  $value
     */
    private function looksLikeLocaleBag(array $value): bool
    {
        foreach (array_keys($value) as $key) {
            if (! is_string($key) || ! preg_match('/^[a-z]{2}(?:-[a-z]{2})?$/i', $key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    private function localeBag(string $field): array
    {
        $value = $this->getAttribute($field);

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        }

        return is_array($value) ? $value : [];
    }
}
