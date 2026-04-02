## ADDED Requirements

### Requirement: Database indexes for performance

The system SHALL include database indexes on frequently queried columns in the `article_translations` table. A composite index SHALL be added on `['language_code', 'status']` for filtered article list queries. Single-column indexes SHALL be added on `path` for path-based lookups and `updated_at` for sorting by recency.

#### Scenario: Migration adds indexes

- **WHEN** the migration is executed
- **THEN** indexes are created on `article_translations` for columns `['language_code', 'status']`, `path`, and `updated_at`

#### Scenario: Migration rollback removes indexes

- **WHEN** the migration is rolled back
- **THEN** the indexes on `['language_code', 'status']`, `path`, and `updated_at` are removed

---

### Requirement: Query performance improvement

The system SHALL utilize the new indexes for queries filtering by language and status, searching by path, or ordering by updated date.

#### Scenario: List query uses composite index

- **WHEN** a query filters `article_translations` by `language_code` and `status`
- **THEN** the database query planner utilizes the composite index for efficient retrieval

#### Scenario: Path lookup uses index

- **WHEN** a query looks up an article translation by `path`
- **THEN** the database query planner utilizes the `path` index for efficient retrieval

#### Scenario: Recent articles query uses index

- **WHEN** a query orders `article_translations` by `updated_at DESC`
- **THEN** the database query planner utilizes the `updated_at` index for efficient sorting
