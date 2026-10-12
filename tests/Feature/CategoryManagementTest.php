<?php

namespace Tests\Feature;

use App\Auth\Role;
use App\Catalog\CategoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedCatalog();
    }

    public function test_a_super_admin_can_view_the_collections_index(): void
    {
        $this->actingAsRole(Role::SuperAdmin, 'boss@example.test')
            ->get(route('categories.index'))
            ->assertOk()
            ->assertSee('Collections')
            ->assertSee('Computer Components');
    }

    public function test_a_super_admin_can_add_a_collection_that_appears_in_the_sidebar(): void
    {
        $this->actingAsRole(Role::SuperAdmin, 'boss@example.test');

        $response = $this->post(route('categories.store'), [
            'name' => 'Gaming Peripherals',
            'short_label' => 'Peripherals',
            'description' => 'Keyboards, mice and headsets.',
            'monogram' => 'GP',
        ]);

        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHas('success_dialog', 'Category created');

        $this->assertDatabaseHas('categories', ['name' => 'Gaming Peripherals']);

        // The new collection is a navigation link in the sidebar.
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Gaming Peripherals');

        // And it resolves as its own management overview page.
        $record = app(CategoryRepository::class)->findByName('Gaming Peripherals');
        $this->assertNotNull($record);
        $this->get(route('categories.overview', $record->slug()))->assertOk();
    }

    public function test_a_collection_requires_a_name(): void
    {
        $this->actingAsRole(Role::SuperAdmin, 'boss@example.test')
            ->post(route('categories.store'), ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    public function test_a_regular_user_cannot_manage_collections(): void
    {
        $this->actingAsRole(Role::User, 'viewer@example.test')
            ->get(route('categories.create'))
            ->assertForbidden();
    }
}
