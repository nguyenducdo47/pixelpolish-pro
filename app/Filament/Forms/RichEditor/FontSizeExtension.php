<?php

namespace App\Filament\Forms\RichEditor;

use Tiptap\Core\Mark;

class FontSizeExtension extends Mark
{
    public static $name = 'fontSize';

    /**
     * @return array<int, array<string, mixed>>
     */
    public function parseHTML(): array
    {
        return [
            [
                'tag' => 'span',
                'getAttrs' => function ($DOMNode): bool {
                    $style = (string) $DOMNode->getAttribute('style');

                    return (bool) preg_match('/font-size:\s*[^;]+/i', $style);
                },
            ],
        ];
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function addAttributes(): array
    {
        return [
            'size' => [
                'parseHTML' => function ($DOMNode): ?string {
                    $style = (string) $DOMNode->getAttribute('style');

                    if (preg_match('/font-size:\s*([^;]+)/i', $style, $matches)) {
                        return trim($matches[1]);
                    }

                    return null;
                },
                'renderHTML' => function ($attributes): ?array {
                    $size = is_array($attributes)
                        ? ($attributes['size'] ?? null)
                        : ($attributes->size ?? null);

                    if (! is_string($size) || ! preg_match('/^\d+(\.\d+)?(px|rem|em|%)$/', $size)) {
                        return null;
                    }

                    return ['style' => 'font-size: '.$size];
                },
            ],
        ];
    }

    /**
     * @param  object  $mark
     * @param  array<string, mixed>  $HTMLAttributes
     * @return array<mixed>
     */
    public function renderHTML($mark, $HTMLAttributes = []): array
    {
        return [
            'span',
            $HTMLAttributes,
            0,
        ];
    }
}
