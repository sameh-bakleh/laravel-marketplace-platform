<?php

namespace App\Http\Controllers\Api\V1\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductImageResource;
use App\Services\ProductImageService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductImageController extends Controller
{
    public function __construct(
        private readonly ProductService $products,
        private readonly ProductImageService $images,
    ) {}

    public function store(Request $request, int $product): ProductImageResource
    {
        $model = $this->products->showForSeller($product, $request->user());
        if (! $model) {
            throw new NotFoundHttpException;
        }

        $request->validate([
            'image' => ['required', 'image', 'max:5120'],
        ]);

        $image = $this->images->store($model, $request->file('image'));

        return new ProductImageResource($image);
    }

    public function destroy(Request $request, int $product, int $image): Response
    {
        $model = $this->products->showForSeller($product, $request->user());
        if (! $model) {
            throw new NotFoundHttpException;
        }

        $img = $model->images()->whereKey($image)->first();
        if (! $img) {
            throw new NotFoundHttpException;
        }

        $this->images->delete($img, $model);

        return response()->noContent();
    }
}
