# API Authentication

## ADDED Requirements

### Requirement: API key middleware gates private access

The system SHALL implement a `CheckApiKey` middleware that reads the `X-API-KEY` request header and compares it to the configured `API_KEY` environment variable. Routes requiring private access SHALL be protected by this middleware.

#### Scenario: Valid API key grants private access

- **WHEN** a request is sent with an `X-API-KEY` header matching the configured `API_KEY`
- **THEN** the middleware sets the request as authenticated and passes it through

#### Scenario: Missing API key blocks private access

- **WHEN** a request is sent without an `X-API-KEY` header to a private-protected route
- **THEN** the middleware returns HTTP 401 with a JSON error message

#### Scenario: Invalid API key blocks private access

- **WHEN** a request is sent with an `X-API-KEY` header that does not match the configured `API_KEY`
- **THEN** the middleware returns HTTP 401 with a JSON error message

---

### Requirement: Public routes do not require API key

The system SHALL allow unauthenticated access to public article retrieval endpoints. The `CheckApiKey` middleware SHALL NOT be applied to public endpoints, but the article visibility check SHALL still restrict private article data.

#### Scenario: Public article endpoint accessible without API key

- **WHEN** a GET request is sent to a public article endpoint without any API key
- **THEN** the system processes the request and returns public articles only

---

### Requirement: API key configuration

The API key value SHALL be stored in the `.env` file as `API_KEY`. The application SHALL read it via `config('app.api_key')` or `env('API_KEY')`. An `.env.example` file SHALL include a placeholder entry for `API_KEY`.

#### Scenario: API key missing from environment

- **WHEN** `API_KEY` is not set in the environment and a private route is accessed
- **THEN** the middleware returns HTTP 401 (treats as unauthenticated)
