<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    public function __construct(#[\SensitiveParameter] public string $token) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $expire = (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->subject(__('passwords.mail.subject'))
            ->line(__('passwords.mail.intro'))
            ->action(__('passwords.mail.action'), $this->resetUrl($notifiable))
            ->line(__('passwords.mail.expire', ['count' => $expire]))
            ->line(__('passwords.mail.ignore'));
    }

    protected function resetUrl(object $notifiable): string
    {
        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
            'locale' => app()->getLocale(),
        ], false));
    }
}
