<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles if they don't exist
        if (!Role::where('name', 'Admin')->exists()) {
            Role::create(['name' => 'Admin']);
        }

        if (!Role::where('name', 'Petugas')->exists()) {
            Role::create(['name' => 'Petugas']);
        }
    }

    /** @test */
    public function user_can_be_identified_as_admin()
    {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('Admin');

        $this->assertTrue($adminUser->isAdmin());
        $this->assertFalse($adminUser->isPetugas());
    }

    /** @test */
    public function user_can_be_identified_as_petugas()
    {
        $petugasUser = User::factory()->create();
        $petugasUser->assignRole('Petugas');

        $this->assertTrue($petugasUser->isPetugas());
        $this->assertFalse($petugasUser->isAdmin());
    }

    /** @test */
    public function user_without_role_is_neither_admin_nor_petugas()
    {
        $regularUser = User::factory()->create();

        $this->assertFalse($regularUser->isAdmin());
        $this->assertFalse($regularUser->isPetugas());
    }

    /** @test */
    public function user_can_get_role_name()
    {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('Admin');

        $this->assertEquals('Admin', $adminUser->role_name);
    }

    /** @test */
    public function user_with_multiple_roles_returns_first_role_name()
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        $user->assignRole('Petugas'); // This shouldn't happen in practice but let's test it

        $this->assertEquals('Admin', $user->role_name);
    }

    /** @test */
    public function user_without_roles_returns_default_role_name()
    {
        $user = User::factory()->create();

        $this->assertEquals('User', $user->role_name);
    }
}