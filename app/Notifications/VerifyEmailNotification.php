<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => hash('sha256', $notifiable->getEmailForVerification()),
            ]
        );

        return (new MailMessage)
            ->subject('HeatAlert — Vérifiez votre adresse e-mail')
            ->view('emails.layout', [
                'title' => 'Vérifiez votre adresse e-mail',
                'greeting' => $this->greeting($notifiable->name ?? null),
                'introLines' => [
                    'Bienvenue sur HeatAlert.',
                    'Pour sécuriser votre compte et accéder aux fonctionnalités de la plateforme, veuillez confirmer votre adresse e-mail.',
                ],
                'actionText' => 'Vérifier mon adresse e-mail',
                'actionUrl' => $verificationUrl,
                'outroLines' => [
                    'Ce lien est personnel et sécurisé.',
                    'Si vous n’avez pas créé de compte HeatAlert, vous pouvez ignorer cet e-mail.',
                ],
            ]);
    }

    private function greeting(?string $name): string
    {
        return filled($name) ? 'Bonjour '.$name.',' : 'Bonjour,';
    }
}
