<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class UserManagementFeaturesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
            'password' => bcrypt('password'),
        ]);
        $this->adminUser->assignRole('Admin');

        $this->petugasUser = User::factory()->create([
            'name' => 'Petugas User',
            'email' => 'petugas@test.com',
            'password' => bcrypt('password'),
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
        $response->assertSee('User Management');
        $response->assertSee('Add User');
    }

    /** @test */
    public function admin_can_create_new_user()
    {
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'Petugas',
        ];

        $response = $this->actingAs($this->adminUser)
                         ->post(route('users.store'), $userData);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['email' => 'newuser@test.com']);
    }

    /** @test */
    public function admin_can_view_user_details()
    {
        $newUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'testuser@test.com',
        ]);
        $newUser->assignRole('Petugas');

        $response = $this->actingAs($this->adminUser)
                         ->get(route('users.show', $newUser));

        $response->assertOk();
        $response->assertViewIs('users.show');
        $response->assertSee($newUser->name);
        $response->assertSee($newUser->email);
    }

    /** @test */
    public function admin_can_edit_user()
    {
        $newUser = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@test.com',
        ]);
        $newUser->assignRole('Petugas');

        $updatedData = [
            'name' => 'New Name',
            'email' => 'new@test.com',
            'role' => 'Admin',
        ];

        $response = $this->actingAs($this->adminUser)
                         ->put(route('users.update', $newUser), $updatedData);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['name' => 'New Name']);
    }

    /** @test */
    public function admin_can_delete_user()
    {
        $newUser = User::factory()->create([
            'name' => 'Delete User',
            'email' => 'delete@test.com',
        ]);
        $newUser->assignRole('Petugas');

        $response = $this->actingAs($this->adminUser)
                         ->delete(route('users.destroy', $newUser));

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['email' => 'delete@test.com']);
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
        $newUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'testuser@test.com',
        ]);
        $newUser->assignRole('Petugas');

        $response = $this->actingAs($this->petugasUser)
                         ->get(route('users.show', $newUser));

        $response->assertForbidden();
    }

    /** @test */
    public function petugas_cannot_edit_user()
    {
        $newUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'testuser@test.com',
        ]);
        $newUser->assignRole('Petugas');

        $updatedData = [
            'name' => 'New Name',
            'email' => 'new@test.com',
            'role' => 'Admin',
        ];

        $response = $this->actingAs($this->petugasUser)
                         ->put(route('users.update', $newUser), $updatedData);

        $response->assertForbidden();
    }

    /** @test */
    public function petugas_cannot_delete_user()
    {
        $newUser = User::factory()->create([
            'name' => 'Delete User',
            'email' => 'delete@test.com',
        ]);
        $newUser->assignRole('Petugas');

        $response = $this->actingAs($this->petugasUser)
                         ->delete(route('users.destroy', $newUser));

        $response->assertForbidden();
    }

    /** @test */
    public function guest_cannot_access_user_management()
    {
        $response = $this->get(route('users.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function guest_cannot_create_user()
    {
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'Petugas',
        ];

        $response = $this->post(route('users.store'), $userData);

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function delete_button_requires_confirmation()
    {
        // This test verifies that the delete functionality exists in the view
        $response = $this->actingAs($this->adminUser)
                         ->get(route('users.index'));

        $response->assertOk();
        // Check for the presence of delete buttons and modal
        $response->assertSee('Delete', false); // Check for delete button text
        $response->assertSee('Confirm Delete User'); // Check for modal title
    }

    /** @test */
    public function edit_button_opens_edit_form()
    {
        $newUser = User::factory()->create([
            'name' => 'Edit User',
            'email' => 'edit@test.com',
        ]);
        $newUser->assignRole('Petugas');

        $response = $this->actingAs($this->adminUser)
                         ->get(route('users.edit', $newUser));

        $response->assertOk();
        $response->assertViewIs('users.edit');
        $response->assertSee('Edit User');
        $response->assertSee($newUser->name);
        $response->assertSee($newUser->email);
    }

    /** @test */
    public function show_button_displays_user_details()
    {
        $newUser = User::factory()->create([
            'name' => 'Show User',
            'email' => 'show@test.com',
        ]);
        $newUser->assignRole('Petugas');

        $response = $this->actingAs($this->adminUser)
                         ->get(route('users.show', $newUser));

        $response->assertOk();
        $response->assertViewIs('users.show');
        $response->assertSee('View User');
        $response->assertSee($newUser->name);
        $response->assertSee($newUser->email);
    }

    /** @test */
    public function delete_functionality_removes_user_from_database()
    {
        $newUser = User::factory()->create([
            'name' => 'Deleted User',
            'email' => 'deleted@test.com',
        ]);
        $newUser->assignRole('Petugas');

        // Verify user exists before deletion
        $this->assertDatabaseHas('users', ['email' => 'deleted@test.com']);

        $response = $this->actingAs($this->adminUser)
                         ->delete(route('users.destroy', $newUser));

        $response->assertRedirect(route('users.index'));
        // Verify user is removed from database
        $this->assertDatabaseMissing('users', ['email' => 'deleted@test.com']);
    }

    /** @test */
    public function user_management_navigation_only_visible_to_admins()
    {
        // Admin user should see the navigation item
        $response = $this->actingAs($this->adminUser)
                         ->get(route('dashboard'));

        $response->assertOk();
        // Check if navigation contains user management link for admin
        $response->assertSee('Users', false);

        // Petugas user should not see the navigation item
        $response = $this->actingAs($this->petugasUser)
                         ->get(route('dashboard'));

        $response->assertOk();
        // Check if navigation does not contain user management link for petugas
        $response->assertDontSee('Users', false);
    }

    /** @test */
    public function user_management_routes_are_protected_by_middleware()
    {
        // Test that all user management routes require authentication
        $routes = [
            ['GET', 'users.index'],
            ['GET', 'users.create'],
            ['POST', 'users.store'],
            ['GET', 'users.show'],
            ['GET', 'users.edit'],
            ['PUT', 'users.update'],
            ['DELETE', 'users.destroy'],
        ];

        foreach ($routes as $route) {
            [$method, $routeName] = $route;
            
            // Create a test user for routes that require a parameter
            $testUser = User::factory()->create();
            $testUser->assignRole('Petugas');
            
            if ($method === 'GET') {
                if ($routeName === 'users.show' || $routeName === 'users.edit') {
                    $response = $this->$method(route($routeName, $testUser));
                } else {
                    $response = $this->$method(route($routeName));
                }
            } elseif ($method === 'POST') {
                $response = $this->$method(route($routeName));
            } elseif ($method === 'PUT') {
                $response = $this->$method(route($routeName, $testUser));
            } elseif ($method === 'DELETE') {
                $response = $this->$method(route($routeName, $testUser));
            }
            
            $response->assertRedirect(route('login'));
        }
    }

    /** @test */
    public function user_management_api_endpoints_are_accessible()
    {
        // Test API endpoints
        $response = $this->actingAs($this->adminUser)
                         ->get('/api/users');

        // This should work for authenticated admin users
        $response->assertOk();
    }

    /** @test */
    public function user_management_api_returns_correct_json_structure()
    {
        $response = $this->actingAs($this->adminUser)
                         ->get('/api/users');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'profile_photo_path',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                    'roles' => [
                        '*' => [
                            'id',
                            'name',
                            'guard_name',
                            'created_at',
                            'updated_at',
                        ]
                    ]
                ]
            ],
            'pagination' => [
                'current_page',
                'last_page',
                'per_page',
                'total',
            ]
        ]);
    }

    /** @test */
    public function user_management_views_contain_required_elements()
    {
        // Test index view contains required elements
        $response = $this->actingAs($this->adminUser)
                         ->get(route('users.index'));

        $response->assertSee('User Management');
        $response->assertSee('Add User');
        $response->assertSee('Search users...');
        
        // Test create view contains required elements
        $response = $this->actingAs($this->adminUser)
                         ->get(route('users.create'));

        $response->assertSee('Create User');
        $response->assertSee('Name');
        $response->assertSee('Email');
        $response->assertSee('Password');
        $response->assertSee('Confirm Password');
        $response->assertSee('Role');
        
        // Create a user for testing show and edit views
        $testUser = User::factory()->create();
        $testUser->assignRole('Petugas');
        
        // Test show view contains required elements
        $response = $this->actingAs($this->adminUser)
                         ->get(route('users.show', $testUser));

        $response->assertSee('View User');
        $response->assertSee($testUser->name);
        $response->assertSee($testUser->email);
        
        // Test edit view contains required elements
        $response = $this->actingAs($this->adminUser)
                         ->get(route('users.edit', $testUser));

        $response->assertSee('Edit User');
        $response->assertSee($testUser->name);
        $response->assertSee($testUser->email);
    }
}