<?php

namespace App\Services;

use App\Models\SellerProfile;
use App\Models\User;
use App\Repositories\Contracts\SellerProfileRepositoryInterface;
use Illuminate\Support\Str;

class SellerProfileService
{
    public function __construct(
        private readonly SellerProfileRepositoryInterface $profiles,
    ) {}

    public function getBySlug(string $slug): ?SellerProfile
    {
        return $this->profiles->findBySlug($slug);
    }

    public function getForUser(User $user): ?SellerProfile
    {
        return $this->profiles->findByUserId($user->id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateMine(User $user, array $data): SellerProfile
    {
        $profile = $this->profiles->findByUserId($user->id);

        if (! $profile) {
            $profile = $this->profiles->createForUser($user, [
                'store_name' => $data['store_name'] ?? ($user->name.' Store'),
                'slug' => $this->uniqueSlug(Str::slug($data['store_name'] ?? $user->name)),
            ]);
        }

        if (isset($data['store_name']) && empty($data['slug'])) {
            $data['slug'] = $this->uniqueSlug(Str::slug((string) $data['store_name']), ignoreId: $profile->id);
        }

        return $this->profiles->update($profile, $data);
    }

    private function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = $base !== '' ? $base : 'store';
        $candidate = $slug;
        $i = 1;

        while (SellerProfile::query()
            ->where('slug', $candidate)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $candidate = $slug.'-'.$i;
            $i++;
        }

        return $candidate;
    }
}
