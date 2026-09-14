<?php

namespace App\Filament\Resources\MailCampaigns;

use App\Enums\ContentProfile;
use App\Enums\MailCampaignStatus;
use App\Enums\MailTemplateKind;
use App\Filament\Concerns\ConfiguresAdminTables;
use App\Filament\Concerns\RestrictsToAdminPanel;
use App\Filament\Concerns\TranslatesNavigation;
use App\Filament\Resources\MailCampaigns\Pages\EditMailCampaign;
use App\Filament\Resources\MailCampaigns\Pages\ManageMailCampaigns;
use App\Models\MailCampaign;
use App\Models\MailTemplate;
use App\Services\MailCampaigns\MailCampaignService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class MailCampaignResource extends Resource
{
    use ConfiguresAdminTables;
    use RestrictsToAdminPanel;
    use TranslatesNavigation;

    protected static ?string $model = MailCampaign::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static ?string $navigationLabel = 'panel.nav.mail_campaigns';

    protected static string|UnitEnum|null $navigationGroup = 'panel.nav.settings';

    protected static ?int $navigationSort = 82;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('panel.mail_campaigns.sections.campaign'))->schema([
                Select::make('mail_template_id')
                    ->label(__('panel.mail_campaigns.fields.template'))
                    ->relationship('template', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->disabled(fn (?MailCampaign $record): bool => $record !== null && ! $record->isEditable()),
                TextInput::make('name')
                    ->label(__('panel.mail_campaigns.fields.campaign_name'))
                    ->required()
                    ->maxLength(160)
                    ->disabled(fn (?MailCampaign $record): bool => $record !== null && ! $record->isEditable()),
                TextInput::make('timezone')
                    ->label(__('panel.mail_campaigns.fields.timezone'))
                    ->default('Asia/Ho_Chi_Minh')
                    ->maxLength(64)
                    ->disabled(fn (?MailCampaign $record): bool => $record !== null && ! $record->isEditable()),
                DateTimePicker::make('send_at')
                    ->label(__('panel.mail_campaigns.fields.send_at'))
                    ->seconds(false)
                    ->native(false)
                    ->disabled(fn (?MailCampaign $record): bool => $record !== null && ! $record->isEditable()),
            ])->columns(2),
            Section::make(__('panel.mail_campaigns.sections.audience'))->schema([
                Toggle::make('audience_exclude_admins')
                    ->label(__('panel.mail_campaigns.fields.exclude_admins'))
                    ->default(true)
                    ->disabled(fn (?MailCampaign $record): bool => $record !== null && ! $record->isEditable()),
                Select::make('audience_is_published')
                    ->label(__('panel.mail_campaigns.fields.portfolio_published'))
                    ->options([
                        '' => __('panel.mail_campaigns.audience.published_any'),
                        '1' => __('panel.mail_campaigns.audience.published_only'),
                        '0' => __('panel.mail_campaigns.audience.unpublished_only'),
                    ])
                    ->native(false)
                    ->disabled(fn (?MailCampaign $record): bool => $record !== null && ! $record->isEditable()),
                Select::make('audience_content_profiles')
                    ->label(__('panel.mail_campaigns.fields.content_profiles'))
                    ->options(ContentProfile::options())
                    ->multiple()
                    ->native(false)
                    ->disabled(fn (?MailCampaign $record): bool => $record !== null && ! $record->isEditable()),
                Toggle::make('audience_require_marketing_opt_in')
                    ->label(__('panel.mail_campaigns.fields.require_marketing_opt_in'))
                    ->helperText(__('panel.mail_campaigns.fields.require_marketing_opt_in_helper'))
                    ->default(true)
                    ->disabled(fn (?MailCampaign $record): bool => $record !== null && ! $record->isEditable()),
            ])->columns(2),
            Section::make(__('panel.mail_campaigns.sections.stats'))->schema([
                TextInput::make('status')
                    ->label(__('panel.mail_campaigns.fields.status'))
                    ->formatStateUsing(fn (?MailCampaignStatus $state): string => $state?->label() ?? '')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('recipients_total')
                    ->label(__('panel.mail_campaigns.fields.recipients_total'))
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('sent_count')
                    ->label(__('panel.mail_campaigns.fields.sent_count'))
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('failed_count')
                    ->label(__('panel.mail_campaigns.fields.failed_count'))
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('skipped_count')
                    ->label(__('panel.mail_campaigns.fields.skipped_count'))
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),
            ])
                ->columns(3)
                ->visible(fn (?MailCampaign $record): bool => $record !== null),
        ]);
    }

    public static function table(Table $table): Table
    {
        return static::configureAdminListingTable($table)
            ->columns([
                TextColumn::make('name')
                    ->label(__('panel.mail_campaigns.fields.campaign_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('template.name')
                    ->label(__('panel.mail_campaigns.fields.template'))
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('panel.mail_campaigns.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (MailCampaignStatus $state): string => $state->label()),
                TextColumn::make('send_at')
                    ->label(__('panel.mail_campaigns.fields.send_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('recipients_total')
                    ->label(__('panel.mail_campaigns.fields.recipients_total'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sent_count')
                    ->label(__('panel.mail_campaigns.fields.sent_count'))
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (MailCampaign $record): bool => $record->status === MailCampaignStatus::Draft),
                Action::make('sendNow')
                    ->label(__('panel.mail_campaigns.actions.send_now'))
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->requiresConfirmation()
                    ->visible(fn (MailCampaign $record): bool => in_array($record->status, [
                        MailCampaignStatus::Draft,
                        MailCampaignStatus::Scheduled,
                    ], true))
                    ->action(function (MailCampaign $record, MailCampaignService $service): void {
                        try {
                            $service->sendNow($record);

                            Notification::make()
                                ->title(__('panel.mail_campaigns.notify.send_started'))
                                ->success()
                                ->send();
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title(__('panel.mail_campaigns.notify.action_failed'))
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ]);
    }

    public static function canDelete(Model $record): bool
    {
        return $record instanceof MailCampaign && $record->status === MailCampaignStatus::Draft;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function mergeAudienceFormData(array $data, ?MailTemplateKind $kind = null): array
    {
        $published = $data['audience_is_published'] ?? '';

        $data['audience'] = [
            'exclude_admins' => (bool) ($data['audience_exclude_admins'] ?? true),
            'is_published' => $published === '' ? null : (bool) (int) $published,
            'content_profiles' => array_values($data['audience_content_profiles'] ?? []),
            'require_marketing_opt_in' => (bool) ($data['audience_require_marketing_opt_in'] ?? ($kind === MailTemplateKind::Marketing)),
        ];

        unset(
            $data['audience_exclude_admins'],
            $data['audience_is_published'],
            $data['audience_content_profiles'],
            $data['audience_require_marketing_opt_in'],
        );

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function expandAudienceFormData(array $data): array
    {
        $audience = $data['audience'] ?? [];

        $data['audience_exclude_admins'] = $audience['exclude_admins'] ?? true;
        $data['audience_is_published'] = array_key_exists('is_published', $audience) && $audience['is_published'] !== null
            ? ((bool) $audience['is_published'] ? '1' : '0')
            : '';
        $data['audience_content_profiles'] = $audience['content_profiles'] ?? [];
        $data['audience_require_marketing_opt_in'] = $audience['require_marketing_opt_in']
            ?? $audience['exclude_marketing_unsubscribed']
            ?? true;

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMailCampaigns::route('/'),
            'edit' => EditMailCampaign::route('/{record}/edit'),
        ];
    }
}
