<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImageService
{
    public function store(Product $product, UploadedFile $file): ProductImage
    {
        $disk = config('marketplace.uploads.disk', 'public');
        $dir = trim(config('marketplace.uploads.product_path', 'products'), '/').'/'.$product->id;
        $name = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();

        $path = $file->storeAs($dir, $name, $disk);

        $maxSort = (int) $product->images()->max('sort_order');

        return $product->images()->create([
            'path' => $path,
            'disk' => $disk,
            'sort_order' => $maxSort + 1,
        ]);
    }

    public function delete(ProductImage $image, Product $product): void
    {
        if ($image->product_id !== $product->id) {
            abort(404);
        }

        Storage::disk($image->disk)->delete($image->path);
        $image->delete();
    }
}
