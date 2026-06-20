<?php

namespace App\Repositories\Contracts;

use App\Models\Favorite;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface FavoriteRepositoryInterface
{
    public function paginateForUser(User $user, int $perPage = 15): LengthAwarePaginator;

    /** @return Collection<int, Product> */
    public function productsForUser(User $user): Collection;

    public function exists(User $user, int $productId): bool;

    public function add(User $user, int $productId): Favorite;

    public function remove(User $user, int $productId): void;
}
