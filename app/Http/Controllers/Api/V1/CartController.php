<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
    ) {}

    public function show(Request $request): JsonResponse
    {
        $cart = $this->cart->getCart($request->user());

        $items = [];
        foreach ($cart['items'] as $row) {
            $items[] = [
                'product_id' => $row['product_id'],
                'quantity' => $row['quantity'],
                'line_total' => $row['line_total'],
                'product' => new ProductResource($row['product']),
            ];
        }

        return response()->json([
            'data' => [
                'items' => $items,
                'subtotal' => (string) $cart['subtotal'],
                'currency' => $cart['currency'],
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:9999'],
        ]);

        $this->cart->add(
            $request->user(),
            (int) $data['product_id'],
            (int) ($data['quantity'] ?? 1),
        );

        return $this->show($request);
    }

    public function update(Request $request, int $product): JsonResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:9999'],
        ]);

        $this->cart->setQuantity($request->user(), $product, (int) $data['quantity']);

        return $this->show($request);
    }

    public function destroy(Request $request, int $product): JsonResponse
    {
        $this->cart->remove($request->user(), $product);

        return $this->show($request);
    }

    public function checkout(Request $request): OrderResource
    {
        $data = $request->validate([
            'shipping_address' => ['nullable', 'array'],
        ]);

        $order = $this->cart->checkout($request->user(), $data['shipping_address'] ?? null);

        return new OrderResource($order);
    }
}
