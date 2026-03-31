# Article Management

## ADDED Requirements

### Requirement: Create article with translation

The system SHALL allow creating an article along with at least one language translation via a POST request. The request MUST be validated using a Form Request class. The `ArticleService` SHALL handle persistence of both the `articles` record and the `article_translations` record.

The `node_type` field SHALL be an enum with the following accepted values: `article`, `user_agreement`. The `visibility` field SHALL be an enum with accepted values: `public`, `private`. The `status` field on `article_translations` SHALL be an enum with accepted values: `draft`, `published`, `unpublished`.

#### Scenario: Successful article creation

- **WHEN** a POST request is sent to `/api/articles` with valid `node_type`, `visibility`, `language_code`, `title`, `path`, `content`, and `status` fields
- **THEN** the system returns HTTP 201 with the created article and its translation in JSON format

#### Scenario: Validation failure on missing required fields

- **WHEN** a POST request is sent to `/api/articles` with missing required fields (e.g., no `title`)
- **THEN** the system returns HTTP 422 with a JSON error body listing the validation errors

#### Scenario: Duplicate path for same language

- **WHEN** a POST request is sent to `/api/articles` with a `path` and `language_code` combination that already exists
- **THEN** the system returns HTTP 422 with a validation error indicating the path must be unique per language

---

### Requirement: Update article and its translation

The system SHALL allow updating an existing article and its translation via a PUT/PATCH request. The `ArticleService` SHALL handle the update logic. Soft deletes SHALL be preserved (deleted records are not updatable).

#### Scenario: Successful article update

- **WHEN** a PUT request is sent to `/api/articles/{article_id}` with valid updated fields
- **THEN** the system returns HTTP 200 with the updated article and translation data

#### Scenario: Update non-existent article

- **WHEN** a PUT request is sent to `/api/articles/{article_id}` where the article does not exist
- **THEN** the system returns HTTP 404

#### Scenario: Validation failure on update

- **WHEN** a PUT request is sent to `/api/articles/{article_id}` with invalid data (e.g., `status` set to an unknown enum value)
- **THEN** the system returns HTTP 422 with validation error details

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
