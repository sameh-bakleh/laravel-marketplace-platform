<?php

namespace App\Repositories;

use App\Models\SellerProfile;
use App\Models\User;
use App\Repositories\Contracts\SellerProfileRepositoryInterface;

class SellerProfileRepository implements SellerProfileRepositoryInterface
{
    public function findByUserId(int $userId): ?SellerProfile
    {
        return SellerProfile::query()->where('user_id', $userId)->first();
    }

    public function findBySlug(string $slug): ?SellerProfile
    {
        return SellerProfile::query()
            ->with('user')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }

    public function createForUser(User $user, array $data): SellerProfile
    {
        return $user->sellerProfile()->create($data);
    }

    public function update(SellerProfile $profile, array $data): SellerProfile
    {
        $profile->update($data);

        return $profile->fresh();
    }
}
