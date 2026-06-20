<?php

namespace App\Http\Resources;

use App\Models\SellerProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin SellerProfile */
class SellerProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'store_name' => $this->store_name,
            'slug' => $this->slug,
            'bio' => $this->bio,
            'phone' => $this->phone,
            'avatar_path' => $this->avatar_path,
            'is_active' => $this->is_active,
            'user' => UserResource::make($this->whenLoaded('user')),
        ];
    }
}
