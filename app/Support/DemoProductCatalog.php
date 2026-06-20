<?php

namespace App\Support;

use Illuminate\Support\Str;

class DemoProductCatalog
{
    /**
     * @return list<array{
     *     title: string,
     *     category: string,
     *     price: float,
     *     compare_at_price: float|null,
     *     stock: int,
     *     description: string,
     *     image_seed: string
     * }>
     */
    public static function products(): array
    {
        return [
            [
                'title' => 'Wireless Earbuds Pro',
                'category' => 'electronics',
                'price' => 79.99,
                'compare_at_price' => 99.99,
                'stock' => 42,
                'description' => 'Active noise reduction, 32-hour case battery, and IPX5 sweat resistance for commuting and workouts.',
                'image_seed' => 'marketplace-earbuds',
            ],
            [
                'title' => 'USB-C Hub 7-in-1',
                'category' => 'electronics',
                'price' => 45.00,
                'compare_at_price' => null,
                'stock' => 68,
                'description' => 'HDMI 4K, SD/microSD readers, and three USB-A ports in an aluminum shell for laptop travel setups.',
                'image_seed' => 'marketplace-usbc-hub',
            ],
            [
                'title' => 'Mechanical Keyboard',
                'category' => 'electronics',
                'price' => 129.00,
                'compare_at_price' => 149.00,
                'stock' => 19,
                'description' => 'Hot-swappable switches, per-key RGB, and a gasket-mounted plate for a quieter, premium typing feel.',
                'image_seed' => 'marketplace-keyboard',
            ],
            [
                'title' => 'Portable SSD 1TB',
                'category' => 'electronics',
                'price' => 99.50,
                'compare_at_price' => null,
                'stock' => 35,
                'description' => 'Pocket-sized NVMe drive with USB 3.2 Gen 2 speeds — ideal for video edits and backup on the go.',
                'image_seed' => 'marketplace-ssd',
            ],
            [
                'title' => 'Smart Watch Band',
                'category' => 'electronics',
                'price' => 24.99,
                'compare_at_price' => 34.99,
                'stock' => 120,
                'description' => 'Soft-touch silicone strap with stainless buckle; compatible with 42–46 mm watch cases.',
                'image_seed' => 'marketplace-watch-band',
            ],
            [
                'title' => 'Noise Cancelling Headphones',
                'category' => 'electronics',
                'price' => 189.00,
                'compare_at_price' => 229.00,
                'stock' => 27,
                'description' => 'Over-ear ANC with multipoint Bluetooth, 40-hour battery, and fold-flat carry case.',
                'image_seed' => 'marketplace-headphones',
            ],
            [
                'title' => 'Bluetooth Speaker',
                'category' => 'electronics',
                'price' => 64.00,
                'compare_at_price' => null,
                'stock' => 55,
                'description' => '360° sound, IP67 waterproof body, and 12-hour playback for kitchen counters and patio tables.',
                'image_seed' => 'marketplace-speaker',
            ],
            [
                'title' => 'Wireless Mouse',
                'category' => 'electronics',
                'price' => 39.00,
                'compare_at_price' => 49.00,
                'stock' => 88,
                'description' => 'Ergonomic shell, silent clicks, and a USB-C rechargeable cell that lasts six weeks per charge.',
                'image_seed' => 'marketplace-mouse',
            ],
            [
                'title' => 'Ceramic Pour-Over Set',
                'category' => 'home-living',
                'price' => 36.50,
                'compare_at_price' => null,
                'stock' => 44,
                'description' => 'Dripper, carafe, and 100 filters — brews a clean cup with bright acidity for single-origin beans.',
                'image_seed' => 'marketplace-pour-over',
            ],
            [
                'title' => 'Linen Throw Blanket',
                'category' => 'home-living',
                'price' => 58.00,
                'compare_at_price' => 72.00,
                'stock' => 31,
                'description' => 'Stone-washed European linen in a neutral oatmeal tone; breathable for sofa naps year-round.',
                'image_seed' => 'marketplace-blanket',
            ],
            [
                'title' => 'Scented Candle Trio',
                'category' => 'home-living',
                'price' => 22.00,
                'compare_at_price' => null,
                'stock' => 85,
                'description' => 'Soy wax candles in cedar, fig, and bergamot — 25-hour burn time each, reusable glass jars.',
                'image_seed' => 'marketplace-candles',
            ],
            [
                'title' => 'Bamboo Cutting Board',
                'category' => 'home-living',
                'price' => 31.00,
                'compare_at_price' => null,
                'stock' => 52,
                'description' => 'End-grain bamboo with juice groove and non-slip feet; gentle on knife edges.',
                'image_seed' => 'marketplace-cutting-board',
            ],
            [
                'title' => 'Minimal Desk Lamp',
                'category' => 'home-living',
                'price' => 44.00,
                'compare_at_price' => 55.00,
                'stock' => 38,
                'description' => 'Adjustable arm, warm-dim LED, and touch dimmer — fits compact desks without glare.',
                'image_seed' => 'marketplace-desk-lamp',
            ],
            [
                'title' => 'Glass Storage Jars (Set of 6)',
                'category' => 'home-living',
                'price' => 27.50,
                'compare_at_price' => null,
                'stock' => 64,
                'description' => 'Airtight bamboo lids for pantry staples, spices, and overnight oats — dishwasher safe.',
                'image_seed' => 'marketplace-jars',
            ],
            [
                'title' => 'Stainless Water Bottle',
                'category' => 'home-living',
                'price' => 26.00,
                'compare_at_price' => null,
                'stock' => 102,
                'description' => 'Double-wall vacuum insulation keeps drinks cold 24 h or hot 12 h; fits standard cup holders.',
                'image_seed' => 'marketplace-bottle',
            ],
            [
                'title' => 'Organic Cotton T-Shirt',
                'category' => 'fashion',
                'price' => 28.00,
                'compare_at_price' => null,
                'stock' => 90,
                'description' => 'GOTS-certified cotton, relaxed fit, and garment-dyed finish that softens after every wash.',
                'image_seed' => 'marketplace-tshirt',
            ],
            [
                'title' => 'Leather Crossbody Bag',
                'category' => 'fashion',
                'price' => 118.00,
                'compare_at_price' => 145.00,
                'stock' => 16,
                'description' => 'Full-grain leather with adjustable strap, interior card slots, and magnetic closure.',
                'image_seed' => 'marketplace-crossbody',
            ],
            [
                'title' => 'Running Sneakers',
                'category' => 'fashion',
                'price' => 96.00,
                'compare_at_price' => null,
                'stock' => 48,
                'description' => 'Lightweight mesh upper, responsive foam midsole, and reflective accents for early-morning routes.',
                'image_seed' => 'marketplace-sneakers',
            ],
            [
                'title' => 'Wool Beanie',
                'category' => 'fashion',
                'price' => 21.00,
                'compare_at_price' => 28.00,
                'stock' => 74,
                'description' => 'Merino wool blend with a folded cuff — warm, breathable, and packable for travel.',
                'image_seed' => 'marketplace-beanie',
            ],
            [
                'title' => 'Canvas Tote Bag',
                'category' => 'fashion',
                'price' => 19.50,
                'compare_at_price' => null,
                'stock' => 66,
                'description' => 'Heavyweight organic canvas with interior pocket; ideal for groceries, books, and daily carry.',
                'image_seed' => 'marketplace-tote',
            ],
            [
                'title' => 'Productivity Planner',
                'category' => 'books-stationery',
                'price' => 18.50,
                'compare_at_price' => null,
                'stock' => 73,
                'description' => 'Undated 90-day layout with weekly reviews, habit tracker, and lay-flat binding.',
                'image_seed' => 'marketplace-planner',
            ],
            [
                'title' => 'Fountain Pen Set',
                'category' => 'books-stationery',
                'price' => 42.00,
                'compare_at_price' => 49.00,
                'stock' => 29,
                'description' => 'Medium nib, converter plus two blue/black cartridges, and a lined notebook gift box.',
                'image_seed' => 'marketplace-pen',
            ],
            [
                'title' => 'Desk Organizer Tray',
                'category' => 'books-stationery',
                'price' => 19.99,
                'compare_at_price' => null,
                'stock' => 56,
                'description' => 'Powder-coated steel compartments for pens, cables, and sticky notes — weighted base.',
                'image_seed' => 'marketplace-organizer',
            ],
            [
                'title' => 'Hardcover Notebook (3-Pack)',
                'category' => 'books-stationery',
                'price' => 24.00,
                'compare_at_price' => null,
                'stock' => 61,
                'description' => 'A5 dotted pages, lay-flat binding, and 120 gsm paper that handles fountain pen ink.',
                'image_seed' => 'marketplace-notebook',
            ],
        ];
    }

    public static function imageFilename(string $title): string
    {
        return Str::slug($title).'.jpg';
    }

    public static function publicImagePath(string $title): string
    {
        return '/demo/products/'.self::imageFilename($title);
    }

    public static function diskImagePath(string $title): string
    {
        return 'demo/products/'.self::imageFilename($title);
    }
}
