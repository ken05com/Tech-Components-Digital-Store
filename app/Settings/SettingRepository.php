<?php

namespace App\Settings;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Query-builder key/value settings store with config-backed defaults, so the
 * dashboard's author information and reflection prompts stay editable without
 * touching code.
 */
class SettingRepository
{
    /** @var array<string, string|null>|null */
    private ?array $cache = null;

    /** @return array<string, string|null> */
    private function stored(): array
    {
        if ($this->cache === null) {
            $this->cache = DB::table('settings')
                ->pluck('value', 'key')
                ->all();
        }

        return $this->cache;
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $stored = $this->stored();

        if (array_key_exists($key, $stored) && $stored[$key] !== null && $stored[$key] !== '') {
            return $stored[$key];
        }

        return $default;
    }

    public function set(string $key, ?string $value): void
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('settings')->updateOrInsert(
            ['key' => $key],
            ['value' => $value, 'updated_at' => $now, 'created_at' => $now],
        );

        if ($this->cache !== null) {
            $this->cache[$key] = $value;
        }
    }

    /**
     * @param  array<string, string|null>  $values
     */
    public function putMany(array $values): void
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    /** Insert the value only when the key has never been stored. */
    public function putIfMissing(string $key, ?string $value): void
    {
        if (! DB::table('settings')->where('key', $key)->exists()) {
            $this->set($key, $value);
        }
    }

    /** @return array{name: string, address: string, image: string} */
    public function author(): array
    {
        return [
            'name' => (string) $this->get('author_name', config('tech_components.author.name')),
            'address' => (string) $this->get('author_address', config('tech_components.author.address')),
            'image' => (string) $this->get('author_image', config('tech_components.author.image')),
        ];
    }

    /**
     * @return array<int, array{key: string, title: string, body: string}>
     */
    public function reflections(): array
    {
        return array_map(function (array $reflection) {
            return [
                'key' => $reflection['key'],
                'title' => (string) $this->get($reflection['key'].'_title', $reflection['title']),
                'body' => (string) $this->get($reflection['key'].'_body', $reflection['body']),
            ];
        }, config('tech_components.reflections'));
    }

    /** Flush the in-request cache (used after writes in long-running processes). */
    public function flush(): void
    {
        $this->cache = null;
    }
}
