<?php

namespace App\Repositories;

use App\Models\CartItem;
use App\Models\User;
use App\Repositories\Contracts\CartRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CartRepository implements CartRepositoryInterface
{
    public function linesWithProducts(User $user): Collection
    {
        return CartItem::query()
            ->where('user_id', $user->id)
            ->with(['product.category', 'product.images', 'product.seller.sellerProfile'])
            ->orderBy('id')
            ->get();
    }

    public function upsertQuantity(User $user, int $productId, int $quantity): CartItem
    {
        return CartItem::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $productId,
            ],
            [
                'quantity' => $quantity,
            ],
        );
    }

    public function remove(User $user, int $productId): void
    {
        CartItem::query()
            ->where('user_id', $user->id)
            ->where('product_id', $productId)
            ->delete();
    }

    public function clear(User $user): void
    {
        CartItem::query()->where('user_id', $user->id)->delete();
    }
}
