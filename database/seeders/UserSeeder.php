<?php

namespace Database\Seeders;

use App\Auth\Role;
use App\Auth\UserRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Provisions the initial accounts.
 *
 * The Super Admin is required. Its password comes from SUPER_ADMIN_PASSWORD
 * when set; otherwise a strong random password is generated and printed once.
 * Demo Admin/User accounts are only created when SEED_DEMO_USERS is truthy.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = app(UserRepository::class);

        $superEmail = (string) env('SUPER_ADMIN_EMAIL', 'SuperAdmin');
        $superPassword = (string) (env('SUPER_ADMIN_PASSWORD') ?: Str::password(16));

        $users->upsert($superEmail, (string) env('SUPER_ADMIN_NAME', 'Super Admin'), $superPassword, Role::SuperAdmin);

        $this->report('Super Admin', $superEmail, $superPassword);

        if (filter_var(env('SEED_DEMO_USERS', false), FILTER_VALIDATE_BOOLEAN)) {
            $adminEmail = (string) env('DEMO_ADMIN_EMAIL', 'Admin');
            $userEmail = (string) env('DEMO_USER_EMAIL', 'User');
            $adminPassword = (string) (env('DEMO_ADMIN_PASSWORD') ?: Str::password(16));
            $userPassword = (string) (env('DEMO_USER_PASSWORD') ?: Str::password(16));

            $users->upsert($adminEmail, 'Admin', $adminPassword, Role::Admin);
            $users->upsert($userEmail, 'User', $userPassword, Role::User);

            $this->report('Admin', $adminEmail, $adminPassword);
            $this->report('User', $userEmail, $userPassword);
        }
    }

    private function report(string $role, string $email, string $password): void
    {
        $this->command?->newLine();
        $this->command?->info("{$role} account ready");
        $this->command?->line("  Email:    {$email}");
        $this->command?->line("  Password: {$password}");
        $this->command?->warn('  Store this password now — it is only shown once.');
    }
}
