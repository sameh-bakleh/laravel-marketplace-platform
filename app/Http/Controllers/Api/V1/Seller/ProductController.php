<?php

namespace App\Http\Controllers\Api\V1\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $products,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['search', 'category_id', 'min_price', 'max_price', 'status']);
        $perPage = min(50, max(1, (int) $request->query('per_page', 15)));

        $paginator = $this->products->listForSeller($request->user(), $filters, $perPage);

        return ProductResource::collection($paginator);
    }

    public function store(Request $request): ProductResource
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'max:64', 'unique:products,sku'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);

        $product = $this->products->createForSeller($request->user(), $data);

        return new ProductResource($product);
    }

    public function show(Request $request, int $product): ProductResource
    {
        $model = $this->products->showForSeller($product, $request->user());
        if (! $model) {
            throw new NotFoundHttpException;
        }

        return new ProductResource($model);
    }

    public function update(Request $request, int $product): ProductResource
    {
        $model = $this->products->showForSeller($product, $request->user());
        if (! $model) {
            throw new NotFoundHttpException;
        }

        $data = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'max:64', 'unique:products,sku,'.$model->id],
            'status' => ['sometimes', 'in:draft,published,archived'],
        ]);

        $updated = $this->products->updateForSeller($model, $data);

        return new ProductResource($updated);
    }

    public function destroy(Request $request, int $product): Response
    {
        $model = $this->products->showForSeller($product, $request->user());
        if (! $model) {
            throw new NotFoundHttpException;
        }

        $this->products->deleteForSeller($model);

        return response()->noContent();
    }
}
