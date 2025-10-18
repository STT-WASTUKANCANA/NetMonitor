<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $petugasUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles exist
        if (!Role::where('name', 'Admin')->exists()) {
            Role::create(['name' => 'Admin']);
        }

        if (!Role::where('name', 'Petugas')->exists()) {
            Role::create(['name' => 'Petugas']);
        }

        // Create test users
        $this->adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
        ]);
        $this->adminUser->assignRole('Admin');

        $this->petugasUser = User::factory()->create([
            'name' => 'Petugas User',
            'email' => 'petugas@test.com',
        ]);
        $this->petugasUser->assignRole('Petugas');
    }

    /** @test */
    public function admin_can_view_user_list()
    {
        $response = $this->actingAs($this->adminUser)
                         ->get(route('users.index'));

        $response->assertOk();
        $response->assertViewIs('users.index');
    }

    /** @test */
    public function admin_can_view_user_details()
    {
        $user = User::factory()->create();
        $user->assignRole('Petugas');

        $response = $this->actingAs($this->adminUser)
                         ->get(route('users.show', $user));

        $response->assertOk();
        $response->assertViewIs('users.show');
        $response->assertSee($user->name);
    }

    /** @test */
    public function admin_can_edit_user()
    {
        $user = User::factory()->create();
        $user->assignRole('Petugas');

        $response = $this->actingAs($this->adminUser)
                         ->get(route('users.edit', $user));

        $response->assertOk();
        $response->assertViewIs('users.edit');
        $response->assertSee($user->name);
    }

    /** @test */
    public function admin_can_delete_user()
    {
        $user = User::factory()->create();
        $user->assignRole('Petugas');

        $response = $this->actingAs($this->adminUser)
                         ->delete(route('users.destroy', $user));

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function petugas_cannot_access_user_management()
    {
        $response = $this->actingAs($this->petugasUser)
                         ->get(route('users.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function petugas_cannot_view_user_details()
    {
        $user = User::factory()->create();
        $user->assignRole('Petugas');

        $response = $this->actingAs($this->petugasUser)
                         ->get(route('users.show', $user));

        $response->assertForbidden();
    }

    /** @test */
    public function petugas_cannot_edit_user()
    {
        $user = User::factory()->create();
        $user->assignRole('Petugas');

        $response = $this->actingAs($this->petugasUser)
                         ->get(route('users.edit', $user));

        $response->assertForbidden();
    }

    /** @test */
    public function petugas_cannot_delete_user()
    {
        $user = User::factory()->create();
        $user->assignRole('Petugas');

        $response = $this->actingAs($this->petugasUser)
                         ->delete(route('users.destroy', $user));

        $response->assertForbidden();
    }

    /** @test */
    public function guest_cannot_access_user_management()
    {
        $response = $this->get(route('users.index'));

        $response->assertRedirect(route('login'));
    }
}