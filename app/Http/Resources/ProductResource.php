<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Product */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (string) $this->price,
            'compare_at_price' => $this->compare_at_price !== null ? (string) $this->compare_at_price : null,
            'stock' => $this->stock,
            'sku' => $this->sku,
            'status' => $this->status,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'seller' => UserResource::make($this->whenLoaded('seller')),
        ];
    }
}
