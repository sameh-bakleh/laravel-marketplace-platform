<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SellerProfileResource;
use App\Services\SellerProfileService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SellerProfileController extends Controller
{
    public function __construct(
        private readonly SellerProfileService $sellers,
    ) {}

    public function show(string $slug): SellerProfileResource
    {
        $profile = $this->sellers->getBySlug($slug);
        if (! $profile) {
            throw new NotFoundHttpException;
        }

        return new SellerProfileResource($profile->load('user'));
    }

    public function me(Request $request): SellerProfileResource
    {
        $profile = $this->sellers->getForUser($request->user());
        if (! $profile) {
            throw new NotFoundHttpException('Seller profile not found.');
        }

        return new SellerProfileResource($profile);
    }

    public function updateMe(Request $request): SellerProfileResource
    {
        $user = $request->user();
        $profileId = $user->sellerProfile?->id;

        $data = $request->validate([
            'store_name' => ['sometimes', 'string', 'max:255'],
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('seller_profiles', 'slug')->ignore($profileId),
            ],
            'bio' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:64'],
        ]);

        $profile = $this->sellers->updateMine($user, $data);

        return new SellerProfileResource($profile);
    }
}
