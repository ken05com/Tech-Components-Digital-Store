<?php

namespace Database\Seeders;

use App\Settings\SettingRepository;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = app(SettingRepository::class);

        // Persist the config defaults on first run so they are editable from
        // the Settings screen. Existing values are preserved.
        $settings->putIfMissing('author_name', (string) config('tech_components.author.name'));
        $settings->putIfMissing('author_address', (string) config('tech_components.author.address'));
        $settings->putIfMissing('author_image', (string) config('tech_components.author.image'));

        foreach (config('tech_components.reflections') as $reflection) {
            $settings->putIfMissing($reflection['key'].'_title', $reflection['title']);
            $settings->putIfMissing($reflection['key'].'_body', $reflection['body']);
        }
    }
}
