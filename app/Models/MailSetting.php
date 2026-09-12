<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Throwable;

#[Fillable([
    'mailer',
    'scheme',
    'host',
    'port',
    'username',
    'password',
    'from_address',
    'from_name',
])]
#[Hidden(['password'])]
class MailSetting extends Model
{
    protected function casts(): array
    {
        return [
            'port' => 'integer',
            'password' => 'encrypted',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultsFromConfig(): array
    {
        return [
            'mailer' => in_array(config('mail.default'), ['log', 'smtp'], true)
                ? config('mail.default')
                : 'log',
            'scheme' => config('mail.mailers.smtp.scheme'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'username' => config('mail.mailers.smtp.username'),
            'password' => filled(config('mail.mailers.smtp.password'))
                ? config('mail.mailers.smtp.password')
                : null,
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ];
    }

    public static function current(): self
    {
        return static::query()->first() ?? static::query()->create(static::defaultsFromConfig());
    }

    /**
     * @return array<string, mixed>
     */
    public function valuesForForm(): array
    {
        $stored = [
            'mailer' => $this->mailer,
            'scheme' => $this->scheme,
            'host' => $this->host,
            'port' => $this->port,
            'username' => $this->username,
            'password' => $this->password,
            'from_address' => $this->from_address,
            'from_name' => $this->from_name,
        ];

        foreach (static::defaultsFromConfig() as $key => $default) {
            if (blank($stored[$key] ?? null) && filled($default)) {
                $stored[$key] = $default;
            }
        }

        return $stored;
    }

    public static function applyToConfig(): void
    {
        if (! Schema::hasTable('mail_settings')) {
            return;
        }

        try {
            static::query()->first()?->writeConfig();
        } catch (Throwable) {
            return;
        }
    }

    public function writeConfig(): void
    {
        config([
            'mail.default' => $this->mailer ?: 'log',
        ]);

        if (filled($this->from_address)) {
            config(['mail.from.address' => $this->from_address]);
        }

        if (filled($this->from_name)) {
            config(['mail.from.name' => $this->from_name]);
        }

        if ($this->mailer !== 'smtp') {
            return;
        }

        config([
            'mail.mailers.smtp.host' => $this->host,
            'mail.mailers.smtp.port' => $this->port,
            'mail.mailers.smtp.username' => $this->username,
            'mail.mailers.smtp.password' => $this->password,
            'mail.mailers.smtp.scheme' => filled($this->scheme) ? $this->scheme : null,
        ]);
    }
}
