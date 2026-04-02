## ADDED Requirements

### Requirement: ArticlePolicy class

The system SHALL provide an `App\Policies\ArticlePolicy` class with authorization methods for `create`, `update`, and `delete` operations. Authorization SHALL be based on article ownership, determined by comparing the authenticated user's ID with the `created_by` field on the article's first translation.

#### Scenario: Policy allows delete for article owner

- **WHEN** `ArticlePolicy@delete` is called with a user whose ID matches the article's first translation's `created_by`
- **THEN** the method returns `true`

#### Scenario: Policy denies delete for non-owner

- **WHEN** `ArticlePolicy@delete` is called with a user whose ID does not match the article's first translation's `created_by`
- **THEN** the method returns `false`

#### Scenario: Policy handles article without translations

- **WHEN** `ArticlePolicy@delete` is called for an article that has no translations
- **THEN** the method returns `false`

---

### Requirement: Form Request authorization integration

The system SHALL integrate `ArticlePolicy` with Form Request classes. The `StoreArticleRequest`, `UpdateArticleRequest`, and new `DeleteArticleRequest` SHALL use the policy for authorization instead of returning `true` unconditionally.

#### Scenario: StoreArticleRequest authorization

- **WHEN** a store request is made
- **THEN** the `authorize()` method SHALL return `true` for authenticated users with valid API keys

#### Scenario: UpdateArticleRequest authorization

- **WHEN** an update request is made for an article
- **THEN** the `authorize()` method SHALL check `ArticlePolicy@update` for the authenticated user and target article

#### Scenario: DeleteArticleRequest authorization

- **WHEN** a delete request is made for an article
- **THEN** the `authorize()` method SHALL check `ArticlePolicy@delete` for the authenticated user and target article

---

### Requirement: Policy registration

The system SHALL register `ArticlePolicy` in the `AuthServiceProvider` with the `Article` model.

#### Scenario: Policy resolved for Article model

- **WHEN** `Gate::allows('delete', $article)` is called
- **THEN** the `ArticlePolicy@delete` method is invoked automatically
