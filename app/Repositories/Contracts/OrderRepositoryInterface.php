<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function createWithItems(User $buyer, array $orderData, array $items): Order;

    public function findForBuyer(int $orderId, User $buyer): ?Order;

    /** @return LengthAwarePaginator<Order> */
    public function paginateForBuyer(User $buyer, int $perPage = 15);

    /** @return LengthAwarePaginator<Order> */
    public function paginateForSeller(User $seller, int $perPage = 15);

    public function updateStatus(Order $order, string $status): Order;
}
