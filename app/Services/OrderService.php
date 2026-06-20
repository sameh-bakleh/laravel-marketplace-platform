<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly ProductRepositoryInterface $products,
        private readonly NotificationRepositoryInterface $notifications,
    ) {}

    /**
     * @param  list<array{product_id: int, quantity: int}>  $lines
     */
    public function placeOrder(User $buyer, array $lines, ?array $shippingAddress = null): Order
    {
        if ($lines === []) {
            throw ValidationException::withMessages(['items' => ['At least one line item is required.']]);
        }

        return DB::transaction(function () use ($buyer, $lines, $shippingAddress): Order {
            $ids = array_values(array_unique(array_map(fn ($l) => (int) $l['product_id'], $lines)));
            $products = $this->products->findByIdsForOrder($ids)->keyBy('id');

            if ($products->count() !== count($ids)) {
                throw ValidationException::withMessages(['items' => ['One or more products are invalid.']]);
            }

            $qtyById = [];
            foreach ($lines as $line) {
                $pid = (int) $line['product_id'];
                $qtyById[$pid] = ($qtyById[$pid] ?? 0) + max(1, (int) $line['quantity']);
            }

            $items = [];
            $total = 0;

            foreach ($qtyById as $productId => $qty) {
                $product = $products->get($productId);
                if (! $product) {
                    throw ValidationException::withMessages(['items' => ['Invalid product.']]);
                }

                if ($product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'items' => ["Insufficient stock for: {$product->title}"],
                    ]);
                }

                $lineTotal = (float) $product->price * $qty;
                $total += $lineTotal;

                $items[] = [
                    'product_id' => $product->id,
                    'seller_id' => $product->seller_id,
                    'product_title' => $product->title,
                    'quantity' => $qty,
                    'unit_price' => $product->price,
                ];
            }

            $order = $this->orders->createWithItems($buyer, [
                'status' => 'pending',
                'total' => $total,
                'currency' => 'USD',
                'shipping_address' => $shippingAddress,
            ], $items);

            foreach ($qtyById as $productId => $qty) {
                $product = $products->get($productId);
                $product->decrement('stock', $qty);
            }

            $this->notifications->createForUser(
                $buyer,
                'order_placed',
                'Order received',
                'Your order #'.$order->public_id.' is pending.',
                ['order_id' => $order->id, 'public_id' => $order->public_id],
            );

            $sellerIds = collect($items)->pluck('seller_id')->unique()->values();
            foreach ($sellerIds as $sellerId) {
                $seller = User::query()->find($sellerId);
                if ($seller) {
                    $this->notifications->createForUser(
                        $seller,
                        'order_new',
                        'New order',
                        'You have a new order #'.$order->public_id,
                        ['order_id' => $order->id, 'public_id' => $order->public_id],
                    );
                }
            }

            return $order->load('items');
        });
    }

    public function listMine(User $buyer, int $perPage = 15): LengthAwarePaginator
    {
        return $this->orders->paginateForBuyer($buyer, $perPage);
    }

    public function showMine(int $orderId, User $buyer): ?Order
    {
        return $this->orders->findForBuyer($orderId, $buyer);
    }

    public function listForSeller(User $seller, int $perPage = 15): LengthAwarePaginator
    {
        return $this->orders->paginateForSeller($seller, $perPage);
    }

    public function updateStatus(Order $order, string $status, User $actor): Order
    {
        if ($actor->isAdmin()) {
            return $this->orders->updateStatus($order, $status);
        }

        if ($actor->isSeller() && $order->items()->where('seller_id', $actor->id)->exists()) {
            return $this->orders->updateStatus($order, $status);
        }

        abort(403);
    }
}
