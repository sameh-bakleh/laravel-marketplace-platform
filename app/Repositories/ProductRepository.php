<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\User;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function paginatePublished(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $q = Product::query()
            ->with(['category', 'images', 'seller.sellerProfile'])
            ->where('status', 'published');

        $this->applyFilters($q, $filters);

        return $q->orderByDesc('id')->paginate($perPage);
    }

    public function paginateForSeller(User $seller, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $q = Product::query()
            ->with(['category', 'images'])
            ->where('seller_id', $seller->id);

        $this->applyFilters($q, $filters, includeUnpublished: true);

        return $q->orderByDesc('id')->paginate($perPage);
    }

    public function findPublished(int $id): ?Product
    {
        return Product::query()
            ->with(['category', 'images', 'seller.sellerProfile'])
            ->where('status', 'published')
            ->whereKey($id)
            ->first();
    }

    public function findForSeller(int $id, User $seller): ?Product
    {
        return Product::query()
            ->with(['category', 'images'])
            ->where('seller_id', $seller->id)
            ->whereKey($id)
            ->first();
    }

    public function create(array $data): Product
    {
        return Product::query()->create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->fresh();
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    public function findByIdsForOrder(array $ids): Collection
    {
        return Product::query()
            ->whereIn('id', $ids)
            ->where('status', 'published')
            ->lockForUpdate()
            ->get();
    }

    /**
     * @param  Builder<Product>  $q
     */
    private function applyFilters(Builder $q, array $filters, bool $includeUnpublished = false): void
    {
        if (! empty($filters['search'])) {
            $term = '%'.addcslashes((string) $filters['search'], '%_\\').'%';
            $q->where(function (Builder $inner) use ($term): void {
                $inner->where('title', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhere('sku', 'like', $term);
            });
        }

        if (! empty($filters['category_id'])) {
            $q->where('category_id', (int) $filters['category_id']);
        }

        if ($includeUnpublished && ! empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }

        if (! empty($filters['min_price'])) {
            $q->where('price', '>=', (float) $filters['min_price']);
        }

        if (! empty($filters['max_price'])) {
            $q->where('price', '<=', (float) $filters['max_price']);
        }
    }
}
