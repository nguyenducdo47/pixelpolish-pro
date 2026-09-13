<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\TranslatesPage;
use App\Mail\MailSettingsTestMail;
use App\Models\MailSetting;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;
use Throwable;
use UnitEnum;

class ManageMail extends Page
{
    use TranslatesPage;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $navigationLabel = 'panel.nav.mail';

    protected static string|UnitEnum|null $navigationGroup = 'panel.nav.settings';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 80;

    protected static ?string $title = 'panel.pages.mail';

    protected string $view = 'filament.pages.manage-mail';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() === true
            && ! session()->has('impersonator_id')
            && Filament::getCurrentPanel()?->getId() === 'admin';
    }

    public function mount(): void
    {
        $this->form->fill($this->settings()->valuesForForm());
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data')->model($this->settings());
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('panel.sections.mail'))->schema([
                Select::make('mailer')
                    ->label(__('panel.fields.mail_mailer'))
                    ->options([
                        'log' => __('panel.fields.mail_mailer_log'),
                        'smtp' => 'SMTP',
                    ])
                    ->native(false)
                    ->required()
                    ->live()
                    ->helperText(__('panel.fields.mail_mailer_helper')),
                Select::make('scheme')
                    ->label(__('panel.fields.mail_scheme'))
                    ->options([
                        '' => __('panel.fields.mail_scheme_none'),
                        'smtps' => 'SMTPS (465)',
                    ])
                    ->native(false)
                    ->helperText(__('panel.fields.mail_scheme_helper')),
                TextInput::make('host')
                    ->label(__('panel.fields.mail_host'))
                    ->required(fn (Get $get): bool => $get('mailer') === 'smtp'),
                TextInput::make('port')
                    ->label(__('panel.fields.mail_port'))
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(65535)
                    ->required(fn (Get $get): bool => $get('mailer') === 'smtp'),
                TextInput::make('username')
                    ->label(__('panel.fields.mail_username'))
                    ->autocomplete(false),
                TextInput::make('password')
                    ->label(__('panel.fields.mail_password'))
                    ->password()
                    ->revealable()
                    ->autocomplete(false)
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->helperText(__('panel.fields.mail_password_helper')),
                TextInput::make('from_address')
                    ->label(__('panel.fields.mail_from_address'))
                    ->email()
                    ->required(),
                TextInput::make('from_name')
                    ->label(__('panel.fields.mail_from_name'))
                    ->required(),
            ])->columns(2),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label(__('panel.actions.save'))->submit('save'),
                        Action::make('sendTest')
                            ->label(__('panel.actions.send_test_mail'))
                            ->color('gray')
                            ->action('sendTest'),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        $this->settings()->update($this->form->getState());
        $this->settings()->refresh()->writeConfig();
        Mail::purge();

        $this->form->fill($this->settings()->refresh()->valuesForForm());

        Notification::make()->title(__('panel.notify.mail_saved'))->success()->send();
    }

    public function sendTest(): void
    {
        $this->previewFromForm()->writeConfig();
        Mail::purge();

        try {
            Mail::to(auth()->user()->email)->send(new MailSettingsTestMail);
        } catch (Throwable $exception) {
            Notification::make()
                ->title(__('panel.notify.mail_test_failed'))
                ->body($exception->getMessage())
                ->danger()
                ->send();

            return;
        }

        Notification::make()->title(__('panel.notify.mail_test_sent'))->success()->send();
    }

    /**
     * @return array<string, mixed>
     */
    protected function formStateForMail(): array
    {
        $data = $this->form->getState();
        $settings = $this->settings();

        if (blank($data['password'] ?? null)) {
            $data['password'] = $settings->password;
        }

        $data['scheme'] = filled($data['scheme'] ?? null) ? $data['scheme'] : null;

        return $data;
    }

    protected function previewFromForm(): MailSetting
    {
        $preview = new MailSetting;
        $preview->forceFill($this->formStateForMail());

        return $preview;
    }

    protected function settings(): MailSetting
    {
        return MailSetting::current();
    }
}
