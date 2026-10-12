<?php

namespace Tests\Feature;

use App\Auth\Role;
use App\Auth\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_the_login_screen_is_reachable_for_guests(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Sign');
    }

    public function test_a_user_can_authenticate_with_valid_credentials(): void
    {
        $this->makeUser(Role::SuperAdmin, 'boss@example.test', 'secret-password');
        $this->seedCatalog();

        $response = $this->post(route('login.store'), [
            'email' => 'boss@example.test',
            'password' => 'secret-password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_authentication_fails_with_a_wrong_password(): void
    {
        $this->makeUser(Role::User, 'viewer@example.test', 'secret-password');

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => 'viewer@example.test',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_a_user_can_sign_in_with_a_name_instead_of_an_email(): void
    {
        $this->seed(\Database\Seeders\UserSeeder::class);

        $response = $this->post(route('login.store'), [
            'email' => 'Admin',
            'password' => '12345',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_an_authenticated_user_can_log_out(): void
    {
        $this->actingAsRole(Role::User, 'viewer@example.test');

        $this->post(route('logout'))->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_the_user_provider_resolves_seeded_accounts(): void
    {
        $this->makeUser(Role::Admin, 'admin@example.test', 'secret-password');

        /** @var UserRepository $users */
        $users = app(UserRepository::class);

        $this->assertSame('admin@example.test', $users->findByEmail('admin@example.test')?->email);
        $this->assertNull($users->findByEmail('missing@example.test'));
    }
}
