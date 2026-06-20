# Contributing

Thanks for reviewing this portfolio project.

## Before you open a PR

1. Search existing issues to avoid duplicate work.
2. Keep changes focused — one concern per PR when possible.
3. Run `./vendor/bin/pint` and `php artisan test` before pushing.

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret --force
php artisan migrate --seed
php artisan test
```

## Mobile contract changes

If you modify `/api/login`, `/api/products`, or `/api/favorites`, update:

- `tests/Feature/MobileClientApiTest.php`
- `docs/MOBILE_CLIENT.md`
- [`ios-marketplace-product-app`](https://github.com/sameh-bakleh/ios-marketplace-product-app) if the Swift client must change too

## Do not submit

- Real `.env` files, JWT private keys, or API tokens
- Production deployment configs with live credentials
