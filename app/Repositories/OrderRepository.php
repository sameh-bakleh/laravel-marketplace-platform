<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{
    public function createWithItems(User $buyer, array $orderData, array $items): Order
    {
        return DB::transaction(function () use ($buyer, $orderData, $items): Order {
            /** @var Order $order */
            $order = Order::query()->create(array_merge($orderData, [
                'buyer_id' => $buyer->id,
            ]));

            foreach ($items as $row) {
                OrderItem::query()->create(array_merge($row, [
                    'order_id' => $order->id,
                ]));
            }

            return $order->load('items');
        });
    }

    public function findForBuyer(int $orderId, User $buyer): ?Order
    {
        return Order::query()
            ->with('items.product')
            ->whereKey($orderId)
            ->where('buyer_id', $buyer->id)
            ->first();
    }

    public function paginateForBuyer(User $buyer, int $perPage = 15): LengthAwarePaginator
    {
        return Order::query()
            ->with('items')
            ->where('buyer_id', $buyer->id)
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function paginateForSeller(User $seller, int $perPage = 15): LengthAwarePaginator
    {
        return Order::query()
            ->whereHas('items', fn ($q) => $q->where('seller_id', $seller->id))
            ->with([
                'buyer',
                'items' => fn ($q) => $q->where('seller_id', $seller->id),
            ])
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function updateStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);

        return $order->fresh();
    }
}
