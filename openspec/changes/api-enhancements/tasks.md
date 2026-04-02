## 1. Enums

- [ ] 1.1 Create `TranslationStatus` enum at `app/Enums/TranslationStatus.php` with cases `DRAFT`, `PUBLISHED`, `UNPUBLISHED` and methods `label()`, `isPublished()`, `values()`
- [ ] 1.2 Add `values()` static method to `NodeType` enum returning `['article', 'user_agreement']`
- [ ] 1.3 Add `values()` static method to `Visibility` enum returning `['public', 'private']`
- [ ] 1.4 Update `ArticleTranslation` model to cast `status` to `TranslationStatus::class`

## 2. Database Indexes

- [ ] 2.1 Create migration `add_indexes_to_article_translations_table` with indexes on `['language_code', 'status']`, `path`, and `updated_at`
- [ ] 2.2 Run migration and verify indexes created successfully

## 3. API Resources

- [ ] 3.1 Create `ArticleResource` at `app/Http/Resources/ArticleResource.php` for single article/translation transformation
- [ ] 3.2 Create `ArticleCollection` at `app/Http/Resources/ArticleCollection.php` for paginated lists with metadata

## 4. Authorization Policy

- [ ] 4.1 Create `ArticlePolicy` at `app/Policies/ArticlePolicy.php` with `create`, `update`, and `delete` methods
- [ ] 4.2 Register `ArticlePolicy` in `AuthServiceProvider` for `Article` model
- [ ] 4.3 Update `StoreArticleRequest` `authorize()` method to return `true` (authenticated via API key middleware)
- [ ] 4.4 Update `UpdateArticleRequest` `authorize()` method to use `ArticlePolicy@update`
- [ ] 4.5 Create `DeleteArticleRequest` at `app/Http/Requests/DeleteArticleRequest.php` with authorization via `ArticlePolicy@delete`

## 5. Form Request Validation Updates

- [ ] 5.1 Update `StoreArticleRequest` to use `NodeType::values()` for `node_type` validation
- [ ] 5.2 Update `StoreArticleRequest` to use `Visibility::values()` for `visibility` validation
- [ ] 5.3 Update `StoreArticleRequest` to use `TranslationStatus::values()` for `status` validation
- [ ] 5.4 Update `UpdateArticleRequest` to use `NodeType::values()` for `node_type` validation
- [ ] 5.5 Update `UpdateArticleRequest` to use `Visibility::values()` for `visibility` validation
- [ ] 5.6 Update `UpdateArticleRequest` to use `TranslationStatus::values()` for `status` validation

## 6. Controller Updates

- [ ] 6.1 Update `ArticleController@index` to return `ArticleCollection` instead of raw JSON
- [ ] 6.2 Update `ArticleController@show` to return `ArticleResource`
- [ ] 6.3 Update `ArticleController@showByPath` to return `ArticleResource`
- [ ] 6.4 Update `ArticleController@store` to return `ArticleResource` with HTTP 201
- [ ] 6.5 Update `ArticleController@update` to return `ArticleResource`
- [ ] 6.6 Add `destroy()` method to `ArticleController` for DELETE endpoint with authorization

## 7. Routes

- [ ] 7.1 Add `DELETE /api/articles/{id}` route in `routes/api.php` protected by `api.key` middleware

## 8. Tests

- [ ] 8.1 Create test for `TranslationStatus` enum values and methods
- [ ] 8.2 Create test for DELETE endpoint success (owner)
- [ ] 8.3 Create test for DELETE endpoint authorization failure (non-owner)
- [ ] 8.4 Create test for DELETE endpoint not found
- [ ] 8.5 Create test for DELETE endpoint unauthenticated
- [ ] 8.6 Verify existing article tests pass with API Resources

## 9. Verification

- [ ] 9.1 Run full test suite and verify all tests pass
- [ ] 9.2 Verify API responses maintain consistent structure with `ArticleResource`
- [ ] 9.3 Verify database query performance with new indexes (optional EXPLAIN analysis)
