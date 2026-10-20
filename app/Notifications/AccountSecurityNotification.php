<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountSecurityNotification extends Notification
{
    use Queueable;

    /**
     * @param  array<int, string>  $lines
     */
    private function __construct(
        private readonly string $subject,
        private readonly string $title,
        private readonly array $lines,
    ) {}

    public static function passwordChanged(): self
    {
        return new self(
            'HeatAlert — Votre mot de passe a été modifié',
            'Votre mot de passe a été modifié',
            [
                'Le mot de passe de votre compte HeatAlert vient d’être modifié.',
                'Si vous êtes à l’origine de cette modification, aucune action n’est nécessaire.',
                'Sinon, utilisez immédiatement la procédure de récupération de mot de passe ou contactez l’administrateur.',
            ]
        );
    }

    public static function twoFactorEnabled(): self
    {
        return new self(
            'HeatAlert — Authentification à deux facteurs activée',
            'Authentification à deux facteurs activée',
            [
                'Votre compte est maintenant protégé par l’authentification à deux facteurs.',
                'Conservez vos codes de récupération dans un endroit sûr. Ils ne sont jamais transmis par e-mail.',
            ]
        );
    }

    public static function twoFactorDisabled(): self
    {
        return new self(
            'HeatAlert — Authentification à deux facteurs désactivée',
            'Authentification à deux facteurs désactivée',
            [
                'L’authentification à deux facteurs a été désactivée sur votre compte HeatAlert.',
                'Si vous n’êtes pas à l’origine de cette action, modifiez immédiatement votre mot de passe.',
            ]
        );
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->view('emails.layout', [
                'title' => $this->title,
                'greeting' => filled($notifiable->name ?? null) ? 'Bonjour '.$notifiable->name.',' : 'Bonjour,',
                'introLines' => $this->lines,
                'actionText' => null,
                'actionUrl' => null,
                'outroLines' => [],
            ]);
    }
}
