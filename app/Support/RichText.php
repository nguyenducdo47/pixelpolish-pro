<?php

namespace App\Support;

use App\Filament\Forms\RichEditor\FullEditorPlugin;
use Filament\Forms\Components\RichEditor\RichContentRenderer;

final class RichText
{
    public static function html(mixed $value): string
    {
        $value = self::toStoredString($value);

        if (self::isBlank($value)) {
            return '';
        }

        if (! preg_match('/<[a-z][\s\S]*>/i', $value)) {
            $value = '<p>'.nl2br(e($value), false).'</p>';
        }

        return RichContentRenderer::make($value)
            ->plugins([FullEditorPlugin::make()])
            ->toHtml();
    }

    public static function toStoredString(mixed $value): string
    {
        if (is_array($value)) {
            if ($value === []) {
                return '';
            }

            if (self::isEditorState($value)) {
                return (string) RichContentRenderer::make($value)
                    ->plugins([FullEditorPlugin::make()])
                    ->toHtml();
            }

            $nested = $value[UiLocale::current()]
                ?? $value[LocaleCatalog::defaultCode()]
                ?? reset($value);

            return self::toStoredString($nested);
        }

        return trim((string) $value);
    }

    public static function isBlank(?string $value): bool
    {
        return trim(html_entity_decode(strip_tags((string) $value))) === '';
    }

    /**
     * @param  array<string, mixed>  $value
     */
    public static function isEditorState(array $value): bool
    {
        if (isset($value['type']) || isset($value['content'])) {
            return true;
        }

        $first = $value[0] ?? null;

        return array_is_list($value) && is_array($first) && (isset($first['type']) || isset($first['content']));
    }
}
