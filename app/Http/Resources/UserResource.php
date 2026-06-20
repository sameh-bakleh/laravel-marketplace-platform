<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role->value,
            'seller_profile' => $this->when(
                $this->relationLoaded('sellerProfile') && $this->sellerProfile !== null,
                fn () => new SellerProfileResource($this->sellerProfile)
            ),
        ];
    }
}
