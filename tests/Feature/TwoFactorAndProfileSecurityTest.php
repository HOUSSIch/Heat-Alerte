<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\AccountSecurityNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorAndProfileSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_enable_and_confirm_totp_two_factor_authentication(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('two-factor.enable'), ['current_password' => 'password'])
            ->assertRedirect(route('two-factor.setup'));

        $user->refresh();
        $code = app(Google2FA::class)->getCurrentOtp($user->two_factor_secret);

        $this->actingAs($user)
            ->post(route('two-factor.confirm'), ['code' => $code])
            ->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertTrue($user->hasTwoFactorEnabled());
        $this->assertCount(8, $user->two_factor_recovery_codes);
        Notification::assertSentTo($user, AccountSecurityNotification::class, function (AccountSecurityNotification $notification) use ($user) {
            return $notification->toMail($user)->subject === 'HeatAlert — Authentification à deux facteurs activée';
        });
    }

    public function test_user_receives_a_security_notification_when_two_factor_is_disabled(): void
    {
        Notification::fake();
        $user = User::factory()->create([
            'two_factor_secret' => app(Google2FA::class)->generateSecretKey(),
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => ['ABCD-EFGH'],
        ]);

        $this->actingAs($user)
            ->delete(route('two-factor.disable'), ['current_password' => 'password'])
            ->assertRedirect();

        $this->assertFalse($user->fresh()->hasTwoFactorEnabled());
        Notification::assertSentTo($user, AccountSecurityNotification::class, function (AccountSecurityNotification $notification) use ($user) {
            return $notification->toMail($user)->subject === 'HeatAlert — Authentification à deux facteurs désactivée';
        });
    }

    public function test_two_factor_challenge_accepts_totp_and_consumes_a_recovery_code_once(): void
    {
        $secret = app(Google2FA::class)->generateSecretKey();
        $user = User::factory()->create([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => ['ABCD-EFGH'],
        ]);

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('two-factor.challenge'));
        $this->assertGuest();

        $this->post(route('two-factor.challenge.store'), ['code' => app(Google2FA::class)->getCurrentOtp($secret)])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'));
        $this->withSession(['two_factor_login.id' => $user->id])
            ->post(route('two-factor.challenge.store'), ['recovery_code' => 'ABCD-EFGH'])
            ->assertRedirect(route('dashboard'));

        $this->assertSame([], $user->fresh()->two_factor_recovery_codes);
    }

    public function test_profile_photo_is_validated_and_stored_on_the_public_disk(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'profile_photo' => UploadedFile::fake()->image('avatar.png'),
            ])
            ->assertRedirect(route('profile.show'));

        $path = $user->fresh()->profile_photo_path;
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'profile_photo' => UploadedFile::fake()->create('not-an-image.txt', 12, 'text/plain'),
            ])
            ->assertSessionHasErrors('profile_photo');
    }
}
