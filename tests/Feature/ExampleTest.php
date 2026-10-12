<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_home_page_opens_the_public_storefront(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Tech Components', false)
            ->assertSee('Latest Tech', false)
            ->assertSee('Gadgets', false);
    }
}
