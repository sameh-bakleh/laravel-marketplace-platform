<?php

namespace App\Console\Commands;

use App\Support\DemoProductCatalog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncDemoProductImages extends Command
{
    protected $signature = 'demo:sync-product-images {--force : Re-download images even if they already exist}';

    protected $description = 'Download demo product placeholder JPEGs into public/demo/products';

    public function handle(): int
    {
        $directory = public_path('demo/products');

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            $this->error('Unable to create directory: '.$directory);

            return self::FAILURE;
        }

        $force = (bool) $this->option('force');
        $synced = 0;

        foreach (DemoProductCatalog::products() as $item) {
            $filename = DemoProductCatalog::imageFilename($item['title']);
            $target = $directory.DIRECTORY_SEPARATOR.$filename;

            if (! $force && is_file($target) && filesize($target) > 0) {
                $this->line("Skipped {$filename} (already exists)");

                continue;
            }

            $response = Http::timeout(30)
                ->withOptions(['allow_redirects' => true])
                ->get('https://picsum.photos/seed/'.$item['image_seed'].'/800/800.jpg');

            if (! $response->successful()) {
                $this->error("Failed to download {$filename} (HTTP {$response->status()})");

                return self::FAILURE;
            }

            file_put_contents($target, $response->body());
            $synced++;
            $this->info("Saved {$filename}");
        }

        $this->newLine();
        $this->info("Synced {$synced} product placeholder image(s) to public/demo/products.");

        return self::SUCCESS;
    }
}
