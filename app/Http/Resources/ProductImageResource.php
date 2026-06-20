<?php

namespace App\Http\Resources;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin ProductImage */
class ProductImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $url = Storage::disk($this->disk)->url($this->path);

        return [
            'id' => $this->id,
            'url' => $url,
            'sort_order' => $this->sort_order,
        ];
    }
}
