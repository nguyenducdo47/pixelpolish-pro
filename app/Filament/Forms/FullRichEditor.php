<?php

namespace App\Filament\Forms;

use App\Filament\Forms\RichEditor\FullEditorPlugin;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Support\Icons\Heroicon;

class FullRichEditor
{
    public static function make(string $name): RichEditor
    {
        return RichEditor::make($name)
            ->fileAttachments(false)
            ->customTextColors()
            ->columnSpanFull()
            ->afterStateHydrated(function (RichEditor $component): void {
                $state = $component->getState();

                if (is_array($state)) {
                    return;
                }

                if (! is_string($state) || trim($state) === '') {
                    $component->state('<p></p>');
                }
            })
            ->plugins([
                FullEditorPlugin::make(),
            ])
            ->toolbarButtons([
                ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link', 'textColor', 'highlight'],
                [ToolbarButtonGroup::make('Cỡ chữ', FullEditorPlugin::fontSizeTools())->textualButtons()->icon(Heroicon::OutlinedArrowsPointingOut)],
                [ToolbarButtonGroup::make('Heading', ['paragraph', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'small', 'lead'])->textualButtons()],
                ['alignStart', 'alignCenter', 'alignEnd', 'alignJustify'],
                ['blockquote', 'code', 'codeBlock', 'bulletList', 'orderedList'],
                ['table', 'horizontalRule', 'details', 'grid', 'clearFormatting'],
                ['htmlSource', 'translateSelection', 'undo', 'redo'],
            ]);
    }
}
