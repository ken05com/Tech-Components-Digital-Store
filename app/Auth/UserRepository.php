<?php

namespace App\Auth;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Query-builder access to the users table. No Eloquent, matching the rest of
 * the catalog layer.
 */
class UserRepository
{
    public function find(int $id): ?User
    {
        $row = DB::table('users')->where('id', $id)->first();

        return $row ? User::fromArray((array) $row) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $row = DB::table('users')->where('email', $email)->first();

        return $row ? User::fromArray((array) $row) : null;
    }

    public function findByEmailOrName(string $identifier): ?User
    {
        $normalized = trim($identifier);

        $row = DB::table('users')
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($normalized)])
            ->first();

        if ($row !== null) {
            return User::fromArray((array) $row);
        }

        $row = DB::table('users')
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($normalized)])
            ->first();

        return $row ? User::fromArray((array) $row) : null;
    }

    /** @return Collection<int, User> */
    public function all(): Collection
    {
        return DB::table('users')
            ->orderBy('name')
            ->get()
            ->map(fn ($row) => User::fromArray((array) $row));
    }

    /** @param array<string, mixed> $data */
    public function create(array $data): User
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        $id = DB::table('users')->insertGetId([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make((string) $data['password']),
            'role' => ($data['role'] instanceof Role ? $data['role'] : Role::tryFrom((string) $data['role']))?->value ?? Role::User->value,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $this->find($id);
    }

    public function updateRole(int $id, Role $role): void
    {
        DB::table('users')->where('id', $id)->update([
            'role' => $role->value,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Update a user's profile. The password is only touched when a non-empty
     * value is supplied.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(int $id, array $data): ?User
    {
        $fields = [
            'name' => (string) $data['name'],
            'email' => (string) $data['email'],
            'role' => ($data['role'] instanceof Role ? $data['role'] : Role::tryFrom((string) $data['role']))?->value ?? Role::User->value,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        if (! empty($data['password'])) {
            $fields['password'] = Hash::make((string) $data['password']);
        }

        DB::table('users')->where('id', $id)->update($fields);

        return $this->find($id);
    }

    public function delete(int $id): void
    {
        DB::table('users')->where('id', $id)->delete();
    }

    public function countByRole(Role $role): int
    {
        return DB::table('users')->where('role', $role->value)->count();
    }

    public function updatePasswordHash(int $id, string $hash): void
    {
        DB::table('users')->where('id', $id)->update([
            'password' => $hash,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function updateRememberToken(int $id, ?string $token): void
    {
        DB::table('users')->where('id', $id)->update([
            'remember_token' => $token,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function count(): int
    {
        return DB::table('users')->count();
    }

    /** Seeds or updates the initial accounts without leaking passwords. */
    public function upsert(string $email, string $name, string $password, Role $role): User
    {
        $existing = $this->findByEmail($email);

        if ($existing !== null) {
            DB::table('users')->where('id', $existing->id)->update([
                'name' => $name,
                'role' => $role->value,
                'password' => Hash::make($password),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            return $this->find($existing->id);
        }

        return $this->create(compact('name', 'email', 'password', 'role'));
    }
}
