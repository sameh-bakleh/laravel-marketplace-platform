<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\MobileProductResource;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * iOS client contract: authenticated GET /api/products (+ pagination meta).
 */
class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $products,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['search', 'category_id', 'min_price', 'max_price']);
        $perPage = min(50, max(1, (int) $request->query('per_page', 20)));

        $paginator = $this->products->listPublished($filters, $perPage);

        return MobileProductResource::collection($paginator);
    }

    public function show(int $product): MobileProductResource
    {
        $model = $this->products->showPublished($product);
        if (! $model) {
            throw new NotFoundHttpException;
        }

        return new MobileProductResource($model);
    }
}
