<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\MobileProductResource;
use App\Services\FavoriteService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

/**
 * iOS client contract: /api/favorites returns { data: [Product] }.
 */
class FavoriteController extends Controller
{
    public function __construct(
        private readonly FavoriteService $favorites,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return MobileProductResource::collection(
            $this->favorites->listProducts($request->user())
        );
    }

    public function store(Request $request): Response
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $this->favorites->add($request->user(), (int) $data['product_id']);

        return response()->noContent();
    }

    public function destroy(Request $request, int $product): Response
    {
        $this->favorites->remove($request->user(), $product);

        return response()->noContent();
    }
}
