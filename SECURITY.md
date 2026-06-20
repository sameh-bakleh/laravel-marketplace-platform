# Security Policy

## Supported scope

This is a **portfolio / demonstration** API. It is not a production deployment.

## Reporting

If you find a vulnerability involving **committed secrets**, unsafe defaults in CI, or an issue that could affect users of a forked production deployment, open a private GitHub security advisory or email the maintainer.

## Known portfolio limitations

- Demo users and passwords in seeders (`password`)
- No login rate limiting
- No inventory locking on concurrent checkout (documented trade-off)
- JWT access tokens are stateless until expiry
- Docker Compose uses development database passwords

## Safe usage

- Copy `.env.example` to `.env` — never commit `.env`
- Run `php artisan jwt:secret` for local JWT keys
- Use synthetic data only; do not connect production storefronts or ERP systems
- Set `APP_DEBUG=false` in any shared environment

## Mobile client

The iOS app stores bearer tokens in the **Keychain**. API URLs are configured locally, not hardcoded for production hosts.
