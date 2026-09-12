<?php

namespace App\Filament\Forms\RichEditor;

use App\Services\TextTranslator;
use App\Support\LocaleCatalog;
use App\Support\UiLocale;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Throwable;
use Tiptap\Core\Extension;

class FullEditorPlugin implements RichContentPlugin
{
    /**
     * @return list<string>
     */
    public static function fontSizeTools(): array
    {
        return [
            'fontSizeDefault',
            'fontSize12',
            'fontSize14',
            'fontSize16',
            'fontSize18',
            'fontSize20',
            'fontSize24',
            'fontSize28',
            'fontSize32',
            'fontSize36',
            'fontSize48',
        ];
    }

    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * @return array<Extension>
     */
    public function getTipTapPhpExtensions(): array
    {
        return [
            app(FontSizeExtension::class),
        ];
    }

    /**
     * @return array<string>
     */
    public function getTipTapJsExtensions(): array
    {
        $path = public_path('js/app/rich-content-plugins/font-size.js');

        return [
            '/js/app/rich-content-plugins/font-size.js?v='.(is_file($path) ? filemtime($path) : time()),
        ];
    }

    /**
     * @return array<RichEditorTool>
     */
    public function getEditorTools(): array
    {
        $sizes = [
            'fontSize12' => '12px',
            'fontSize14' => '14px',
            'fontSize16' => '16px',
            'fontSize18' => '18px',
            'fontSize20' => '20px',
            'fontSize24' => '24px',
            'fontSize28' => '28px',
            'fontSize32' => '32px',
            'fontSize36' => '36px',
            'fontSize48' => '48px',
        ];

        $tools = [
            RichEditorTool::make('fontSizeDefault')
                ->label(__('panel.actions.font_default'))
                ->jsHandler('$getEditor()?.chain().focus().unsetMark(\'fontSize\').run()')
                ->activeJsExpression('! $getEditor()?.getAttributes(\'fontSize\')?.size')
                ->icon(Heroicon::OutlinedArrowsPointingOut),
        ];

        foreach ($sizes as $name => $size) {
            $tools[] = RichEditorTool::make($name)
                ->label($size)
                ->jsHandler('$getEditor()?.chain().focus().setMark(\'fontSize\', { size: \''.$size.'\' }).run()')
                ->activeJsExpression('$getEditor()?.getAttributes(\'fontSize\')?.size === \''.$size.'\'')
                ->icon(Heroicon::OutlinedArrowsPointingOut);
        }

        $tools[] = RichEditorTool::make('htmlSource')
            ->label(__('panel.actions.html'))
            ->action(arguments: '{ html: $getEditor()?.getHTML() }')
            ->icon(Heroicon::OutlinedCodeBracket);

        $tools[] = RichEditorTool::make('translateSelection')
            ->label(__('panel.actions.translate'))
            ->action(arguments: '{ text: (() => { const editor = $getEditor(); if (! editor) return \'\'; const { from, to } = editor.state.selection; return editor.state.doc.textBetween(from, to, \'\\n\'); })() }')
            ->icon(Heroicon::OutlinedLanguage);

        return $tools;
    }

    /**
     * @return array<Action>
     */
    public function getEditorActions(): array
    {
        return [
            Action::make('htmlSource')
                ->label(__('panel.actions.html'))
                ->modalHeading(__('panel.actions.html'))
                ->modalWidth(Width::FiveExtraLarge)
                ->fillForm(fn (array $arguments): array => [
                    'html' => $arguments['html'] ?? '',
                ])
                ->schema([
                    Textarea::make('html')
                        ->hiddenLabel()
                        ->rows(20)
                        ->extraInputAttributes([
                            'class' => 'font-mono text-sm',
                            'spellcheck' => 'false',
                        ]),
                ])
                ->action(function (array $arguments, array $data, RichEditor $component): void {
                    $component->runCommands(
                        [
                            EditorCommand::make('setContent', arguments: [$data['html'] ?? '']),
                        ],
                        editorSelection: $arguments['editorSelection'] ?? null,
                    );
                }),
            Action::make('translateSelection')
                ->label(__('panel.actions.translate'))
                ->modalHeading(__('panel.actions.translate'))
                ->modalSubmitActionLabel(__('panel.actions.translate'))
                ->modalWidth(Width::Large)
                ->fillForm(function (array $arguments): array {
                    $current = UiLocale::current();

                    return [
                        'text' => $arguments['text'] ?? '',
                        'source' => 'auto',
                        'target' => LocaleCatalog::enabled()
                            ->first(fn ($locale) => $locale->code !== $current)?->code
                            ?? LocaleCatalog::defaultCode(),
                    ];
                })
                ->schema([
                    Textarea::make('text')
                        ->label(__('panel.fields.translate_source'))
                        ->helperText(__('panel.fields.translate_source_helper'))
                        ->rows(8)
                        ->required(),
                    Select::make('source')
                        ->label(__('panel.fields.translate_from'))
                        ->options(fn (): array => [
                            'auto' => __('panel.fields.translate_auto'),
                            ...LocaleCatalog::options(),
                        ])
                        ->required(),
                    Select::make('target')
                        ->label(__('panel.fields.translate_target'))
                        ->options(fn (): array => LocaleCatalog::options())
                        ->required(),
                ])
                ->action(function (Action $action, array $arguments, array $data, RichEditor $component, TextTranslator $translator): void {
                    $text = trim((string) ($data['text'] ?? ''));

                    if ($text === '') {
                        Notification::make()
                            ->title(__('panel.notify.translate_empty'))
                            ->danger()
                            ->send();
                        $action->halt();

                        return;
                    }

                    try {
                        $translated = $translator->translate(
                            $text,
                            (string) ($data['target'] ?? 'en'),
                            (string) ($data['source'] ?? 'auto'),
                        );
                    } catch (Throwable) {
                        Notification::make()
                            ->title(__('panel.notify.translate_failed'))
                            ->danger()
                            ->send();
                        $action->halt();

                        return;
                    }

                    $component->runCommands(
                        [
                            EditorCommand::make('insertContent', arguments: [$translated]),
                        ],
                        editorSelection: $arguments['editorSelection'] ?? null,
                    );

                    Notification::make()
                        ->title(__('panel.notify.translated'))
                        ->success()
                        ->send();
                }),
        ];
    }
}
