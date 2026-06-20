# Docker guide

## Stack

| Service | Image | Host port |
|---------|-------|-----------|
| `app` | PHP 8.4 CLI (built from `Dockerfile`) | **8000** |
| `mysql` | MySQL 8.0 | 33060 |
| `redis` | Redis 7 Alpine | 63790 |

On first start the app container runs:

1. `composer install`
2. `php artisan key:generate` (if needed)
3. `php artisan jwt:secret` (if needed)
4. `php artisan migrate --force`
5. `php artisan demo:sync-product-images`
6. `php artisan db:seed --force`
7. `php artisan serve --host=0.0.0.0 --port=8000`

## Commands

```bash
cp .env.example .env
docker compose up --build
```

API: http://localhost:8000  
Swagger: http://localhost:8000/api/documentation

Run tests inside the container:

```bash
docker compose exec app php artisan test
```

Reset database:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

## Host-side development

If you run PHP locally but MySQL/Redis in Docker:

```env
DB_HOST=127.0.0.1
DB_PORT=33060
REDIS_HOST=127.0.0.1
REDIS_PORT=63790
```

Generate JWT secret once:

```bash
php artisan jwt:secret --force
```

## iOS Simulator note

The iOS Simulator reaches the Mac host at `127.0.0.1`. With Compose binding `8000:8000`, the mobile app’s default base URL works without changes.
