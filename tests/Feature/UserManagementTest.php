<?php

namespace Tests\Feature;

use App\Auth\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedCatalog();
    }

    public function test_a_super_admin_can_view_the_users_index(): void
    {
        $this->actingAsRole(Role::SuperAdmin, 'boss@example.test')
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee('Users & Roles')
            ->assertSee('Account Roster');
    }

    public function test_a_super_admin_can_create_a_new_user(): void
    {
        $this->actingAsRole(Role::SuperAdmin, 'boss@example.test');

        $response = $this->post(route('users.store'), [
            'name' => 'Support Operator',
            'email' => 'operator@example.test',
            'password' => 'secret-password-123',
            'password_confirmation' => 'secret-password-123',
            'role' => Role::Admin->value,
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success_dialog', 'User created');

        $this->assertDatabaseHas('users', [
            'email' => 'operator@example.test',
            'role' => Role::Admin->value,
        ]);
    }

    public function test_a_non_super_admin_cannot_manage_users(): void
    {
        $this->actingAsRole(Role::Admin, 'admin@example.test')
            ->get(route('users.index'))
            ->assertForbidden();

        $this->actingAsRole(Role::User, 'viewer@example.test')
            ->get(route('users.index'))
            ->assertForbidden();
    }

    public function test_super_admin_cannot_delete_themselves(): void
    {
        $admin = $this->makeUser(Role::SuperAdmin, 'self@example.test');

        $this->actingAs($admin)
            ->delete(route('users.destroy', $admin->id))
            ->assertSessionHasErrors('user');
    }
}
