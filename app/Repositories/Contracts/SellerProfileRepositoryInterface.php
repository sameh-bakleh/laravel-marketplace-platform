<?php

namespace App\Repositories\Contracts;

use App\Models\SellerProfile;
use App\Models\User;

interface SellerProfileRepositoryInterface
{
    public function findByUserId(int $userId): ?SellerProfile;

    public function findBySlug(string $slug): ?SellerProfile;

    public function createForUser(User $user, array $data): SellerProfile;

    public function update(SellerProfile $profile, array $data): SellerProfile;
}
