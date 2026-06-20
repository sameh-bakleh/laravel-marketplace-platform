<?php

namespace App\Repositories;

use App\Models\Favorite;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Contracts\FavoriteRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class FavoriteRepository implements FavoriteRepositoryInterface
{
    public function paginateForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Favorite::query()
            ->where('user_id', $user->id)
            ->with(['product.category', 'product.images'])
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function productsForUser(User $user): Collection
    {
        return Product::query()
            ->whereHas('favorites', fn ($query) => $query->where('user_id', $user->id))
            ->with(['category', 'images'])
            ->where('status', 'published')
            ->orderByDesc('id')
            ->get();
    }

    public function exists(User $user, int $productId): bool
    {
        return Favorite::query()
            ->where('user_id', $user->id)
            ->where('product_id', $productId)
            ->exists();
    }

    public function add(User $user, int $productId): Favorite
    {
        return Favorite::query()->firstOrCreate([
            'user_id' => $user->id,
            'product_id' => $productId,
        ]);
    }

    public function remove(User $user, int $productId): void
    {
        Favorite::query()
            ->where('user_id', $user->id)
            ->where('product_id', $productId)
            ->delete();
    }
}
