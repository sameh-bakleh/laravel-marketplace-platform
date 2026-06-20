<?php

namespace App\Services\Cache;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;

class ListingCache
{
    private const VERSION_KEY = 'marketplace:products:list:version';

    public function store(): Repository
    {
        $name = config('marketplace.listings.cache_store');

        if (! is_string($name) || ! array_key_exists($name, config('cache.stores', []))) {
            return Cache::store(config('cache.default'));
        }

        return Cache::store($name);
    }

    public function rememberProductsList(string $key, callable $callback): mixed
    {
        $ttl = max(1, (int) config('marketplace.listings.ttl_seconds', 120));

        return $this->store()->remember($key, $ttl, $callback);
    }

    public function bumpProductListingsVersion(): void
    {
        $this->store()->increment(self::VERSION_KEY);
    }

    public function listVersion(): int
    {
        $v = $this->store()->get(self::VERSION_KEY);

        return $v !== null ? (int) $v : 1;
    }

    public function productsListKey(array $filters, int $page, int $perPage): string
    {
        $payload = [
            'f' => $filters,
            'p' => $page,
            'pp' => $perPage,
            'v' => $this->listVersion(),
        ];

        return 'marketplace:products:list:'.hash('sha256', json_encode($payload));
    }
}
