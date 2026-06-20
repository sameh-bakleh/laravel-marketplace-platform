<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\JWT;
use Tests\TestCase;

class MarketplaceWorkflowTest extends TestCase
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

    public function test_guest_can_list_published_products(): void
    {
        $seller = User::factory()->seller()->create();
        Product::factory()->count(2)->create([
            'seller_id' => $seller->id,
            'status' => 'published',
        ]);

        $this->getJson('/api/v1/products')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_cart_requires_authentication(): void
    {
        $seller = User::factory()->seller()->create();
        $product = Product::factory()->create([
            'seller_id' => $seller->id,
            'status' => 'published',
        ]);

        $this->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertUnauthorized();
    }

    public function test_buyer_can_checkout_cart_and_stock_decrements(): void
    {
        $seller = User::factory()->seller()->create();
        $product = Product::factory()->create([
            'seller_id' => $seller->id,
            'status' => 'published',
            'stock' => 10,
            'price' => 12.5,
        ]);
        $buyer = User::factory()->create();

        $this->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ], $this->bearer($buyer))->assertOk();

        $this->getJson('/api/v1/cart', $this->bearer($buyer))
            ->assertOk()
            ->assertJsonPath('data.subtotal', '25');

        $this->postJson('/api/v1/cart/checkout', [], $this->bearer($buyer))
            ->assertCreated()
            ->assertJsonPath('data.status', 'pending');

        $product->refresh();
        $this->assertSame(8, $product->stock);

        $this->getJson('/api/v1/cart', $this->bearer($buyer))
            ->assertOk()
            ->assertJsonPath('data.subtotal', '0');
    }

    public function test_login_returns_bearer_token(): void
    {
        User::factory()->create([
            'email' => 'buyer-flow@test.local',
            'password' => 'password12345',
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'buyer-flow@test.local',
            'password' => 'password12345',
        ])
            ->assertOk()
            ->assertJsonStructure(['token', 'user']);
    }

    public function test_seller_can_create_product_via_api(): void
    {
        $seller = User::factory()->seller()->create();

        $this->postJson(
            '/api/v1/seller/products',
            [
                'title' => 'Handmade Mug',
                'description' => 'Ceramic.',
                'price' => 22,
                'stock' => 5,
                'status' => 'published',
            ],
            $this->bearer($seller),
        )
            ->assertCreated()
            ->assertJsonPath('data.title', 'Handmade Mug')
            ->assertJsonPath('data.status', 'published');
    }

    public function test_admin_can_list_categories(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertTrue($admin->isAdmin());

        $this->getJson('/api/v1/auth/me', $this->bearer($admin))
            ->assertOk()
            ->assertJsonPath('data.id', $admin->id)
            ->assertJsonPath('data.role', 'admin');

        $this->getJson('/api/v1/admin/categories', $this->bearer($admin))
            ->assertOk();
    }

    public function test_non_admin_cannot_access_admin_categories(): void
    {
        $buyer = User::factory()->create();

        $this->getJson('/api/v1/admin/categories', $this->bearer($buyer))
            ->assertForbidden();
    }
}
