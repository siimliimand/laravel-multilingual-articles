# Design Document: Laravel Multilingual Articles

## Context

This is a greenfield Laravel 10+ application. There is no existing codebase to migrate from. The application serves as a REST API backend for managing multilingual articles, exposed via JSON endpoints. The database layer uses MariaDB and the application is containerized with Docker Compose. The architecture must follow SOA principles with a clear separation between controllers, service classes, form request validators, and Eloquent models.

## Goals / Non-Goals

**Goals:**

- Scaffold a complete Laravel 10+ project with REST API endpoints for article management
- Implement a three-table schema: `site_languages`, `articles`, `article_translations`
- Provide an `ArticleService` class that owns all business logic
- Validate every request using Laravel Form Request classes
- Distinguish public vs. private article access via API key middleware
- Support multilingual content retrieval by `path` field
- Enable list queries with filtering, sorting (default: `updated_at` DESC), and pagination
- Provide Docker Compose with `app` (PHP-FPM), `web` (Nginx), and `db` (MariaDB) services
- Write feature tests covering CRUD, filtering, sorting, and public/private access

**Non-Goals:**

- Frontend/UI layer
- User authentication (JWT, session-based) — only API key middleware is required
- Multi-tenancy
- File/media uploads
- Full-text search engine integration (only SQL `LIKE` filtering)
- Caching layer (Redis, etc.)

## Decisions

### Decision 1: Service-Oriented Architecture via ArticleService

**Choice:** All business logic (filtering, pagination, create, update) lives in `app/Services/ArticleService.php`. Controllers are thin and delegate to the service.

**Rationale:** SOA principle requires business logic to be separated from HTTP layer. Controllers handle request/response transformation only.

**Alternatives considered:**

- Fat controllers: rejected — violates SOA and makes testing harder
- Repository pattern in addition: excluded from scope as overkill for this requirement size

---

### Decision 2: API Key Middleware for Public/Private Access

**Choice:** A custom `CheckApiKey` middleware checks for `X-API-KEY` header. If the key matches the configured secret, the request is treated as private (can access private articles). Public requests without a key can only access articles with `visibility = public`.

**Rationale:** Simple, stateless, easy to test. No OAuth complexity needed per requirements.

**Alternatives considered:**

- Laravel Sanctum: heavier, session/token management not required here
- Route-based separate prefixes: would duplicate routing; middleware on a single route group is cleaner

---

### Decision 3: Translations as Separate Table (Not JSON Column)

**Choice:** Multilingual content is stored in `article_translations` with one row per language. The `article_translations.path` is the retrieval key.

**Rationale:** Relational structure allows indexed queries on `path`, `language_code`, `status`, and `title`. JSON column would prevent efficient filtering.

**Alternatives considered:**

- Single `articles` table with JSON `translations` column: rejected — prevents SQL-level filtering and indexing on translation fields

---

### Decision 4: Filtering in ArticleService

**Choice:** Filtering by `title` (LIKE), `node_type`, `status`, `language_code`, and `updated_at` (from/to range on `article_translations.updated_at`) is handled by building an Eloquent query in `ArticleService::list()`.

**Rationale:** Centralizes query logic. Controllers pass validated filter arrays to the service.

---

### Decision 5: Docker Compose with PHP-FPM + Nginx + MariaDB

**Choice:** Three services: `app` (php:8.2-fpm), `web` (nginx:alpine), `db` (mariadb:10.11).

**Rationale:** Standard Laravel Docker setup. MariaDB is explicitly required. PHP 8.2 is stable and compatible with Laravel 10.

---

### Decision 6: Soft Deletes

**Choice:** Both `articles` and `article_translations` use `deleted_at` (Laravel `SoftDeletes` trait).

**Rationale:** Schema specifies `deleted_at` on both tables. Soft deletes preserve history.

## Risks / Trade-offs

- **[Risk] `path` uniqueness scope**: `path` in `article_translations` should be unique per `language_code`. If not enforced at DB level, duplicate paths could cause ambiguous retrieval. → **Mitigation**: Add a unique index on `(path, language_code)` in migration.

- **[Risk] API key in environment variable**: If `.env` is committed, the API key leaks. → **Mitigation**: `.env` is in `.gitignore`; `.env.example` is provided with placeholder.

- **[Risk] N+1 queries on article list with translations**: Joining translations in list queries could be expensive. → **Mitigation**: Use Eloquent eager loading (`with('translations')`) or join in `ArticleService`.

- **[Risk] Feature tests relying on seeders**: If seeder data changes, tests may break. → **Mitigation**: Tests use their own `RefreshDatabase` + factory data, not production seeders.

## Migration Plan

1. Clone repository
2. Copy `.env.example` to `.env` and set `API_KEY`, `DB_*` values
3. Run `docker-compose up -d`
4. Run `docker-compose exec app php artisan migrate --seed`
5. API is available at `http://localhost:8080/api`

**Rollback**: `docker-compose down -v` destroys all containers and volumes. No external state is modified.

## Open Questions

- None — all requirements are clear from the task specification.
