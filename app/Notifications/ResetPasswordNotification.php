<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $broker = config('auth.defaults.passwords');
        $expire = config("auth.passwords.{$broker}.expire", 60);
        $resetUrl = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('HeatAlert — Réinitialisation de votre mot de passe')
            ->view('emails.layout', [
                'title' => 'Réinitialisez votre mot de passe',
                'greeting' => $this->greeting($notifiable->name ?? null),
                'introLines' => [
                    'Nous avons reçu une demande de réinitialisation du mot de passe de votre compte HeatAlert.',
                ],
                'actionText' => 'Réinitialiser mon mot de passe',
                'actionUrl' => $resetUrl,
                'outroLines' => [
                    "Ce lien expire dans {$expire} minute(s).",
                    'Si vous n’êtes pas à l’origine de cette demande, aucune action n’est nécessaire.',
                ],
            ]);
    }

    private function greeting(?string $name): string
    {
        return filled($name) ? 'Bonjour '.$name.',' : 'Bonjour,';
    }
}
