<?php

namespace Tests\Feature;

use App\Catalog\ProductRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PublicStorefrontTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedCatalog();
    }

    public function test_guests_can_open_the_storefront_homepage(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Trending Products', false)
            ->assertSee('Ideas for Your Next Upgrade', false)
            ->assertSee('Exclusive Deals for You!', false);
    }

    public function test_guests_can_browse_the_product_listing(): void
    {
        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('All Products', false);
    }

    public function test_guests_can_open_a_product_detail_page(): void
    {
        $product = app(ProductRepository::class)->find(1);

        $this->get(route('products.show', $product->id))
            ->assertOk()
            ->assertSeeText($product->name);
    }

    public function test_guests_can_search_the_catalog(): void
    {
        $this->get(route('search', ['q' => 'ssd']))
            ->assertOk();
    }

    public function test_guests_can_browse_a_category_page(): void
    {
        $this->get(route('categories.show', 'computer-components'))
            ->assertOk()
            ->assertSee('Computer Components', false);
    }

    public function test_guests_can_open_the_placeholder_pages(): void
    {
        $this->get(route('store.today'))->assertOk();
        $this->get(route('store.following'))->assertOk();
        $this->get(route('ideas'))->assertOk()->assertSee('Gaming Gear', false);
        $this->get(route('ideas.show', 'gaming-gear'))->assertOk()->assertSee('Gaming Gear', false);
    }

    public function test_an_unknown_idea_page_is_not_found(): void
    {
        $this->get(route('ideas.show', 'nope'))->assertNotFound();
    }

    public function test_guests_are_still_redirected_to_login_for_the_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_account_button_behaviour_has_a_login_route_for_guests(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(htmlspecialchars(route('login')), false);
    }

    public function test_deleted_products_are_hidden_from_the_storefront(): void
    {
        $products = app(ProductRepository::class);
        $first = $products->all()->take(3);

        foreach ($first as $product) {
            $products->delete($product);
        }

        $response = $this->get(route('home'))
            ->assertOk();

        foreach ($first as $product) {
            $response->assertDontSee($product->name, false);
        }
    }
}
