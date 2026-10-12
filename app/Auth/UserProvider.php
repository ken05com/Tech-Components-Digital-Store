<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as UserProviderContract;
use Illuminate\Support\Facades\Hash;

/**
 * Query-builder user provider so authentication works without Eloquent.
 */
class UserProvider implements UserProviderContract
{
    public function __construct(private readonly UserRepository $users) {}

    public function retrieveById($identifier): ?Authenticatable
    {
        return $this->users->find((int) $identifier);
    }

    public function retrieveByToken($identifier, #[\SensitiveParameter] $token): ?Authenticatable
    {
        $user = $this->users->find((int) $identifier);

        if ($user === null) {
            return null;
        }

        $remembered = $user->getRememberToken();

        return ($remembered !== null && hash_equals($remembered, (string) $token)) ? $user : null;
    }

    public function updateRememberToken(Authenticatable $user, #[\SensitiveParameter] $token): void
    {
        $this->users->updateRememberToken((int) $user->getAuthIdentifier(), $token);
    }

    /**
     * @param  array<string, mixed>  $credentials
     */
    public function retrieveByCredentials(#[\SensitiveParameter] array $credentials): ?Authenticatable
    {
        $identifier = $credentials['email'] ?? $credentials['name'] ?? null;

        if (! is_string($identifier) || trim($identifier) === '') {
            return null;
        }

        return $this->users->findByEmailOrName(trim($identifier));
    }

    /**
     * @param  array<string, mixed>  $credentials
     */
    public function validateCredentials(Authenticatable $user, #[\SensitiveParameter] array $credentials): bool
    {
        return Hash::check((string) ($credentials['password'] ?? ''), $user->getAuthPassword());
    }

    /**
     * @param  array<string, mixed>  $credentials
     */
    public function rehashPasswordIfRequired(Authenticatable $user, #[\SensitiveParameter] array $credentials, bool $force = false): void
    {
        if (! isset($credentials['password'])) {
            return;
        }

        if (! $force && ! Hash::needsRehash($user->getAuthPassword())) {
            return;
        }

        $this->users->updatePasswordHash((int) $user->getAuthIdentifier(), Hash::make((string) $credentials['password']));
    }
}
