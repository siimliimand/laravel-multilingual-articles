# Task List: Laravel Multilingual Articles

## 1. Project Scaffolding & Docker Setup

- [x] 1.1 Create Laravel 10+ project and initialize Git repository
- [x] 1.2 Create `docker-compose.yml` with `app` (php:8.2-fpm), `web` (nginx:alpine), and `db` (mariadb:10.11) services
- [x] 1.3 Create `docker/nginx/default.conf` Nginx configuration for Laravel
- [x] 1.4 Create `docker/php/Dockerfile` for PHP-FPM with required extensions (pdo_mysql, mbstring, etc.)
- [x] 1.5 Add `.env.example` with `APP_KEY`, `DB_*`, and `API_KEY` placeholder entries
- [x] 1.6 Verify `docker-compose up -d` starts all three services and the app is reachable

## 2. Database Migrations

- [x] 2.1 Create migration for `site_languages` table (`language_id` PK, `language_code` varchar(2), `language_name` varchar(45), timestamps)
- [x] 2.2 Create migration for `articles` table (`article_id` PK, `node_type` enum, `visibility` enum, `created_at`, `updated_at`, `deleted_at`)
- [x] 2.3 Create migration for `article_translations` table with all specified columns, FK to `articles.article_id`, FK to `site_languages.language_code`, and unique index on `(path, language_code)`
- [x] 2.4 Run `php artisan migrate` and verify all three tables are created correctly

## 3. Eloquent Models

- [x] 3.1 Create `SiteLanguage` model mapped to `site_languages` table
- [x] 3.2 Create `Article` model with `SoftDeletes`, `node_type`/`visibility` enum casts, and `hasMany(ArticleTranslation)` relationship
- [x] 3.3 Create `ArticleTranslation` model with `SoftDeletes`, `status` enum cast, `belongsTo(Article)`, and `belongsTo(SiteLanguage, 'language_code', 'language_code')` relationship

## 4. Seeders

- [x] 4.1 Create `SiteLanguageSeeder` to seed at least `en` (English) and `et` (Estonian) into `site_languages`
- [x] 4.2 Create `ArticleSeeder` with at least 2 predefined articles: one with `visibility = public` and one with `visibility = private`, each with translations in at least one language
- [x] 4.3 Wire seeders in `DatabaseSeeder` and run `php artisan db:seed` to verify

## 5. Service Layer

- [x] 5.1 Create `app/Services/ArticleService.php` with method `list(array $filters): LengthAwarePaginator` for filtered, paginated, sorted article list
- [x] 5.2 Implement filtering in `ArticleService::list()`: `title` (LIKE), `node_type`, `status`, `language_code`, `updated_at_from`, `updated_at_to` on `article_translations.updated_at`
- [x] 5.3 Implement default sort of `article_translations.updated_at` DESC in `ArticleService::list()`
- [x] 5.4 Add `ArticleService::getByPath(string $path, bool $isPrivate): ArticleTranslation` to retrieve a translation by path, enforcing visibility for public requests
- [x] 5.5 Add `ArticleService::getById(int $id): Article` to retrieve an article with all its translations
- [x] 5.6 Add `ArticleService::create(array $data): Article` to persist a new article and its translation
- [x] 5.7 Add `ArticleService::update(int $id, array $data): Article` to update an existing article and/or translation
- [x] 5.8 Bind `ArticleService` in the service container (or use direct instantiation in controller constructor)

## 6. Middleware

- [x] 6.1 Create `app/Http/Middleware/CheckApiKey.php` that reads `X-API-KEY` header and compares to `env('API_KEY')`
- [x] 6.2 Return HTTP 401 JSON response when API key is missing or invalid
- [x] 6.3 Register `CheckApiKey` middleware alias in `app/Http/Kernel.php` (e.g., as `api.key`)
- [x] 6.4 Add a request attribute or helper (e.g., `$request->isPrivateAccess()` or a flag) so controllers and services can distinguish public vs. private context

## 7. Form Request Validation Classes

- [ ] 7.1 Create `app/Http/Requests/StoreArticleRequest.php` with rules for `node_type`, `visibility`, `language_code`, `title`, `path`, `content`, `status` (required, enum values, max lengths, unique path per language)
- [ ] 7.2 Create `app/Http/Requests/UpdateArticleRequest.php` with `sometimes` rules for updatable fields and unique path scoping (ignore current translation)
- [ ] 7.3 Create `app/Http/Requests/ListArticleRequest.php` with optional filter parameters (`title`, `node_type`, `status`, `language_code`, `updated_at_from`, `updated_at_to`, `page`, `per_page`) and date validation for range fields

## 8. API Controller & Routes

- [ ] 8.1 Create `app/Http/Controllers/Api/ArticleController.php` with constructor injecting `ArticleService`
- [ ] 8.2 Implement `ArticleController::index(ListArticleRequest $request)` — returns paginated list via `ArticleService::list()`
- [ ] 8.3 Implement `ArticleController::showByPath(Request $request, string $path)` — returns article translation by path, using visibility flag from API key middleware
- [ ] 8.4 Implement `ArticleController::show(Request $request, int $id)` — returns single article with all translations
- [ ] 8.5 Implement `ArticleController::store(StoreArticleRequest $request)` — creates article via `ArticleService::create()`, returns HTTP 201
- [ ] 8.6 Implement `ArticleController::update(UpdateArticleRequest $request, int $id)` — updates article via `ArticleService::update()`, returns HTTP 200
- [ ] 8.7 Register API routes in `routes/api.php`:
  - `GET /api/articles` (public, with `CheckApiKey` optional behavior)
  - `GET /api/articles/by-path/{path}` (CheckApiKey middleware, visibility filtered on service level)
  - `GET /api/articles/{id}` (CheckApiKey required for private access)
  - `POST /api/articles` (CheckApiKey required)
  - `PUT /api/articles/{id}` (CheckApiKey required)
- [ ] 8.8 Ensure all JSON responses follow REST conventions (consistent structure with `data`, `message`, pagination metadata)

## 9. Feature Tests

- [ ] 9.1 Create `tests/Feature/ArticleTest.php` with `RefreshDatabase` trait
- [ ] 9.2 Write test: article list returns articles sorted by `updated_at` DESC by default
- [ ] 9.3 Write test: filter by `title` returns only matching articles
- [ ] 9.4 Write test: filter by `status` returns only matching articles
- [ ] 9.5 Write test: filter by `language_code` returns only matching articles
- [ ] 9.6 Write test: filter by `node_type` returns only articles with matching node_type
- [ ] 9.7 Write test: filter by `updated_at_from` and `updated_at_to` returns articles in range
- [ ] 9.8 Write test: combined filters narrow results correctly
- [ ] 9.9 Write test: pagination returns correct `per_page` count and metadata
- [ ] 9.10 Write test: retrieve public article by path without API key succeeds
- [ ] 9.11 Write test: retrieve private article by path without API key returns 403/404
- [ ] 9.12 Write test: retrieve private article by path with valid API key returns 200
- [ ] 9.13 Write test: retrieve article with invalid API key returns 401
- [ ] 9.14 Write test: create article with valid data returns 201 and persists to DB
- [ ] 9.15 Write test: create article with invalid data returns 422
- [ ] 9.16 Write test: update article with valid data returns 200 and updates DB
- [ ] 9.17 Run `php artisan test` and ensure all tests pass

## 10. Documentation

- [ ] 10.1 Write `README.md` with project description, prerequisites (Docker), and installation steps (`git clone`, `cp .env.example .env`, `docker-compose up`, `migrate --seed`)
- [ ] 10.2 Document all API endpoints in `README.md` with method, path, description, request parameters, and example cURL commands
- [ ] 10.3 Add `API_KEY` usage instructions to `README.md` (how to set and pass the key)
