<?php

namespace App\Repositories\Contracts;

use App\Models\CartItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface CartRepositoryInterface
{
    /**
     * @return Collection<int, CartItem>
     */
    public function linesWithProducts(User $user): Collection;

    public function upsertQuantity(User $user, int $productId, int $quantity): CartItem;

    public function remove(User $user, int $productId): void;

    public function clear(User $user): void;
}
