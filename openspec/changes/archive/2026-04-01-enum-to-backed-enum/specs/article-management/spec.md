# Article Management

## MODIFIED Requirements

### Requirement: Create article with translation

The system SHALL allow creating an article along with at least one language translation via a POST request. The request MUST be validated using a Form Request class. The `ArticleService` SHALL handle persistence of both the `articles` record and the `article_translations` record.

The `node_type` field SHALL accept `NodeType` enum cases or their string values (`article`, `user_agreement`). The `visibility` field SHALL accept `Visibility` enum cases or their string values (`public`, `private`). The `status` field on `article_translations` SHALL be an enum with accepted values: `draft`, `published`, `unpublished`.

The `Article` model SHALL cast `node_type` to `NodeType::class` and `visibility` to `Visibility::class`.

#### Scenario: Successful article creation

- **WHEN** a POST request is sent to `/api/articles` with valid `node_type`, `visibility`, `language_code`, `title`, `path`, `content`, and `status` fields
- **THEN** the system returns HTTP 201 with the created article and its translation in JSON format with `node_type` and `visibility` as string values

#### Scenario: Validation failure on missing required fields

- **WHEN** a POST request is sent to `/api/articles` with missing required fields (e.g., no `title`)
- **THEN** the system returns HTTP 422 with a JSON error body listing the validation errors

#### Scenario: Duplicate path for same language

- **WHEN** a POST request is sent to `/api/articles` with a `path` and `language_code` combination that already exists
- **THEN** the system returns HTTP 422 with a validation error indicating the path must be unique per language

#### Scenario: Article creation with enum instance

- **WHEN** an article is created programmatically using `NodeType::ARTICLE` and `Visibility::PUBLIC` enum instances
- **THEN** the article is persisted with the correct string values and the model returns enum instances when accessed

---

### Requirement: Update article and its translation

The system SHALL allow updating an existing article and its translation via a PUT/PATCH request. The `ArticleService` SHALL handle the update logic. Soft deletes SHALL be preserved (deleted records are not updatable).

The `Article` model SHALL return `NodeType` and `Visibility` enum instances when accessing `node_type` and `visibility` attributes.

#### Scenario: Successful article update

- **WHEN** a PUT request is sent to `/api/articles/{article_id}` with valid updated fields
- **THEN** the system returns HTTP 200 with the updated article and translation data with enum fields as string values

#### Scenario: Update non-existent article

- **WHEN** a PUT request is sent to `/api/articles/{article_id}` where the article does not exist
- **THEN** the system returns HTTP 404

#### Scenario: Validation failure on update

- **WHEN** a PUT request is sent to `/api/articles/{article_id}` with invalid data (e.g., `status` set to an unknown enum value)
- **THEN** the system returns HTTP 422 with validation error details

#### Scenario: Model returns enum instances

- **WHEN** an article model is retrieved from the database
- **THEN** `$article->node_type` returns a `NodeType` enum instance and `$article->visibility` returns a `Visibility` enum instance
