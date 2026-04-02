## ADDED Requirements

### Requirement: Delete article endpoint

The system SHALL provide a `DELETE /api/articles/{id}` endpoint that soft-deletes an article and all its translations. The endpoint SHALL require a valid API key. The endpoint SHALL authorize the request using `ArticlePolicy@delete` to verify the requesting user owns the article.

#### Scenario: Successful article deletion

- **WHEN** a DELETE request is sent to `/api/articles/{id}` with a valid API key and the user owns the article
- **THEN** the system returns HTTP 200 with a success message and the article is soft-deleted

#### Scenario: Unauthorized deletion attempt

- **WHEN** a DELETE request is sent to `/api/articles/{id}` with a valid API key but the user does not own the article
- **THEN** the system returns HTTP 403 with an authorization error

#### Scenario: Delete non-existent article

- **WHEN** a DELETE request is sent to `/api/articles/{id}` where the article does not exist
- **THEN** the system returns HTTP 404 with a not found message

#### Scenario: Delete without API key

- **WHEN** a DELETE request is sent to `/api/articles/{id}` without a valid API key
- **THEN** the system returns HTTP 401 with an authentication error

---

### Requirement: Soft delete cascade behavior

The system SHALL soft-delete the article record by setting `deleted_at` on the `articles` table. Related translations SHALL remain in the database with their own `deleted_at` timestamps via the model's `SoftDeletes` trait.

#### Scenario: Article and translations soft-deleted

- **WHEN** an article is deleted via the DELETE endpoint
- **THEN** both the article record and all related translation records have `deleted_at` set
