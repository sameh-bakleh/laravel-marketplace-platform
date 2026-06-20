<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Cache\ListingCache;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
        private readonly ListingCache $listingCache,
    ) {}

    /**
     * Paginated product lists are not cached as whole paginator objects (serialization limits).
     * Listing cache version is bumped on writes so CDNs or future edge caches can key off it.
     */
    public function listPublished(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->products->paginatePublished($filters, $perPage);
    }

    public function listForSeller(User $seller, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->products->paginateForSeller($seller, $filters, $perPage);
    }

    public function showPublished(int $id): ?Product
    {
        return $this->products->findPublished($id);
    }

    public function showForSeller(int $id, User $seller): ?Product
    {
        return $this->products->findForSeller($id, $seller);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createForSeller(User $seller, array $data): Product
    {
        $slug = $this->uniqueProductSlug(Str::slug((string) $data['title']));

        $product = $this->products->create(array_merge($data, [
            'seller_id' => $seller->id,
            'slug' => $slug,
        ]));

        $this->listingCache->bumpProductListingsVersion();

        return $product->load(['category', 'images']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateForSeller(Product $product, array $data): Product
    {
        if (isset($data['title'])) {
            $data['slug'] = $this->uniqueProductSlug(Str::slug((string) $data['title']), ignoreId: $product->id);
        }

        $updated = $this->products->update($product, $data);
        $this->listingCache->bumpProductListingsVersion();

        return $updated->load(['category', 'images']);
    }

    public function deleteForSeller(Product $product): void
    {
        $this->products->delete($product);
        $this->listingCache->bumpProductListingsVersion();
    }

    private function uniqueProductSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = $base !== '' ? $base : 'product';
        $candidate = $slug;
        $i = 1;

        while (Product::query()
            ->where('slug', $candidate)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $candidate = $slug.'-'.$i;
            $i++;
        }

        return $candidate;
    }
}
