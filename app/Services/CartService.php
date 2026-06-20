<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Contracts\CartRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function __construct(
        private readonly CartRepositoryInterface $cart,
        private readonly OrderService $orders,
    ) {}

    public function getCart(User $user): array
    {
        $lines = $this->cart->linesWithProducts($user);

        $subtotal = 0.0;
        $items = [];

        foreach ($lines as $line) {
            $product = $line->product;
            if (! $product || $product->status !== 'published') {
                continue;
            }

            $qty = (int) $line->quantity;
            $lineTotal = (float) $product->price * $qty;
            $subtotal += $lineTotal;

            $items[] = [
                'product_id' => $product->id,
                'quantity' => $qty,
                'line_total' => round($lineTotal, 2),
                'product' => $product,
            ];
        }

        return [
            'items' => $items,
            'subtotal' => round($subtotal, 2),
            'currency' => 'USD',
        ];
    }

    public function add(User $user, int $productId, int $quantity): void
    {
        $quantity = max(1, $quantity);

        DB::transaction(function () use ($user, $productId, $quantity): void {
            $product = Product::query()
                ->whereKey($productId)
                ->where('status', 'published')
                ->lockForUpdate()
                ->first();

            if (! $product) {
                throw ValidationException::withMessages([
                    'product_id' => ['Product is not available.'],
                ]);
            }

            $existing = CartItem::query()
                ->where('user_id', $user->id)
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->first();

            $newQty = ($existing?->quantity ?? 0) + $quantity;

            if ($newQty > $product->stock) {
                throw ValidationException::withMessages([
                    'quantity' => ['Not enough stock for this product.'],
                ]);
            }

            $this->cart->upsertQuantity($user, $productId, $newQty);
        });
    }

    public function setQuantity(User $user, int $productId, int $quantity): void
    {
        if ($quantity < 1) {
            $this->cart->remove($user, $productId);

            return;
        }

        DB::transaction(function () use ($user, $productId, $quantity): void {
            $product = Product::query()
                ->whereKey($productId)
                ->where('status', 'published')
                ->lockForUpdate()
                ->first();

            if (! $product) {
                throw ValidationException::withMessages([
                    'product_id' => ['Product is not available.'],
                ]);
            }

            if ($quantity > $product->stock) {
                throw ValidationException::withMessages([
                    'quantity' => ['Not enough stock for this product.'],
                ]);
            }

            $this->cart->upsertQuantity($user, $productId, $quantity);
        });
    }

    public function remove(User $user, int $productId): void
    {
        $this->cart->remove($user, $productId);
    }

    public function checkout(User $user, ?array $shippingAddress = null): Order
    {
        $lines = $this->cart->linesWithProducts($user);

        $payload = [];
        foreach ($lines as $line) {
            $product = $line->product;
            if (! $product || $product->status !== 'published') {
                continue;
            }

            $payload[] = [
                'product_id' => (int) $line->product_id,
                'quantity' => (int) $line->quantity,
            ];
        }

        if ($payload === []) {
            throw ValidationException::withMessages([
                'cart' => ['Your cart is empty.'],
            ]);
        }

        $order = $this->orders->placeOrder($user, $payload, $shippingAddress);
        $this->cart->clear($user);

        return $order;
    }
}
