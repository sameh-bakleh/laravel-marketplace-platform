# Mobile client contract (iOS)

This API exposes **unversioned** routes at `/api/*` that match the SwiftUI portfolio app [`ios-marketplace-product-app`](https://github.com/sameh-bakleh/ios-marketplace-product-app).

Implementation:

- Controllers: `app/Http/Controllers/Api/Mobile/`
- JSON resources: `app/Http/Resources/Mobile/MobileProductResource.php`, `MobileUserResource.php`
- Tests: `tests/Feature/MobileClientApiTest.php`

---

## Why a separate mobile surface?

The full marketplace API at `/api/v1/*` uses richer product JSON (`title`, nested `category`, `images[]`). The iOS client expects a **flat product shape** (`name`, `image_url`, string `category`) and **unversioned paths** (`POST /api/login`, not `/api/v1/auth/login`).

Both surfaces share the same services, repositories, and database — only the HTTP boundary differs.

---

## iOS app configuration

| Setting | Default |
|---------|---------|
| Base URL | `http://127.0.0.1:8000` |
| Scheme env override | `MARKETPLACE_API_BASE_URL` |
| UserDefaults override | `marketplace.api.baseURL` |

When this API runs via Docker Compose or `php artisan serve` on port **8000**, no iOS changes are required.

---

## Demo login

| Field | Value |
|-------|-------|
| Email | `demo@example.com` |
| Password | `password` |

Seeder pre-adds three favorites for this user so the Favorites tab is non-empty on first launch.

---

## Field mapping (Product)

| iOS / mobile JSON | Database / v1 API |
|-------------------|-------------------|
| `name` | `products.title` |
| `image_url` | First uploaded image URL, or `/demo/products/placeholder.svg` |
| `category` | `categories.name` (string) |
| `currency` | `EUR` (config: `marketplace.default_currency`) |
| `price` | Decimal as string, e.g. `"79.99"` |

---

## Quick verification

```bash
# Start API (from repo root)
docker compose up --build
# or: php artisan serve

# Login
curl -s -X POST http://127.0.0.1:8000/api/login \
  -H 'Content-Type: application/json' \
  -d '{"email":"demo@example.com","password":"password"}' | jq .

# Products (replace TOKEN)
curl -s -H "Authorization: Bearer TOKEN" \
  'http://127.0.0.1:8000/api/products?per_page=20' | jq '.meta, .data | length'
```

Then run the iOS app in Simulator and sign in with the same credentials.
