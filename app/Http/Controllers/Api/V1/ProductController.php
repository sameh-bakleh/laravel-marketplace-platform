<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $products,
    ) {}

    #[OA\Get(path: '/products', summary: 'List published products', tags: ['Products'])]
    #[OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'category_id', in: 'query', schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Paginated products')]
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['search', 'category_id', 'min_price', 'max_price']);
        $perPage = min(50, max(1, (int) $request->query('per_page', 15)));

        $paginator = $this->products->listPublished($filters, $perPage);

        return ProductResource::collection($paginator);
    }

    public function show(int $product): ProductResource
    {
        $model = $this->products->showPublished($product);
        if (! $model) {
            throw new NotFoundHttpException;
        }

        return new ProductResource($model);
    }
}
