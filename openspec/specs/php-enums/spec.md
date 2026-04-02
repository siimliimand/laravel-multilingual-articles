# PHP Enums

## ADDED Requirements

### Requirement: NodeType enum class

The system SHALL provide a PHP 8.1+ string-backed enum `App\Enums\NodeType` with the following cases:

- `ARTICLE` with value `'article'`
- `USER_AGREEMENT` with value `'user_agreement'`

The enum SHALL implement a `label()` method returning a human-readable display name for each case. The enum SHALL implement an `isArticle()` method that returns `true` when the case is `ARTICLE`.

#### Scenario: NodeType enum values

- **WHEN** the `NodeType` enum is accessed
- **THEN** `NodeType::ARTICLE->value` returns `'article'` and `NodeType::USER_AGREEMENT->value` returns `'user_agreement'`

#### Scenario: NodeType label method

- **WHEN** `NodeType::ARTICLE->label()` is called
- **THEN** the method returns `'Article'`

#### Scenario: NodeType isArticle method

- **WHEN** `NodeType::ARTICLE->isArticle()` is called
- **THEN** the method returns `true`

---

### Requirement: Visibility enum class

The system SHALL provide a PHP 8.1+ string-backed enum `App\Enums\Visibility` with the following cases:

- `PUBLIC` with value `'public'`
- `PRIVATE` with value `'private'`

The enum SHALL implement a `label()` method returning a human-readable display name for each case. The enum SHALL implement an `isPublic()` method that returns `true` when the case is `PUBLIC`.

#### Scenario: Visibility enum values

- **WHEN** the `Visibility` enum is accessed
- **THEN** `Visibility::PUBLIC->value` returns `'public'` and `Visibility::PRIVATE->value` returns `'private'`

#### Scenario: Visibility label method

- **WHEN** `Visibility::PUBLIC->label()` is called
- **THEN** the method returns `'Public'`

#### Scenario: Visibility isPublic method

- **WHEN** `Visibility::PUBLIC->isPublic()` is called
- **THEN** the method returns `true`

---

### Requirement: TranslationStatus enum class

The system SHALL provide a PHP 8.1+ string-backed enum `App\Enums\TranslationStatus` with the following cases:

- `DRAFT` with value `'draft'`
- `PUBLISHED` with value `'published'`
- `UNPUBLISHED` with value `'unpublished'`

The enum SHALL implement a `label()` method returning a human-readable display name for each case. The enum SHALL implement a `isPublished()` method that returns `true` when the case is `PUBLISHED`. The enum SHALL implement a static `values()` method returning an array of all string values.

#### Scenario: TranslationStatus enum values

- **WHEN** the `TranslationStatus` enum is accessed
- **THEN** `TranslationStatus::DRAFT->value` returns `'draft'`, `TranslationStatus::PUBLISHED->value` returns `'published'`, and `TranslationStatus::UNPUBLISHED->value` returns `'unpublished'`

#### Scenario: TranslationStatus label method

- **WHEN** `TranslationStatus::DRAFT->label()` is called
- **THEN** the method returns `'Draft'`

#### Scenario: TranslationStatus isPublished method

- **WHEN** `TranslationStatus::PUBLISHED->isPublished()` is called
- **THEN** the method returns `true`

#### Scenario: TranslationStatus values method

- **WHEN** `TranslationStatus::values()` is called
- **THEN** the method returns `['draft', 'published', 'unpublished']`

---

### Requirement: Existing enums provide values method

The `NodeType` and `Visibility` enums SHALL each implement a static `values()` method returning an array of all string values for use in validation rules.

#### Scenario: NodeType values method

- **WHEN** `NodeType::values()` is called
- **THEN** the method returns `['article', 'user_agreement']`

#### Scenario: Visibility values method

- **WHEN** `Visibility::values()` is called
- **THEN** the method returns `['public', 'private']`

---

### Requirement: Enum serialization

The enums SHALL serialize to their string values when converted to strings or JSON. This ensures API responses remain unchanged when enum instances are returned in model data.

#### Scenario: Enum serialization to string

- **WHEN** an enum instance is cast to string or returned in JSON
- **THEN** the enum's string value is used (e.g., `NodeType::ARTICLE` becomes `'article'`, `TranslationStatus::PUBLISHED` becomes `'published'`)

#### Scenario: Enum tryFrom validation

- **WHEN** `TranslationStatus::tryFrom('invalid_value')` is called
- **THEN** the method returns `null`

---

### Requirement: Database columns use VARCHAR

The `node_type` and `visibility` columns in the `articles` table SHALL use VARCHAR(50) instead of MySQL ENUM. This provides database portability and allows adding new enum values without schema changes.

#### Scenario: Migration alters column types

- **WHEN** the migration is executed
- **THEN** `node_type` and `visibility` columns are VARCHAR(50) with existing data preserved

#### Scenario: Migration rollback

- **WHEN** the migration is rolled back
- **THEN** `node_type` and `visibility` columns are converted back to ENUM with original values
