<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SellerProfile;
use App\Models\User;
use App\Support\DemoProductCatalog;
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

        SellerProfile::query()->updateOrCreate(
            ['user_id' => $sellerUser->id],
            [
                'store_name' => 'Demo Crafts Co.',
                'slug' => 'demo-crafts-co',
                'bio' => 'Curated electronics, home goods, and lifestyle essentials for portfolio demos.',
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

        $categories = collect([
            ['slug' => 'electronics', 'name' => 'Electronics', 'description' => 'Devices, audio, and desk accessories', 'sort_order' => 1],
            ['slug' => 'home-living', 'name' => 'Home & Living', 'description' => 'Decor, kitchen, and comfort', 'sort_order' => 2],
            ['slug' => 'fashion', 'name' => 'Fashion', 'description' => 'Apparel and everyday carry', 'sort_order' => 3],
            ['slug' => 'books-stationery', 'name' => 'Books & Stationery', 'description' => 'Planners, pens, and desk essentials', 'sort_order' => 4],
        ])->mapWithKeys(function (array $row) {
            $category = Category::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'sort_order' => $row['sort_order'],
                ],
            );

            return [$row['slug'] => $category];
        });

        $catalog = DemoProductCatalog::products();
        $productIds = [];

        foreach ($catalog as $i => $item) {
            $slug = Str::slug($item['title']);
            $category = $categories[$item['category']];
            $sku = 'DEMO-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT);
            $imagePath = DemoProductCatalog::publicImagePath($item['title']);

            $product = Product::query()->updateOrCreate(
                ['sku' => $sku],
                [
                    'seller_id' => $sellerUser->id,
                    'category_id' => $category->id,
                    'title' => $item['title'],
                    'slug' => $slug,
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'compare_at_price' => $item['compare_at_price'],
                    'stock' => $item['stock'],
                    'status' => 'published',
                ],
            );

            ProductImage::query()->updateOrCreate(
                [
                    'product_id' => $product->id,
                    'sort_order' => 0,
                ],
                [
                    'path' => $imagePath,
                    'disk' => 'demo',
                ],
            );

            $productIds[] = $product->id;
        }

        Favorite::query()->where('user_id', $iosDemo->id)->delete();

        foreach ([
            $productIds[0],
            $productIds[5],
            $productIds[16],
        ] as $productId) {
            Favorite::query()->firstOrCreate([
                'user_id' => $iosDemo->id,
                'product_id' => $productId,
            ]);
        }

        $this->command?->info('Seeded '.count($catalog).' products with local placeholder images.');
        $this->command?->info('Run `php artisan demo:sync-product-images` if JPEG files are missing from public/demo/products.');
        $this->command?->info('Demo users (password: password):');
        $this->command?->info('  iOS client: '.$iosDemo->email);
        $this->command?->info('  Buyer:      '.$buyer->email);
        $this->command?->info('  Seller:     '.$sellerUser->email);
        $this->command?->info('  Admin:      '.$admin->email);
    }
}
