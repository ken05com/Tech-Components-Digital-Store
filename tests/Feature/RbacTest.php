<?php

namespace Tests\Feature;

use App\Auth\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedCatalog();
    }

    public function test_every_authenticated_role_can_view_the_dashboard(): void
    {
        foreach ([Role::SuperAdmin, Role::Admin, Role::User] as $role) {
            $this->actingAsRole($role, $role->value.'@example.test')
                ->get(route('dashboard'))
                ->assertOk();
        }
    }

    public function test_super_admin_can_view_the_inventory_page(): void
    {
        $this->actingAsRole(Role::SuperAdmin)
            ->get(route('inventory.index'))
            ->assertOk()
            ->assertViewIs('inventory.index')
            ->assertSee(route('inventory.products'));
    }

    public function test_users_with_inventory_access_can_view_the_inventory_product_page(): void
    {
        $this->actingAsRole(Role::Admin)
            ->get(route('inventory.products'))
            ->assertOk()
            ->assertViewIs('inventory.products');

        $this->actingAsRole(Role::User)
            ->get(route('inventory.products'))
            ->assertForbidden();
    }

    public function test_sales_pages_mark_the_sales_nav_item_as_active(): void
    {
        $this->actingAsRole(Role::SuperAdmin)
            ->get(route('sales.index'))
            ->assertOk()
            ->assertSee('aria-current="page"', false);
    }

    public function test_only_managers_can_open_the_create_form(): void
    {
        $this->actingAsRole(Role::User, 'viewer@example.test')
            ->get(route('products.create'))
            ->assertForbidden();

        $this->actingAsRole(Role::Admin, 'admin@example.test')
            ->get(route('products.create'))
            ->assertOk();
    }

    public function test_deleted_items_are_reserved_for_super_admins(): void
    {
        $this->actingAsRole(Role::Admin, 'admin@example.test')
            ->get(route('deleted-items.index'))
            ->assertForbidden();

        $this->actingAsRole(Role::SuperAdmin, 'boss@example.test')
            ->get(route('deleted-items.index'))
            ->assertOk();
    }

    public function test_only_super_admins_can_reset_the_catalog(): void
    {
        $this->actingAsRole(Role::Admin, 'admin@example.test')
            ->post(route('catalog.reset'))
            ->assertForbidden();
    }

    public function test_only_super_admins_can_update_settings(): void
    {
        $this->actingAsRole(Role::Admin, 'admin@example.test')
            ->put(route('settings.update'), [])
            ->assertForbidden();
    }

    public function test_guests_cannot_reach_the_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }
}
