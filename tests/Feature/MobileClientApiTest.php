<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\JWT;
use Tests\TestCase;

class MobileClientApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, string>
     */
    private function bearer(User $user): array
    {
        $token = app(JWT::class)->fromUser($user);

        return ['Authorization' => 'Bearer '.$token];
    }

    public function test_mobile_login_returns_token_and_user(): void
    {
        User::factory()->create([
            'email' => 'demo@example.com',
            'password' => 'password',
        ]);

        $this->postJson('/api/login', [
            'email' => 'demo@example.com',
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonPath('token', fn ($value) => is_string($value) && $value !== '')
            ->assertJsonPath('user.email', 'demo@example.com')
            ->assertJsonPath('user.name', fn ($value) => is_string($value))
            ->assertJsonMissingPath('user.role');
    }

    public function test_mobile_products_require_authentication(): void
    {
        $this->getJson('/api/products')->assertUnauthorized();
    }

    public function test_mobile_products_return_ios_shape(): void
    {
        $seller = User::factory()->seller()->create();
        Product::factory()->count(2)->create([
            'seller_id' => $seller->id,
            'status' => 'published',
            'title' => 'Demo Headphones',
        ]);
        $buyer = User::factory()->create();

        $this->getJson('/api/products?per_page=20', $this->bearer($buyer))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    ['id', 'name', 'price', 'currency', 'image_url', 'category'],
                ],
                'meta' => ['current_page', 'last_page'],
            ])
            ->assertJsonPath('data.0.name', 'Demo Headphones')
            ->assertJsonMissingPath('data.0.title');
    }

    public function test_mobile_product_show_is_unwrapped(): void
    {
        $seller = User::factory()->seller()->create();
        $product = Product::factory()->create([
            'seller_id' => $seller->id,
            'status' => 'published',
            'title' => 'Single Item',
        ]);
        $buyer = User::factory()->create();

        $this->getJson('/api/products/'.$product->id, $this->bearer($buyer))
            ->assertOk()
            ->assertJsonPath('id', $product->id)
            ->assertJsonPath('name', 'Single Item')
            ->assertJsonMissingPath('data');
    }

    public function test_mobile_favorites_return_flat_products(): void
    {
        $seller = User::factory()->seller()->create();
        $product = Product::factory()->create([
            'seller_id' => $seller->id,
            'status' => 'published',
            'title' => 'Favorited Item',
        ]);
        $buyer = User::factory()->create();

        $this->postJson('/api/favorites', [
            'product_id' => $product->id,
        ], $this->bearer($buyer))->assertNoContent();

        $this->getJson('/api/favorites', $this->bearer($buyer))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $product->id)
            ->assertJsonPath('data.0.name', 'Favorited Item')
            ->assertJsonMissingPath('data.0.product');
    }

    public function test_mobile_favorite_delete_by_product_id(): void
    {
        $seller = User::factory()->seller()->create();
        $product = Product::factory()->create([
            'seller_id' => $seller->id,
            'status' => 'published',
        ]);
        $buyer = User::factory()->create();

        $this->postJson('/api/favorites', [
            'product_id' => $product->id,
        ], $this->bearer($buyer))->assertNoContent();

        $this->deleteJson('/api/favorites/'.$product->id, [], $this->bearer($buyer))
            ->assertNoContent();

        $this->getJson('/api/favorites', $this->bearer($buyer))
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }
}
