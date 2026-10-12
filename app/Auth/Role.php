<?php

namespace App\Auth;

/**
 * The three roles from the whiteboard: Super Admin, Admin and User.
 *
 * All privilege rules live here so they can be adjusted in one place. Gates
 * registered in AppServiceProvider map these abilities onto the framework.
 */
enum Role: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case User = 'user';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::User => 'User',
        };
    }

    /** Badge tone understood by the ui.badge component. */
    public function tone(): string
    {
        return match ($this) {
            self::SuperAdmin => 'accent',
            self::Admin => 'sky',
            self::User => 'slate',
        };
    }

    /**
     * The abilities granted to this role.
     *
     * @return array<int, string>
     */
    public function abilities(): array
    {
        return match ($this) {
            self::SuperAdmin => [
                'view-dashboard',
                'view-catalog',
                'manage-products',
                'delete-products',
                'view-deleted',
                'restore-products',
                'force-delete',
                'manage-settings',
                'reset-catalog',
                'manage-users',
                'manage-categories',
                'view-inventory',
                'view-sales',
            ],
            self::Admin => [
                'view-dashboard',
                'view-catalog',
                'manage-products',
                'manage-categories',
                'view-inventory',
                'view-sales',
            ],
            self::User => [
                'view-dashboard',
                'view-catalog',
            ],
        };
    }

    public function can(string $ability): bool
    {
        return in_array($ability, $this->abilities(), true);
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_map(fn (self $role) => $role->value, self::cases());
    }
}
