<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FavoriteResource;
use App\Services\FavoriteService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class FavoriteController extends Controller
{
    public function __construct(
        private readonly FavoriteService $favorites,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(50, max(1, (int) $request->query('per_page', 15)));

        return FavoriteResource::collection($this->favorites->list($request->user(), $perPage));
    }

    public function store(Request $request): FavoriteResource
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $this->favorites->add($request->user(), (int) $data['product_id']);

        $favorite = $request->user()->favorites()
            ->where('product_id', $data['product_id'])
            ->with(['product.category', 'product.images'])
            ->first();

        return new FavoriteResource($favorite);
    }

    public function destroy(Request $request, int $product): Response
    {
        $this->favorites->remove($request->user(), $product);

        return response()->noContent();
    }
}
