## Why

The API lacks several critical features for production readiness: no deletion endpoint, inconsistent type safety (status field uses plain string while other fields use enums), missing database indexes affecting query performance, hardcoded enum values in validation rules creating maintenance burden, raw JSON responses instead of proper API Resources, and placeholder authorization that grants all access. These gaps impact security, performance, maintainability, and type safety.

## What Changes

- **Add DELETE endpoint**: New `DELETE /api/articles/{id}` route and controller method for article deletion with proper authorization
- **TranslationStatus enum**: Convert `status` field in `article_translations` from plain string to backed enum, matching the pattern used for `NodeType` and `Visibility`
- **Database indexes**: Add composite index on `['language_code', 'status']`, and single-column indexes on `path` and `updated_at` for frequently queried columns
- **Enum-derived validation**: Replace hardcoded enum values in `StoreArticleRequest` and `UpdateArticleRequest` with values derived from enum classes using `implode(',', EnumClass::values())`
- **API Resources**: Replace raw `response()->json()` calls with Laravel API Resources (`ArticleResource`, `ArticleCollection`) for consistent response formatting
- **Authorization policies**: Create `ArticlePolicy` with proper authorization logic for create, update, and delete operations based on article ownership

## Capabilities

### New Capabilities

- `article-deletion`: DELETE endpoint with authorization - allows authenticated users to delete articles they own
- `article-authorization`: Policy-based authorization for article CRUD operations with ownership checks

### Modified Capabilities

- `article-management`: Requirements change - API responses must use API Resources; validation rules must derive enum values from enum classes
- `php-enums`: Requirements change - add `TranslationStatus` enum for article translation status field
- `article-query`: Requirements change - database indexes on frequently queried columns for performance optimization

## Impact

- **API Breaking Changes**: None - response structure remains compatible (API Resources wrap data consistently)
- **Database**: New migration required for indexes on `article_translations` table
- **Models**: `ArticleTranslation` model will cast `status` to `TranslationStatus` enum
- **Controllers**: `ArticleController` will use API Resources and add `destroy()` method
- **Routes**: New `DELETE /api/articles/{id}` route
- **Policies**: New `ArticlePolicy` class for authorization
- **Form Requests**: `StoreArticleRequest` and `UpdateArticleRequest` will use enum-derived validation values
