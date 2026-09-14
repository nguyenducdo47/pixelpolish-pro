<?php

namespace App\Filament\Resources\MailTemplates;

use App\Enums\MailTemplateKind;
use App\Filament\Concerns\ConfiguresAdminTables;
use App\Filament\Concerns\RestrictsToAdminPanel;
use App\Filament\Concerns\TranslatesNavigation;
use App\Filament\Forms\FullRichEditor;
use App\Filament\Resources\MailTemplates\Pages\ManageMailTemplates;
use App\Models\MailTemplate;
use App\Services\MailCampaigns\MailCampaignService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use UnitEnum;

class MailTemplateResource extends Resource
{
    use ConfiguresAdminTables;
    use RestrictsToAdminPanel;
    use TranslatesNavigation;

    protected static ?string $model = MailTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'panel.nav.mail_templates';

    protected static string|UnitEnum|null $navigationGroup = 'panel.nav.settings';

    protected static ?int $navigationSort = 81;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('panel.mail_campaigns.sections.template'))
                ->columnSpanFull()
                ->description(static::placeholderHelpDescription())
                ->schema([
                TextInput::make('name')
                    ->label(__('panel.mail_campaigns.fields.template_name'))
                    ->required()
                    ->maxLength(120),
                TextInput::make('slug')
                    ->label(__('panel.mail_campaigns.fields.template_slug'))
                    ->required()
                    ->maxLength(80)
                    ->unique(ignoreRecord: true)
                    ->alphaDash(),
                Select::make('kind')
                    ->label(__('panel.mail_campaigns.fields.kind'))
                    ->options(MailTemplateKind::options())
                    ->required()
                    ->native(false)
                    ->columnSpanFull(),
                TextInput::make('subject')
                    ->label(__('panel.mail_campaigns.fields.subject'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                FullRichEditor::make('body_html')
                    ->label(__('panel.mail_campaigns.fields.body_html'))
                    ->required(),
                FullRichEditor::make('body_text')
                    ->label(__('panel.mail_campaigns.fields.body_text'))
                    ->dehydrateStateUsing(function (?string $state): ?string {
                        if (! is_string($state) || trim(strip_tags($state)) === '') {
                            return null;
                        }

                        return $state;
                    }),
            ])->columns(2),
        ]);
    }

    public static function configureFormModal(Action $action): Action
    {
        return $action
            ->modalWidth(Width::SevenExtraLarge)
            ->stickyModalHeader()
            ->stickyModalFooter();
    }

    public static function placeholderHelpDescription(): Htmlable
    {
        $rows = [
            ['{{user.name}}', 'panel.mail_campaigns.helpers.placeholder_user_name'],
            ['{{user.email}}', 'panel.mail_campaigns.helpers.placeholder_user_email'],
            ['{{user.username}}', 'panel.mail_campaigns.helpers.placeholder_user_username'],
            ['{{portfolio.url}}', 'panel.mail_campaigns.helpers.placeholder_portfolio_url'],
            ['{{studio.url}}', 'panel.mail_campaigns.helpers.placeholder_studio_url'],
            ['{{unsubscribe.url}}', 'panel.mail_campaigns.helpers.placeholder_unsubscribe_url'],
        ];

        $items = '';

        foreach ($rows as [$token, $translationKey]) {
            $items .= '<li><code>'.e($token).'</code> — '.e(__($translationKey)).'</li>';
        }

        return new HtmlString(
            '<p class="text-sm">'.e(__('panel.mail_campaigns.helpers.placeholders_intro')).'</p>'
            .'<ul class="mt-2 list-disc space-y-1 ps-5 text-sm">'.$items.'</ul>'
        );
    }

    public static function table(Table $table): Table
    {
        return static::configureAdminListingTable($table)
            ->columns([
                TextColumn::make('name')
                    ->label(__('panel.mail_campaigns.fields.template_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label(__('panel.mail_campaigns.fields.template_slug'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('kind')
                    ->label(__('panel.mail_campaigns.fields.kind'))
                    ->badge()
                    ->formatStateUsing(fn (MailTemplateKind $state): string => $state->label()),
                IconColumn::make('is_system')
                    ->label(__('panel.mail_campaigns.fields.is_system'))
                    ->boolean(),
            ])
            ->defaultSort('name')
            ->recordActions([
                Action::make('sendTest')
                    ->label(__('panel.mail_campaigns.actions.send_test'))
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->action(function (MailTemplate $record, MailCampaignService $campaigns): void {
                        $admin = auth()->user();

                        if (! $admin) {
                            return;
                        }

                        try {
                            $campaigns->sendTemplateTest($record, $admin);

                            Notification::make()
                                ->title(__('panel.mail_campaigns.notify.test_sent'))
                                ->success()
                                ->send();
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title(__('panel.mail_campaigns.notify.test_failed'))
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                static::configureFormModal(EditAction::make()),
                DeleteAction::make()
                    ->visible(fn (MailTemplate $record): bool => ! $record->is_system),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canDelete(Model $record): bool
    {
        return $record instanceof MailTemplate && ! $record->is_system;
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMailTemplates::route('/'),
        ];
    }
}
