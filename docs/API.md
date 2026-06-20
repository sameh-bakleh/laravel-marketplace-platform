# API reference

Two REST surfaces share the same Laravel app:

| Surface | Prefix | OpenAPI |
|---------|--------|---------|
| Mobile (iOS) | `/api/login`, `/api/products`, `/api/favorites` | Documented in [MOBILE_CLIENT.md](MOBILE_CLIENT.md) |
| Marketplace v1 | `/api/v1/*` | Swagger UI at `/api/documentation` |

Global headers:

- `Accept: application/json`
- Authenticated routes: `Authorization: Bearer <jwt>`

---

## Mobile client (`/api/*`)

Designed for [`ios-marketplace-product-app`](https://github.com/sameh-bakleh/ios-marketplace-product-app).

### POST `/api/login`

```json
{ "email": "demo@example.com", "password": "password" }
```

**200**

```json
{
  "token": "<jwt>",
  "user": { "id": 1, "name": "Demo User", "email": "demo@example.com" }
}
```

### GET `/api/products` (auth required)

Query: `page`, `per_page` (default 20)

**200** — Laravel paginator:

```json
{
  "data": [
    {
      "id": 1,
      "name": "Wireless Earbuds Pro",
      "description": "…",
      "price": "79.99",
      "currency": "EUR",
      "image_url": "http://localhost:8000/demo/products/placeholder.svg",
      "category": "Electronics"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 20,
    "total": 12
  }
}
```

### GET `/api/products/{id}` (auth required)

**200** — single product object (not wrapped in `data`).

### GET `/api/favorites` (auth required)

**200**

```json
{
  "data": [
    { "id": 1, "name": "Wireless Earbuds Pro", "price": "79.99", "currency": "EUR" }
  ]
}
```

### POST `/api/favorites` (auth required)

```json
{ "product_id": 1 }
```

**204** No content.

### DELETE `/api/favorites/{product_id}` (auth required)

**204** No content. `{product_id}` is the **product** id, not the favorite row id.

---

## Marketplace v1 (`/api/v1/*`)

| Area | Endpoints |
|------|-----------|
| Auth | `POST auth/register`, `auth/login`, `auth/refresh`, `auth/logout`, `GET auth/me` |
| Catalog | `GET categories`, `GET products`, `GET products/{id}` (public read) |
| Favorites | `GET/POST favorites`, `DELETE favorites/{product}` |
| Cart | `GET cart`, `POST cart/items`, `PATCH cart/items/{product}`, `DELETE …`, `POST cart/checkout` |
| Orders | `GET/POST orders`, `GET orders/{id}`, seller/admin variants |
| Seller | `apiResource seller/products`, product images |
| Admin | Category CRUD |

Login response includes `token_type`, `expires_in`, and full `UserResource` (with role).

Generate Swagger after changes:

```bash
php artisan l5-swagger:generate
```

Open http://localhost:8000/api/documentation
