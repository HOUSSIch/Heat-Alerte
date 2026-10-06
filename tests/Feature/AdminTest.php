<?php
namespace Tests\Feature;
use App\Models\User;use Illuminate\Foundation\Testing\RefreshDatabase;use Tests\TestCase;
class AdminTest extends TestCase{use RefreshDatabase;public function test_habitant_is_forbidden_from_admin(){ $u=User::factory()->create(['role'=>'habitant']);$this->actingAs($u)->get('/admin/dashboard')->assertForbidden();$this->actingAs($u)->get('/admin/users')->assertForbidden();}public function test_admin_can_open_dashboard_users_and_profile(){ $u=User::factory()->create(['role'=>'admin']);$this->actingAs($u)->get('/admin/dashboard')->assertOk();$this->actingAs($u)->get('/admin/users')->assertOk();$this->actingAs($u)->get('/admin/profile')->assertOk()->assertSee($u->email);}}
