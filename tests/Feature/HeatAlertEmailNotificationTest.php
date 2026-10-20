<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\AccountSecurityNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class HeatAlertEmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_change_sends_a_security_notification_without_exposing_a_secret(): void
    {
        Notification::fake();
        $user = User::factory()->create(['name' => 'Houssem']);

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'current_password' => 'password',
                'password' => 'nouveau-mot-de-passe',
                'password_confirmation' => 'nouveau-mot-de-passe',
            ])
            ->assertRedirect(route('profile.show'));

        Notification::assertSentTo($user, AccountSecurityNotification::class, function (AccountSecurityNotification $notification) use ($user) {
            $message = $notification->toMail($user);

            return $message->subject === 'HeatAlert — Votre mot de passe a été modifié'
                && $message->view === 'emails.layout';
        });
    }

    public function test_shared_email_template_renders_the_heatalert_identity(): void
    {
        $html = view('emails.layout', [
            'title' => 'Vérifiez votre adresse e-mail',
            'greeting' => 'Bonjour Houssem,',
            'introLines' => ['Bienvenue sur HeatAlert.'],
            'actionText' => 'Vérifier mon adresse e-mail',
            'actionUrl' => 'https://example.test/verification-signee',
            'outroLines' => ['Ce lien est personnel et sécurisé.'],
        ])->render();

        $this->assertStringContainsString('HeatAlert', $html);
        $this->assertStringContainsString('Prévention', $html);
        $this->assertStringContainsString('Vérifier mon adresse e-mail', $html);
    }
}
