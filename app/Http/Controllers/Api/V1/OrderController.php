<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orders,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(50, max(1, (int) $request->query('per_page', 15)));

        return OrderResource::collection($this->orders->listMine($request->user(), $perPage));
    }

    public function store(Request $request): OrderResource
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'shipping_address' => ['nullable', 'array'],
        ]);

        $order = $this->orders->placeOrder($request->user(), $data['items'], $data['shipping_address'] ?? null);

        return new OrderResource($order);
    }

    public function show(Request $request, int $order): OrderResource
    {
        $model = $this->orders->showMine($order, $request->user());
        if (! $model) {
            throw new NotFoundHttpException;
        }

        return new OrderResource($model);
    }

    public function sellerIndex(Request $request): AnonymousResourceCollection
    {
        $perPage = min(50, max(1, (int) $request->query('per_page', 15)));

        return OrderResource::collection($this->orders->listForSeller($request->user(), $perPage));
    }

    public function updateStatus(Request $request, Order $order): OrderResource
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,paid,shipped,cancelled'],
        ]);

        $updated = $this->orders->updateStatus($order, $data['status'], $request->user());

        return new OrderResource($updated->load(['items', 'buyer']));
    }
}
