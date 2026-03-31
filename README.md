# Laravel Multilingual Articles API

A RESTful API backend built with Laravel 10+ for managing multilingual articles. Supports public/private visibility, filtering, sorting, and pagination. Containerized with Docker Compose (PHP-FPM, Nginx, MariaDB).

---

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/) (v20+)
- [Docker Compose](https://docs.docker.com/compose/install/) (v2+)

No local PHP or Composer installation is required — everything runs inside Docker containers.

---

## Installation

1. **Clone the repository**

    ```bash
    git clone git@github.com:siimliimand/laravel-multilingual-articles.git
    cd laravel-multilingual-articles
    ```

2. **Copy the environment file**

    ```bash
    cp .env.example .env
    ```

3. **Configure the environment**

    Edit `.env` and set the required values:

    ```dotenv
    APP_KEY=           # Generate with: docker-compose exec app php artisan key:generate
    DB_DATABASE=laravel
    DB_USERNAME=laravel
    DB_PASSWORD=secret
    API_KEY=your-secret-api-key
    ```

4. **Start all services**

    ```bash
    docker-compose up -d
    ```

    This starts three services:
    - `app` — PHP 8.2-FPM (Laravel application)
    - `web` — Nginx reverse proxy (listens on port `8081`)
    - `db` — MariaDB 10.11

5. **Generate application key** (if not already set)

    ```bash
    docker-compose exec app php artisan key:generate
    ```

6. **Run migrations and seed the database**

    ```bash
    docker-compose exec app php artisan migrate --seed
    ```

The API is now available at: **`http://localhost:8081/api`**

---

## API Key Authentication

Some endpoints require an API key to access private articles or to create/update articles. The API key is configured via the `API_KEY` environment variable in `.env`.

Pass the key in request headers:

```
X-API-KEY: your-secret-api-key
```

**Access levels:**

| Request               | Articles returned                            |
| --------------------- | -------------------------------------------- |
| No `X-API-KEY` header | Public articles only (`visibility = public`) |
| Valid `X-API-KEY`     | Both public and private articles             |
| Invalid `X-API-KEY`   | HTTP 401 Unauthorized                        |

---

## API Endpoints

All responses follow the structure:

```json
{
  "data": { ... },
  "message": "..."
}
```

Paginated list responses also include a `meta` object:

```json
{
  "data": [ ... ],
  "message": "...",
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 72
  }
}
```

---

### `GET /api/articles` — List Articles

Returns a paginated, filtered list of article translations.

- **API Key:** Optional. Without a valid key, only `visibility = public` articles are returned.

#### Query Parameters

| Parameter         | Type    | Description                                                                               |
| ----------------- | ------- | ----------------------------------------------------------------------------------------- |
| `title`           | string  | Partial match on `article_translations.title` (SQL LIKE)                                  |
| `node_type`       | string  | Exact match on `articles.node_type`. Values: `article`, `user_agreement`                  |
| `status`          | string  | Exact match on `article_translations.status`. Values: `draft`, `published`, `unpublished` |
| `language_code`   | string  | Exact match on `article_translations.language_code` (e.g. `en`, `et`)                     |
| `updated_at_from` | date    | Filter translations updated on or after this date (`YYYY-MM-DD`)                          |
| `updated_at_to`   | date    | Filter translations updated on or before this date (`YYYY-MM-DD`)                         |
| `page`            | integer | Page number (default: `1`)                                                                |
| `per_page`        | integer | Items per page (default: `15`)                                                            |

Default sort order: `article_translations.updated_at` descending.

#### Example cURL

```bash
# List all public articles
curl http://localhost:8081/api/articles

# List with API key (includes private articles)
curl -H "X-API-KEY: your-secret-api-key" http://localhost:8081/api/articles

# Filter by language and status, paginated
curl "http://localhost:8081/api/articles?language_code=en&status=published&per_page=10"

# Filter by title and date range
curl "http://localhost:8081/api/articles?title=hello&updated_at_from=2024-01-01&updated_at_to=2024-12-31"
```

---

### `GET /api/articles/by-path/{path}` — Retrieve Article by Path

Retrieves a single article translation by its `path` field.

- **API Key:** Optional. Without a valid key, only `visibility = public` and `status = published` translations are returned.

#### URL Parameters

| Parameter | Description                                                                         |
| --------- | ----------------------------------------------------------------------------------- |
| `path`    | The slug/path of the article translation (supports slashes, e.g. `blog/my-article`) |

#### Responses

| Status  | Description                                                                  |
| ------- | ---------------------------------------------------------------------------- |
| 200     | Article translation found and returned                                       |
| 401     | Invalid API key provided                                                     |
| 403/404 | Article is private and no valid API key was provided, or path does not exist |

#### Example cURL

```bash
# Retrieve a public article by path
curl http://localhost:8081/api/articles/by-path/my-article-slug

# Retrieve a private article by path (requires API key)
curl -H "X-API-KEY: your-secret-api-key" \
  http://localhost:8081/api/articles/by-path/private/my-private-article
```

---

### `GET /api/articles/{id}` — Retrieve Article by ID

Retrieves a single article with all its translations by `article_id`.

- **API Key:** Optional. Without a valid key, only articles with `visibility = public` are returned.

#### URL Parameters

| Parameter | Type    | Description                     |
| --------- | ------- | ------------------------------- |
| `id`      | integer | The `article_id` of the article |

#### Responses

| Status | Description                           |
| ------ | ------------------------------------- |
| 200    | Article and all translations returned |
| 401    | Invalid API key provided              |
| 404    | Article not found or not accessible   |

#### Example cURL

```bash
# Retrieve a public article by ID
curl http://localhost:8081/api/articles/1

# Retrieve any article by ID (requires API key)
curl -H "X-API-KEY: your-secret-api-key" http://localhost:8081/api/articles/1
```

---

### `POST /api/articles` — Create Article

Creates a new article along with its initial translation.

- **API Key:** **Required.**

#### Request Body (JSON)

| Field           | Type   | Required | Description                                                             |
| --------------- | ------ | -------- | ----------------------------------------------------------------------- |
| `node_type`     | string | Yes      | `article` or `user_agreement`                                           |
| `visibility`    | string | Yes      | `public` or `private`                                                   |
| `language_code` | string | Yes      | Language code (e.g. `en`, `et`). Must exist in `site_languages`.        |
| `title`         | string | Yes      | Translation title (max 255 characters)                                  |
| `path`          | string | Yes      | URL path/slug (max 255 characters). Must be unique per `language_code`. |
| `content`       | string | Yes      | Full article content (text)                                             |
| `status`        | string | Yes      | `draft`, `published`, or `unpublished`                                  |

#### Responses

| Status | Description                                                            |
| ------ | ---------------------------------------------------------------------- |
| 201    | Article created successfully                                           |
| 401    | Missing or invalid API key                                             |
| 422    | Validation error (missing fields, invalid enum values, duplicate path) |

#### Example cURL

```bash
curl -X POST http://localhost:8081/api/articles \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: your-secret-api-key" \
  -d '{
    "node_type": "article",
    "visibility": "public",
    "language_code": "en",
    "title": "My First Article",
    "path": "my-first-article",
    "content": "This is the full content of the article.",
    "status": "published"
  }'
```

---

### `PUT /api/articles/{id}` — Update Article

Updates an existing article and/or its translation.

- **API Key:** **Required.**

#### URL Parameters

| Parameter | Type    | Description                               |
| --------- | ------- | ----------------------------------------- |
| `id`      | integer | The `article_id` of the article to update |

#### Request Body (JSON)

All fields are optional (include only those you want to update):

| Field           | Type   | Description                                                                                            |
| --------------- | ------ | ------------------------------------------------------------------------------------------------------ |
| `node_type`     | string | `article` or `user_agreement`                                                                          |
| `visibility`    | string | `public` or `private`                                                                                  |
| `language_code` | string | Language code (e.g. `en`, `et`)                                                                        |
| `title`         | string | Translation title (max 255 characters)                                                                 |
| `path`          | string | URL path/slug (max 255 characters). Must be unique per `language_code` (ignoring current translation). |
| `content`       | string | Full article content                                                                                   |
| `status`        | string | `draft`, `published`, or `unpublished`                                                                 |

#### Responses

| Status | Description                  |
| ------ | ---------------------------- |
| 200    | Article updated successfully |
| 401    | Missing or invalid API key   |
| 404    | Article not found            |
| 422    | Validation error             |

#### Example cURL

```bash
curl -X PUT http://localhost:8081/api/articles/1 \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: your-secret-api-key" \
  -d '{
    "title": "Updated Article Title",
    "status": "published"
  }'
```

---

## Enum Values Reference

| Field        | Model                  | Accepted Values                     |
| ------------ | ---------------------- | ----------------------------------- |
| `node_type`  | `articles`             | `article`, `user_agreement`         |
| `visibility` | `articles`             | `public`, `private`                 |
| `status`     | `article_translations` | `draft`, `published`, `unpublished` |

---

## Running Tests

```bash
docker-compose exec app php artisan test
```

---

## Stopping the Application

```bash
# Stop containers (preserves data)
docker-compose stop

# Stop and remove containers + volumes (destroys all data)
docker-compose down -v
```
