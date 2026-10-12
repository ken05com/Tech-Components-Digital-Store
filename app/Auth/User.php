<?php

namespace App\Auth;

use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * A user record backed by the query builder.
 *
 * This project deliberately ships without Eloquent, so the framework's generic
 * Authenticatable trait is used (it only needs the properties defined below).
 */
class User implements Authenticatable
{
    use AuthenticatableTrait;

    protected $rememberTokenName = 'remember_token';

    public function __construct(
        public ?int $id,
        public string $name,
        public string $email,
        public string $password,
        public Role $role,
        public ?string $remember_token = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {}

    public function getKeyName(): string
    {
        return 'id';
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function is(Role $role): bool
    {
        return $this->role === $role;
    }

    public function can(string $ability): bool
    {
        return $this->role->can($ability);
    }

    public function initials(): string
    {
        $words = preg_split('/\s+/', trim($this->name)) ?: [];
        $letters = array_map(
            fn (string $word) => strtoupper(substr($word, 0, 1)),
            array_slice(array_filter($words), 0, 2)
        );

        return $letters === [] ? 'TC' : implode('', $letters);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role->value,
        ];
    }

    /** @param array<string, mixed> $row */
    public static function fromArray(array $row): self
    {
        return new self(
            id: isset($row['id']) ? (int) $row['id'] : null,
            name: (string) ($row['name'] ?? ''),
            email: (string) ($row['email'] ?? ''),
            password: (string) ($row['password'] ?? ''),
            role: Role::tryFrom((string) ($row['role'] ?? '')) ?? Role::User,
            remember_token: $row['remember_token'] ?? null,
            created_at: $row['created_at'] ?? null,
            updated_at: $row['updated_at'] ?? null,
        );
    }
}
