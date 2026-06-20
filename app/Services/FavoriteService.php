<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Repositories\Contracts\FavoriteRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class FavoriteService
{
    public function __construct(
        private readonly FavoriteRepositoryInterface $favorites,
        private readonly ProductRepositoryInterface $products,
    ) {}

    public function list(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $this->favorites->paginateForUser($user, $perPage);
    }

    /** @return Collection<int, Product> */
    public function listProducts(User $user): Collection
    {
        return $this->favorites->productsForUser($user);
    }

    public function add(User $user, int $productId): void
    {
        if (! $this->products->findPublished($productId)) {
            throw ValidationException::withMessages(['product_id' => ['Product not found or not published.']]);
        }

        $this->favorites->add($user, $productId);
    }

    public function remove(User $user, int $productId): void
    {
        $this->favorites->remove($user, $productId);
    }
}
