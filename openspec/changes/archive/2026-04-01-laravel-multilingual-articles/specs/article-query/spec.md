# Article Query

## ADDED Requirements

### Requirement: List articles with default sorting

The system SHALL return a paginated list of articles with their translations. The default sort order SHALL be `article_translations.updated_at` descending. The `ArticleService::list()` method SHALL handle the query. The Eloquent query MUST fully qualify the sort column as `article_translations.updated_at` to avoid SQL ambiguity when joining `articles` and `article_translations` (both tables have an `updated_at` column).

#### Scenario: List returns articles sorted by updated_at DESC by default

- **WHEN** a GET request is sent to `/api/articles` with no sort parameters
- **THEN** the system returns HTTP 200 with articles ordered by `article_translations.updated_at` descending

#### Scenario: Paginated list

- **WHEN** a GET request is sent to `/api/articles?page=1&per_page=5`
- **THEN** the system returns at most 5 articles and includes pagination metadata (total, per_page, current_page)

---

### Requirement: List visibility filtering by API key

The system SHALL filter article list results based on whether the caller provides a valid `X-API-KEY` header. Unauthenticated (public) callers SHALL only receive articles with `visibility = public`. Authenticated callers with a valid API key SHALL receive both public and private articles.

#### Scenario: Unauthenticated list returns only public articles

- **WHEN** a GET request is sent to `/api/articles` without an `X-API-KEY` header
- **THEN** the system returns HTTP 200 with only articles where `articles.visibility = 'public'`

#### Scenario: Authenticated list returns all articles

- **WHEN** a GET request is sent to `/api/articles` with a valid `X-API-KEY` header
- **THEN** the system returns HTTP 200 with both public and private articles

---

### Requirement: Filter articles by title

The system SHALL support filtering the article list by `title` using a partial match (SQL LIKE).

#### Scenario: Filter by title returns matching articles

- **WHEN** a GET request is sent to `/api/articles?title=foo`
- **THEN** the system returns only articles whose translation title contains "foo"

#### Scenario: Filter by title with no match returns empty list

- **WHEN** a GET request is sent to `/api/articles?title=nonexistent_xyz`
- **THEN** the system returns HTTP 200 with an empty data array

---

### Requirement: Filter articles by node_type

The system SHALL support filtering the article list by `node_type` (exact match on `articles.node_type`).

#### Scenario: Filter by node_type returns matching articles

- **WHEN** a GET request is sent to `/api/articles?node_type=article`
- **THEN** the system returns only articles with `node_type = 'article'`

---

### Requirement: Filter articles by status

The system SHALL support filtering the article list by `status` (exact match on `article_translations.status`).

#### Scenario: Filter by status returns matching articles

- **WHEN** a GET request is sent to `/api/articles?status=published`
- **THEN** the system returns only translations with `status = 'published'`

---

### Requirement: Filter articles by language_code

The system SHALL support filtering the article list by `language_code` (exact match on `article_translations.language_code`).

#### Scenario: Filter by language_code returns matching translations

- **WHEN** a GET request is sent to `/api/articles?language_code=en`
- **THEN** the system returns only translations with `language_code = 'en'`

---

### Requirement: Filter articles by updated_at date range

The system SHALL support filtering the article list by `updated_at` date range using `updated_at_from` and `updated_at_to` query parameters (applied to `article_translations.updated_at`).

#### Scenario: Filter by updated_at_from returns articles updated on or after the date

- **WHEN** a GET request is sent to `/api/articles?updated_at_from=2024-01-01`
- **THEN** the system returns only translations where `updated_at >= '2024-01-01'`

#### Scenario: Filter by updated_at_to returns articles updated on or before the date

- **WHEN** a GET request is sent to `/api/articles?updated_at_to=2024-12-31`
- **THEN** the system returns only translations where `updated_at <= '2024-12-31'`

#### Scenario: Filter by both updated_at_from and updated_at_to

- **WHEN** a GET request is sent to `/api/articles?updated_at_from=2024-01-01&updated_at_to=2024-12-31`
- **THEN** the system returns only translations where `updated_at` is within the date range

---

### Requirement: Combined filters

The system SHALL support applying multiple filters simultaneously.

#### Scenario: Multiple filters applied together

- **WHEN** a GET request is sent to `/api/articles?language_code=en&status=published&per_page=10`
- **THEN** the system returns only English published articles, paginated to 10 per page
