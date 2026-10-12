<?php

namespace Tests\Feature;

use App\Auth\Role;
use App\Catalog\Category;
use App\Catalog\Product;
use App\Catalog\ProductRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedCatalog();
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'category' => Category::ComputerComponents->value,
            'name' => 'Test Graphics Card',
            'description' => 'A reliable graphics card for testing the catalog.',
            'price_min' => 5000,
            'price_max' => 8000,
        ], $overrides);
    }

    public function test_the_index_lists_the_seeded_products(): void
    {
        $this->actingAsRole(Role::User, 'viewer@example.test')
            ->get(route('products.index'))
            ->assertOk()
            ->assertSee('Computer Components');
    }

    public function test_an_admin_can_create_a_product_with_a_success_dialog(): void
    {
        $this->actingAsRole(Role::Admin, 'admin@example.test');

        $response = $this->post(route('products.store'), $this->validPayload());

        $response->assertRedirect();
        $response->assertSessionHas('success_dialog', 'Successfully Added New Collection');

        $this->assertDatabaseHas('products', [
            'name' => 'Test Graphics Card',
            'category' => Category::ComputerComponents->value,
        ]);
    }

    public function test_product_names_are_limited_to_one_hundred_characters(): void
    {
        $this->actingAsRole(Role::Admin, 'admin@example.test');

        $this->post(route('products.store'), $this->validPayload([
            'name' => str_repeat('A', 101),
        ]))->assertSessionHasErrors('name');
    }

    public function test_a_maximum_price_below_the_minimum_is_rejected(): void
    {
        $this->actingAsRole(Role::Admin, 'admin@example.test');

        $this->post(route('products.store'), $this->validPayload([
            'price_min' => 9000,
            'price_max' => 1000,
        ]))->assertSessionHasErrors('price_max');
    }

    public function test_an_admin_can_edit_a_product_with_a_success_dialog(): void
    {
        /** @var Product $product */
        $product = app(ProductRepository::class)->find(1);

        $this->actingAsRole(Role::Admin, 'admin@example.test');

        $response = $this->put(route('products.update', $product->id), $this->validPayload([
            'name' => 'Renamed Component',
        ]));

        $response->assertRedirect(route('products.show', $product->id));
        $response->assertSessionHas('success_dialog', 'Successfully Edited New Collection');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Renamed Component']);
    }

    public function test_a_super_admin_soft_deletes_and_restores_a_product(): void
    {
        /** @var Product $product */
        $product = app(ProductRepository::class)->find(1);
        $this->actingAsRole(Role::SuperAdmin, 'boss@example.test');

        $this->from(route('dashboard'))
            ->delete(route('products.destroy', $product->id))
            ->assertRedirect(route('dashboard'));

        $this->assertNotNull(DB::table('products')->where('id', $product->id)->value('deleted_at'));

        $this->get(route('deleted-items.index'))
            ->assertOk()
            ->assertSee($product->name);

        $this->patch(route('deleted-items.restore', $product->id), $this->validPayload([
            'name' => $product->name,
        ]))->assertRedirect(route('deleted-items.index'));

        $this->assertNull(DB::table('products')->where('id', $product->id)->value('deleted_at'));
    }

    public function test_the_edit_form_cancel_returns_to_the_referring_page(): void
    {
        /** @var Product $product */
        $product = app(ProductRepository::class)->find(1);
        $origin = route('categories.overview', ['category' => 'computer-components']);

        $this->actingAsRole(Role::SuperAdmin, 'boss@example.test');

        $this->from($origin)
            ->get(route('products.edit', $product->id))
            ->assertOk()
            ->assertSee('href="'.$origin.'"', false);
    }

    public function test_a_regular_user_cannot_delete_a_product(): void
    {
        /** @var Product $product */
        $product = app(ProductRepository::class)->find(1);

        $this->actingAsRole(Role::User, 'viewer@example.test')
            ->delete(route('products.destroy', $product->id))
            ->assertForbidden();

        $this->assertNull(DB::table('products')->where('id', $product->id)->value('deleted_at'));
    }
}
