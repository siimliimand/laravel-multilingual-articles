# Article Retrieval

## ADDED Requirements

### Requirement: Retrieve public article by path

The system SHALL allow retrieving a single article translation by its `path` field without an API key. Only articles with `visibility = public` and the translation with `status = published` SHALL be returned to unauthenticated (public) requests.

#### Scenario: Public article retrieved by path

- **WHEN** a GET request is sent to `/api/articles/by-path/{path}` without an API key header
- **THEN** the system returns HTTP 200 with the article and translation data for a public, published article

#### Scenario: Private article not accessible on public endpoint

- **WHEN** a GET request is sent to `/api/articles/by-path/{path}` without an API key, where the article has `visibility = private`
- **THEN** the system returns HTTP 403 or HTTP 404

#### Scenario: Article not found by path

- **WHEN** a GET request is sent to `/api/articles/by-path/{path}` where no translation exists with that path
- **THEN** the system returns HTTP 404

---

### Requirement: Retrieve private article by path

The system SHALL allow retrieving a single article translation by its `path` field when a valid API key is provided. Both public and private articles SHALL be accessible with a valid key.

#### Scenario: Private article retrieved by path with valid API key

- **WHEN** a GET request is sent to `/api/articles/by-path/{path}` with a valid `X-API-KEY` header, where the article has `visibility = private`
- **THEN** the system returns HTTP 200 with the article and translation data

#### Scenario: Public article retrieved by path with valid API key

- **WHEN** a GET request is sent to `/api/articles/by-path/{path}` with a valid `X-API-KEY` header, where the article has `visibility = public`
- **THEN** the system returns HTTP 200 with the article and translation data

#### Scenario: Invalid API key rejected

- **WHEN** a GET request is sent to `/api/articles/by-path/{path}` with an invalid `X-API-KEY` header
- **THEN** the system returns HTTP 401

---

### Requirement: Retrieve article by ID

The system SHALL allow retrieving an article and all its translations by `article_id`. A valid API key SHALL be required to retrieve private articles by ID.

#### Scenario: Article retrieved by ID with valid API key

- **WHEN** a GET request is sent to `/api/articles/{article_id}` with a valid `X-API-KEY`
- **THEN** the system returns HTTP 200 with the article and all its translations

#### Scenario: Non-existent article returns 404

- **WHEN** a GET request is sent to `/api/articles/{article_id}` with a valid `X-API-KEY` and no article exists with that ID
- **THEN** the system returns HTTP 404
