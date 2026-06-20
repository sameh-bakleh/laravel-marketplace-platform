<?php

namespace App\Http\Resources;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ProductImage */
class ProductImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $url = $this->resource->url();

        return [
            'id' => $this->id,
            'url' => $url,
            'sort_order' => $this->sort_order,
        ];
    }
}
