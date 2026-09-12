<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageMail;
use App\Mail\MailSettingsTestMail;
use App\Models\MailSetting;
use App\Models\User;
use Database\Seeders\LocaleSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class MailSettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LocaleSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_admins_can_open_mail_settings(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'username' => 'mail-admin',
        ]);

        $this->actingAs($admin)
            ->get('/admin/manage-mail')
            ->assertOk()
            ->assertSeeText('Cấu hình mail')
            ->assertSeeText('Gửi email')
            ->assertSeeText('SMTP')
            ->assertSeeText('Gửi email thử')
            ->assertSeeText('Tên đăng nhập SMTP')
            ->assertSeeText('Mật khẩu SMTP')
            ->assertSeeText('Máy chủ SMTP');
    }

    public function test_mail_form_is_filled_from_env_including_credentials(): void
    {
        config([
            'mail.default' => 'log',
            'mail.mailers.smtp.scheme' => null,
            'mail.mailers.smtp.host' => '127.0.0.1',
            'mail.mailers.smtp.port' => 2525,
            'mail.mailers.smtp.username' => 'hello@example.com',
            'mail.mailers.smtp.password' => 'app-password',
            'mail.from.address' => 'hello@example.com',
            'mail.from.name' => 'Portfotilo',
        ]);

        $admin = User::factory()->create([
            'is_admin' => true,
            'username' => 'mail-env',
        ]);

        $this->actingAs($admin);

        Livewire::test(ManageMail::class)
            ->assertSet('data.mailer', 'log')
            ->assertSet('data.host', '127.0.0.1')
            ->assertSet('data.port', 2525)
            ->assertSet('data.username', 'hello@example.com')
            ->assertSet('data.password', 'app-password')
            ->assertSet('data.from_address', 'hello@example.com')
            ->assertSet('data.from_name', 'Portfotilo');
    }

    public function test_admin_dashboard_shows_mail_settings(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'username' => 'mail-dash',
        ]);

        $this->actingAs($admin)
            ->get('/admin?locale=vi')
            ->assertOk()
            ->assertSeeText('Cấu hình mail')
            ->assertSee('manage-mail', false);
    }

    public function test_mail_settings_are_hidden_from_studio(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'username' => 'mail-studio',
        ]);

        $this->actingAs($admin)
            ->get('/studio/manage-mail')
            ->assertForbidden();
    }

    public function test_regular_users_cannot_open_admin_mail_settings(): void
    {
        $user = User::factory()->create([
            'username' => 'mail-user',
        ]);

        $this->actingAs($user)
            ->get('/admin/manage-mail')
            ->assertRedirect('/studio/setup');
    }

    public function test_admins_can_save_smtp_settings(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'username' => 'mail-save',
        ]);

        $this->actingAs($admin);

        Livewire::test(ManageMail::class)
            ->set('data.mailer', 'smtp')
            ->set('data.host', 'smtp.example.com')
            ->set('data.port', 465)
            ->set('data.scheme', 'smtps')
            ->set('data.username', 'mailer@example.com')
            ->set('data.password', 'secret-pass')
            ->set('data.from_address', 'hello@example.com')
            ->set('data.from_name', 'Portfotilo')
            ->call('save')
            ->assertHasNoErrors();

        $settings = MailSetting::query()->first();

        $this->assertNotNull($settings);
        $this->assertSame('smtp', $settings->mailer);
        $this->assertSame('smtp.example.com', $settings->host);
        $this->assertSame(465, $settings->port);
        $this->assertSame('smtps', $settings->scheme);
        $this->assertSame('mailer@example.com', $settings->username);
        $this->assertSame('secret-pass', $settings->password);
        $this->assertSame('hello@example.com', $settings->from_address);
        $this->assertSame('smtp', config('mail.default'));
        $this->assertSame('smtp.example.com', config('mail.mailers.smtp.host'));
        $this->assertSame('secret-pass', config('mail.mailers.smtp.password'));
    }

    public function test_blank_password_keeps_the_stored_secret(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'username' => 'mail-keep',
        ]);

        MailSetting::query()->create([
            'mailer' => 'smtp',
            'host' => 'smtp.example.com',
            'port' => 465,
            'scheme' => 'smtps',
            'username' => 'mailer@example.com',
            'password' => 'keep-me',
            'from_address' => 'hello@example.com',
            'from_name' => 'Portfotilo',
        ]);

        $this->actingAs($admin);

        Livewire::test(ManageMail::class)
            ->set('data.mailer', 'smtp')
            ->set('data.host', 'smtp.example.com')
            ->set('data.port', 587)
            ->set('data.scheme', '')
            ->set('data.username', 'mailer@example.com')
            ->set('data.password', '')
            ->set('data.from_address', 'hello@example.com')
            ->set('data.from_name', 'Portfotilo')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('keep-me', MailSetting::query()->first()->password);
        $this->assertSame(587, MailSetting::query()->first()->port);
    }

    public function test_saved_settings_are_applied_when_mail_is_sent(): void
    {
        MailSetting::query()->create([
            'mailer' => 'log',
            'from_address' => 'noreply@portfotilo.test',
            'from_name' => 'Portfotilo Mail',
        ]);

        MailSetting::applyToConfig();

        $this->assertSame('log', config('mail.default'));
        $this->assertSame('noreply@portfotilo.test', config('mail.from.address'));
        $this->assertSame('Portfotilo Mail', config('mail.from.name'));
    }

    public function test_admins_can_send_a_test_email(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'is_admin' => true,
            'username' => 'mail-test',
            'email' => 'admin@example.com',
        ]);

        $this->actingAs($admin);

        Livewire::test(ManageMail::class)
            ->set('data.mailer', 'log')
            ->set('data.from_address', 'hello@example.com')
            ->set('data.from_name', 'Portfotilo')
            ->call('sendTest')
            ->assertHasNoErrors()
            ->assertNotified();

        Mail::assertSent(MailSettingsTestMail::class, function (MailSettingsTestMail $mail) use ($admin): bool {
            return $mail->hasTo($admin->email);
        });
    }
}
