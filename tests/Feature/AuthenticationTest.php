<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_redirects_guests_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Température actuelle')
            ->assertSee('Alerte canicule')
            ->assertSee('Dashboard')
            ->assertSee('Alertes météo')
            ->assertSee('Coupures électriques')
            ->assertSee('Points de fraîcheur')
            ->assertSee('Mes équipements')
            ->assertSee('Mes conseils')
            ->assertSee('Assistant HeatAlert')
            ->assertSee('Profil')
            ->assertSee('navbar-collapse', false);
    }

    public function test_registration_creates_a_habitant_and_authenticates_them(): void
    {
        $response = $this->post('/register', [
            'name' => 'Amina Ben Salem',
            'email' => 'amina@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'amina@example.test', 'role' => 'habitant']);
    }

    public function test_login_and_logout_work(): void
    {
        $user = User::factory()->create(['email' => 'habitant@example.test']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_profile_pages_are_protected_and_show_the_authenticated_user(): void
    {
        $this->get('/profile')->assertRedirect('/login');
        $this->get('/profile/edit')->assertRedirect('/login');

        $user = User::factory()->create(['name' => 'Sami Heat', 'role' => 'habitant']);

        $this->actingAs($user)
            ->get('/profile')
            ->assertOk()
            ->assertSee('Sami Heat')
            ->assertSee($user->email)
            ->assertSee('habitant');

        $this->actingAs($user)
            ->get('/profile/edit')
            ->assertOk()
            ->assertSee('Annuler')
            ->assertSee(route('profile.show'));
    }

    public function test_authenticated_user_can_update_only_their_own_profile_including_password(): void
    {
        $user = User::factory()->create(['role' => 'habitant']);
        $otherUser = User::factory()->create(['name' => 'Autre habitant']);

        $this->actingAs($user)
            ->put('/profile', [
                'name' => 'Nouveau nom',
                'email' => 'nouveau@example.test',
                'password' => 'nouveau-mot-de-passe',
                'password_confirmation' => 'nouveau-mot-de-passe',
                'role' => 'admin',
                'id' => $otherUser->id,
            ])
            ->assertRedirect('/profile');

        $user->refresh();
        $otherUser->refresh();
        $this->assertSame('Nouveau nom', $user->name);
        $this->assertSame('nouveau@example.test', $user->email);
        $this->assertSame('habitant', $user->role);
        $this->assertTrue(Hash::check('nouveau-mot-de-passe', $user->password));
        $this->assertNotSame('Nouveau nom', $otherUser->name);
    }

    public function test_authenticated_user_can_delete_only_their_own_account_and_is_logged_out(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user)
            ->delete('/profile')
            ->assertRedirect('/register');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseHas('users', ['id' => $otherUser->id]);
    }
}
