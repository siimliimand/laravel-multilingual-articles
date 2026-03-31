# Proposal: Laravel Multilingual Articles

## Why

Content platforms require support for multiple languages to reach diverse audiences, yet a clean, maintainable REST API for managing multilingual articles with proper visibility controls (public/private) and Service-Oriented Architecture is missing from this project. This change establishes the full Laravel application foundation with Dockerized infrastructure.

## What Changes

- New Laravel 10+ application scaffolded with Eloquent ORM and REST API
- Three database tables: `site_languages`, `articles`, `article_translations` with migrations and seeders
- REST API endpoints for article creation, retrieval by path, list querying (with filtering, sorting, pagination), and updates
- Service layer (`ArticleService`) encapsulating all business logic
- Form Request validation classes for every API endpoint
- Middleware to distinguish public vs. private API usage via API key
- Dockerized setup with `docker-compose.yml` using MariaDB
- Feature test suite covering filtering, sorting, public/private retrieval, and CRUD operations
- `README.md` with installation instructions and API usage examples

## Capabilities

### New Capabilities

- `article-management`: Core CRUD operations for articles and their multilingual translations, including create, update, soft-delete
- `article-query`: List articles with filtering by `title`, `node_type`, `status`, `language_code`, `updated_at` (date range), default sort by `updated_at` descending, and pagination (`page`, `per_page`)
- `article-retrieval`: Retrieve a single article translation by `path`, supporting both public and private visibility
- `api-authentication`: API key-based middleware to gate private article access from public access
- `multilingual-support`: `site_languages` table and `article_translations` table enabling one article to have many language variants

### Modified Capabilities

- None

## Impact

- **Database**: New MariaDB schema with `site_languages`, `articles`, `article_translations` tables; migrations and seeders required
- **API**: New RESTful endpoints under `/api/articles`
- **Infrastructure**: Docker Compose setup with PHP, MariaDB, and Nginx services
- **Testing**: Feature tests in `tests/Feature/ArticleTest.php`
- **Dependencies**: Laravel 10+, Eloquent ORM, Laravel Sanctum or custom API key middleware
