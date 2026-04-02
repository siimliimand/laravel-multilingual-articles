# Article Management

## ADDED Requirements

### Requirement: API Resources for article responses

The system SHALL use Laravel API Resources for all article-related JSON responses. An `ArticleResource` SHALL transform individual article/translation data, and an `ArticleCollection` SHALL handle paginated lists with metadata.

#### Scenario: Single article resource response

- **WHEN** an article is returned via `ArticleResource`
- **THEN** the response wraps data in a `data` key with consistent field formatting

#### Scenario: Article collection with pagination

- **WHEN** a paginated list is returned via `ArticleCollection`
- **THEN** the response includes `data`, `links`, and `meta` with pagination information

---

### Requirement: Enum-derived validation rules

The system SHALL derive validation rule values from enum classes instead of hardcoding string literals. Each enum class SHALL provide a `values()` method returning an array of valid string values. Form Request classes SHALL use these values in `in:` validation rules.

#### Scenario: NodeType validation from enum

- **WHEN** `StoreArticleRequest` validates `node_type`
- **THEN** the validation rule uses `in:` with values from `NodeType::values()` (e.g., `in:article,user_agreement`)

#### Scenario: Visibility validation from enum

- **WHEN** `StoreArticleRequest` validates `visibility`
- **THEN** the validation rule uses `in:` with values from `Visibility::values()` (e.g., `in:public,private`)

#### Scenario: TranslationStatus validation from enum

- **WHEN** `StoreArticleRequest` validates `status`
- **THEN** the validation rule uses `in:` with values from `TranslationStatus::values()` (e.g., `in:draft,published,unpublished`)

#### Scenario: Adding new enum value updates validation

- **WHEN** a new case is added to `NodeType` enum (e.g., `PAGE`)
- **THEN** validation rules automatically include the new value without code changes to Form Requests

---

### Requirement: Create article with translation

The system SHALL allow creating an article along with at least one language translation via a POST request. The request MUST be validated using a Form Request class. The `ArticleService` SHALL handle persistence of both the `articles` record and the `article_translations` record.

The `node_type` field SHALL accept `NodeType` enum cases or their string values (`article`, `user_agreement`). The `visibility` field SHALL accept `Visibility` enum cases or their string values (`public`, `private`). The `status` field on `article_translations` SHALL be a `TranslationStatus` enum with accepted values: `draft`, `published`, `unpublished`.

The `Article` model SHALL cast `node_type` to `NodeType::class` and `visibility` to `Visibility::class`. The `ArticleTranslation` model SHALL cast `status` to `TranslationStatus::class`.

The controller SHALL return responses using `ArticleResource`.

#### Scenario: Successful article creation

- **WHEN** a POST request is sent to `/api/articles` with valid `node_type`, `visibility`, `language_code`, `title`, `path`, `content`, and `status` fields
- **THEN** the system returns HTTP 201 with the created article wrapped in `ArticleResource`

#### Scenario: Validation failure on missing required fields

- **WHEN** a POST request is sent to `/api/articles` with missing required fields (e.g., no `title`)
- **THEN** the system returns HTTP 422 with a JSON error body listing the validation errors

#### Scenario: Duplicate path for same language

- **WHEN** a POST request is sent to `/api/articles` with a `path` and `language_code` combination that already exists
- **THEN** the system returns HTTP 422 with a validation error indicating the path must be unique per language

#### Scenario: Article creation with enum instance

- **WHEN** an article is created programmatically using `NodeType::ARTICLE`, `Visibility::PUBLIC`, and `TranslationStatus::DRAFT` enum instances
- **THEN** the article is persisted with the correct string values and the model returns enum instances when accessed

---

### Requirement: Update article and its translation

The system SHALL allow updating an existing article and its translation via a PUT/PATCH request. The `ArticleService` SHALL handle the update logic. Soft deletes SHALL be preserved (deleted records are not updatable).

The `Article` model SHALL return `NodeType` and `Visibility` enum instances when accessing `node_type` and `visibility` attributes. The `ArticleTranslation` model SHALL return `TranslationStatus` enum instances when accessing the `status` attribute.

Validation rules SHALL derive enum values from their respective enum classes.

The controller SHALL return responses using `ArticleResource`.

#### Scenario: Successful article update

- **WHEN** a PUT request is sent to `/api/articles/{article_id}` with valid updated fields
- **THEN** the system returns HTTP 200 with the updated article wrapped in `ArticleResource`

#### Scenario: Update non-existent article

- **WHEN** a PUT request is sent to `/api/articles/{article_id}` where the article does not exist
- **THEN** the system returns HTTP 404

#### Scenario: Validation failure on update

- **WHEN** a PUT request is sent to `/api/articles/{article_id}` with an invalid `status` value not in `TranslationStatus::values()`
- **THEN** the system returns HTTP 422 with validation error details

#### Scenario: Model returns enum instances

- **WHEN** an article model is retrieved from the database
- **THEN** `$article->node_type` returns a `NodeType` enum instance, `$article->visibility` returns a `Visibility` enum instance, and `$translation->status` returns a `TranslationStatus` enum instance

---

### Requirement: Soft delete support at database level

The system SHALL support soft-deleting articles by setting `deleted_at` on the `articles` record via Laravel's `SoftDeletes` trait. Soft-deleted articles SHALL NOT appear in list queries or retrieval endpoints. There is no HTTP DELETE endpoint — soft deletion is enforced at the ORM/database level only.

#### Scenario: Soft-deleted article not returned in list

- **WHEN** an article's `deleted_at` is set in the database
- **THEN** the soft-deleted article does not appear in any `GET /api/articles` response

#### Scenario: Soft-deleted article not returned by path

- **WHEN** an article's `deleted_at` is set in the database
- **THEN** a `GET /api/articles/by-path/{path}` request returns HTTP 404 for that article

---

### Requirement: Migrations and seeders

The system SHALL include database migrations for `site_languages`, `articles`, and `article_translations` tables with the exact schema specified. At least 2 predefined articles with translations SHALL be provided via seeders.

#### Scenario: Migrations run successfully

- **WHEN** `php artisan migrate` is executed on a fresh database
- **THEN** all three tables are created with the correct columns and indexes

#### Scenario: Seeders populate initial data

- **WHEN** `php artisan db:seed` is executed
- **THEN** at least 2 article records with their translations are present in the database
