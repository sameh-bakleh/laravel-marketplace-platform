<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoMarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@marketplace.test'],
            [
                'name' => 'Admin User',
                'password' => $password,
                'role' => UserRole::Admin,
            ],
        );

        $sellerUser = User::query()->firstOrCreate(
            ['email' => 'seller@marketplace.test'],
            [
                'name' => 'Demo Seller',
                'password' => $password,
                'role' => UserRole::Seller,
            ],
        );

        SellerProfile::query()->firstOrCreate(
            ['user_id' => $sellerUser->id],
            [
                'store_name' => 'Demo Crafts Co.',
                'slug' => 'demo-crafts-co',
                'bio' => 'Handmade goods and digital templates.',
                'phone' => '+49-555-0100',
                'is_active' => true,
            ],
        );

        $buyer = User::query()->firstOrCreate(
            ['email' => 'buyer@marketplace.test'],
            [
                'name' => 'Demo Buyer',
                'password' => $password,
                'role' => UserRole::User,
            ],
        );

        $iosDemo = User::query()->firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo User',
                'password' => $password,
                'role' => UserRole::User,
            ],
        );

        $electronics = Category::query()->firstOrCreate(
            ['slug' => 'electronics'],
            ['name' => 'Electronics', 'description' => 'Devices and accessories', 'sort_order' => 1],
        );

        $home = Category::query()->firstOrCreate(
            ['slug' => 'home-living'],
            ['name' => 'Home & Living', 'description' => 'Decor and furniture', 'sort_order' => 2],
        );

        $catalog = [
            ['Wireless Earbuds Pro', 79.99, $electronics->id],
            ['USB-C Hub 7-in-1', 45.00, $electronics->id],
            ['Mechanical Keyboard', 129.00, $electronics->id],
            ['Portable SSD 1TB', 99.50, $electronics->id],
            ['Smart Watch Band', 24.99, $electronics->id],
            ['Noise Cancelling Headphones', 189.00, $electronics->id],
            ['Ceramic Pour-Over Set', 36.50, $home->id],
            ['Linen Throw Blanket', 58.00, $home->id],
            ['Scented Candle Trio', 22.00, $home->id],
            ['Bamboo Cutting Board', 31.00, $home->id],
            ['Minimal Desk Lamp', 44.00, $home->id],
            ['Glass Storage Jars (Set of 6)', 27.50, $home->id],
        ];

        $productIds = [];

        foreach ($catalog as $i => [$title, $price, $catId]) {
            $product = Product::query()->firstOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'seller_id' => $sellerUser->id,
                    'category_id' => $catId,
                    'title' => $title,
                    'description' => 'Demo listing #'.($i + 1).' — portfolio sample copy for catalog and pagination.',
                    'price' => $price,
                    'stock' => 25,
                    'sku' => 'DEMO-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                    'status' => 'published',
                ],
            );

            $productIds[] = $product->id;
        }

        foreach (array_slice($productIds, 0, 3) as $productId) {
            Favorite::query()->firstOrCreate([
                'user_id' => $iosDemo->id,
                'product_id' => $productId,
            ]);
        }

        $this->command?->info('Demo users (password: password):');
        $this->command?->info('  iOS client: '.$iosDemo->email);
        $this->command?->info('  Buyer:      '.$buyer->email);
        $this->command?->info('  Seller:     '.$sellerUser->email);
        $this->command?->info('  Admin:      '.$admin->email);
    }
}
