<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\Contracts\SellerProfileRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function __construct(
        private readonly SellerProfileRepositoryInterface $sellerProfiles,
    ) {}

    /**
     * @param  array{name: string, email: string, password: string, as_seller?: bool, store_name?: string}  $data
     */
    public function register(array $data): User
    {
        $asSeller = ! empty($data['as_seller']);

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $asSeller ? UserRole::Seller : UserRole::User,
        ]);

        if ($asSeller) {
            $storeName = $data['store_name'] ?? ($user->name.' Store');
            $this->sellerProfiles->createForUser($user, [
                'store_name' => $storeName,
                'slug' => $this->uniqueSellerSlug(Str::slug($storeName)),
            ]);
        }

        return $user->fresh(['sellerProfile']);
    }

    public function login(string $email, string $password): ?string
    {
        $credentials = ['email' => $email, 'password' => $password];

        if (! $token = JWTAuth::attempt($credentials)) {
            return null;
        }

        return $token;
    }

    private function uniqueSellerSlug(string $base): string
    {
        $slug = $base !== '' ? $base : 'store';
        $candidate = $slug;
        $i = 1;

        while ($this->sellerProfiles->findBySlug($candidate)) {
            $candidate = $slug.'-'.$i;
            $i++;
        }

        return $candidate;
    }
}
