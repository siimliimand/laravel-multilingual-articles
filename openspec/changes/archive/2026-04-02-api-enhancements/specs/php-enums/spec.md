## ADDED Requirements

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

## MODIFIED Requirements

### Requirement: Enum serialization

The enums SHALL serialize to their string values when converted to strings or JSON. This ensures API responses remain unchanged when enum instances are returned in model data.

#### Scenario: Enum serialization to string

- **WHEN** an enum instance is cast to string or returned in JSON
- **THEN** the enum's string value is used (e.g., `NodeType::ARTICLE` becomes `'article'`, `TranslationStatus::PUBLISHED` becomes `'published'`)

#### Scenario: Enum tryFrom validation

- **WHEN** `TranslationStatus::tryFrom('invalid_value')` is called
- **THEN** the method returns `null`
