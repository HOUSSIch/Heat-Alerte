<?php

namespace Tests\Feature;

use App\Models\Quartier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_habitant_is_forbidden_from_admin()
    {
        $u = User::factory()->create(['role' => 'habitant']);
        $this->actingAs($u)->get('/admin/dashboard')->assertForbidden();
        $this->actingAs($u)->get('/admin/users')->assertForbidden();
    }

    public function test_admin_can_open_dashboard_users_and_profile()
    {
        $u = User::factory()->create(['role' => 'admin']);
        $this->actingAs($u)->get('/admin/dashboard')->assertOk();
        $this->actingAs($u)->get('/admin/users')->assertOk();
        $this->actingAs($u)->get('/admin/profile')->assertOk()->assertSee($u->email);
    }

    public function test_admin_can_manage_another_user_without_self_demotion_or_deletion(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $quartier = Quartier::factory()->create();

        $this->actingAs($admin)->get(route('admin.users.create'))->assertOk();
        $this->post(route('admin.users.store'), [
            'name' => 'Nouveau habitant',
            'email' => 'habitant@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'habitant',
            'quartier_id' => $quartier->id,
        ])->assertRedirect(route('admin.users.index'));

        $user = User::query()->where('email', 'habitant@example.test')->firstOrFail();
        $this->get(route('admin.users.show', $user))->assertOk();
        $this->get(route('admin.users.edit', $user))->assertOk();
        $this->put(route('admin.users.update', $user), [
            'name' => 'Habitant modifié',
            'email' => $user->email,
            'role' => 'agent',
            'quartier_id' => $quartier->id,
        ])->assertRedirect(route('admin.users.show', $user));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Habitant modifié', 'role' => 'agent']);

        $this->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'habitant',
        ])->assertSessionHasErrors('role');
        $this->delete(route('admin.users.destroy', $admin))->assertForbidden();
        $this->delete(route('admin.users.destroy', $user))->assertRedirect(route('admin.users.index'));
    }
}
