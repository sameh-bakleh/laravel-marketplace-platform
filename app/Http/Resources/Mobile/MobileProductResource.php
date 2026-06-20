<?php

namespace App\Http\Resources\Mobile;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/** @mixin Product */
class MobileProductResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->title,
            'description' => $this->description,
            'price' => (string) $this->price,
            'compare_at_price' => $this->compare_at_price !== null ? (string) $this->compare_at_price : null,
            'currency' => config('marketplace.default_currency', 'EUR'),
            'image_url' => $this->resolveImageUrl($request),
            'category' => $this->relationLoaded('category') ? $this->category?->name : null,
        ];
    }

    private function resolveImageUrl(Request $request): ?string
    {
        if ($this->relationLoaded('images')) {
            $image = $this->images->first();
            if ($image !== null) {
                return $image->url();
            }
        }

        $slug = $this->slug ?: 'product';

        return url('/demo/products/'.Str::slug($this->title ?: $slug).'.jpg');
    }
}
