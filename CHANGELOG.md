# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

### Changed

### Fixed

## [1.0.0] - 2026-06-20

### Added

- Multi-vendor marketplace REST API with JWT auth (register, login, refresh, logout)
- Role-based access for buyer, seller, and admin via middleware and policies
- Dual API surfaces: versioned `/api/v1/*` and mobile `/api/*` contract for the iOS client
- Catalog, cart, checkout, orders, favorites, seller product CRUD, and admin category management
- Repository and service layer with API Resources for stable JSON shapes
- Redis-backed listing cache with invalidation on writes
- OpenAPI/Swagger UI at `/api/documentation` via l5-swagger
- Demo seeder with 24 products, local JPEG placeholders, and iOS demo user with favorites
- Docker Compose stack (PHP, MySQL, Redis) with automated migrate, image sync, and seed
- Portfolio API landing page at `/` with docs and demo login links
- PHPUnit feature tests including mobile client contract tests
- GitHub Actions CI (Pint + PHPUnit + Composer audit)

[Unreleased]: https://github.com/sameh-bakleh/laravel-marketplace-platform/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/sameh-bakleh/laravel-marketplace-platform/releases/tag/v1.0.0
