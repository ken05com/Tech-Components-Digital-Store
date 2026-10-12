<?php

namespace Tests;

use App\Auth\Role;
use App\Auth\User;
use App\Auth\UserRepository;
use Database\Seeders\CatalogSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    protected function makeUser(Role $role, ?string $email = null, string $password = 'password'): User
    {
        /** @var UserRepository $users */
        $users = app(UserRepository::class);

        return $users->create([
            'name' => 'Test '.$role->label(),
            'email' => $email ?? Str::lower($role->value).'-'.Str::random(6).'@example.test',
            'password' => $password,
            'role' => $role,
        ]);
    }

    protected function actingAsRole(Role $role, ?string $email = null): static
    {
        $this->actingAs($this->makeUser($role, $email));

        return $this;
    }

    protected function seedCatalog(): void
    {
        $this->seed(CatalogSeeder::class);
    }
}
