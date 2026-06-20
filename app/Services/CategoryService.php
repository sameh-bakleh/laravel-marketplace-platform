<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryService
{
    private const REDIS_TREE_KEY = 'marketplace:categories:flat:v1';

    public function __construct(
        private readonly CategoryRepositoryInterface $categories,
    ) {}

    public function all(): Collection
    {
        if (! $this->redisCacheEnabled()) {
            return $this->categories->allOrdered();
        }

        $rows = Cache::store('redis')->remember(
            self::REDIS_TREE_KEY,
            max(60, (int) config('marketplace.listings.ttl_seconds', 120) * 5),
            fn (): array => $this->categories->allOrdered()
                ->map(fn (Category $c) => $c->getAttributes())
                ->values()
                ->all(),
        );

        return Category::query()->hydrate($rows);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Category
    {
        $data['slug'] = $this->uniqueSlug(Str::slug((string) $data['name']));

        $created = $this->categories->create($data);
        $this->forgetCategoryCache();

        return $created;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Category $category, array $data): Category
    {
        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = $this->uniqueSlug(Str::slug((string) $data['name']), ignoreId: $category->id);
        }

        $updated = $this->categories->update($category, $data);
        $this->forgetCategoryCache();

        return $updated;
    }

    public function delete(Category $category): void
    {
        $this->categories->delete($category);
        $this->forgetCategoryCache();
    }

    private function redisCacheEnabled(): bool
    {
        $store = config('marketplace.listings.cache_store');

        return is_string($store)
            && $store === 'redis'
            && array_key_exists('redis', config('cache.stores', []));
    }

    private function forgetCategoryCache(): void
    {
        if (! $this->redisCacheEnabled()) {
            return;
        }

        Cache::store('redis')->forget(self::REDIS_TREE_KEY);
    }

    private function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = $base !== '' ? $base : 'category';
        $candidate = $slug;
        $i = 1;

        while (Category::query()
            ->where('slug', $candidate)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $candidate = $slug.'-'.$i;
            $i++;
        }

        return $candidate;
    }
}
