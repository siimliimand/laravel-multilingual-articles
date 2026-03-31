# Multilingual Support

## ADDED Requirements

### Requirement: site_languages table defines supported languages

The system SHALL maintain a `site_languages` table with `language_id`, `language_code` (2-char), `language_name`, `created_at`, and `updated_at` columns. Each language code in `article_translations` MUST reference a valid `language_code` in `site_languages`.

#### Scenario: Language seeded successfully

- **WHEN** `php artisan db:seed` is executed
- **THEN** at least English (`en`) and Estonian (`et`) language records exist in `site_languages`

#### Scenario: Article translation references valid language

- **WHEN** an article translation is created with `language_code = 'en'`
- **THEN** the translation is saved successfully because `'en'` exists in `site_languages`

---

### Requirement: article_translations stores per-language content

The system SHALL store one row in `article_translations` per article per language. Each row contains `title` (max 70 chars), `path` (max 70 chars, unique per language), `summary` (max 180 chars), `keywords` (max 255 chars), `content` (longtext), `created_by`, `modified_by`, `status` (draft/published/unpublished), `unpublished_at`, `created_at`, `updated_at`, `deleted_at`.

#### Scenario: Multiple translations per article

- **WHEN** an article has translations in both `en` and `et`
- **THEN** two rows exist in `article_translations` linked by the same `article_id`

#### Scenario: path is unique per language_code

- **WHEN** two translations with the same `path` but different `language_code` are created
- **THEN** both are saved successfully (path uniqueness is scoped per language)

#### Scenario: path collision within same language rejected

- **WHEN** a translation is created with a `path` already used by another translation with the same `language_code`
- **THEN** the system returns HTTP 422 indicating the path is already taken

---

### Requirement: Soft delete translations

The system SHALL support soft-deleting `article_translations` rows via `deleted_at`. Soft-deleted translations SHALL NOT be returned in API responses.

#### Scenario: Soft-deleted translation not returned

- **WHEN** an article translation is soft-deleted
- **THEN** it does not appear in list or single-retrieval API responses
