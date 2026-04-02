## Context

The Laravel multilingual articles API currently uses raw JSON responses, lacks proper authorization policies, and has inconsistent type safety between enum-backed fields (`node_type`, `visibility`) and plain string fields (`status`). The `ArticleController` returns data directly via `response()->json()` without resource transformation. Form Request validation rules hardcode enum values as strings, creating maintenance burden when enum values change. The database lacks indexes on frequently queried columns, impacting performance as data grows.

### Current Architecture

- **Controller**: `ArticleController` returns raw JSON with manual response structure
- **Models**: `Article` uses `NodeType` and `Visibility` enums; `ArticleTranslation` uses string for `status`
- **Validation**: `StoreArticleRequest` and `UpdateArticleRequest` hardcode enum values
- **Authorization**: All Form Request `authorize()` methods return `true`
- **Database**: `article_translations` has foreign keys and unique constraint but no performance indexes

## Goals / Non-Goals

**Goals:**

- Implement `DELETE /api/articles/{id}` with authorization based on article ownership
- Create `TranslationStatus` enum matching `NodeType`/`Visibility` pattern
- Add database indexes for query performance optimization
- Replace hardcoded validation values with enum-derived values
- Implement API Resources for consistent response transformation
- Create `ArticlePolicy` with ownership-based authorization

**Non-Goals:**

- Changing API response structure (resources maintain compatibility)
- Adding bulk delete operations
- Implementing soft-delete restoration endpoints
- Adding role-based permissions beyond ownership checks

## Decisions

### 1. TranslationStatus Enum Pattern

**Decision**: Create `App\Enums\TranslationStatus` as a string-backed enum with cases `DRAFT`, `PUBLISHED`, `UNPUBLISHED`.

**Rationale**: Matches existing `NodeType` and `Visibility` enum pattern for consistency. Provides type safety, IDE support, and prevents invalid status values.

**Alternatives considered**:

- Using a PHP 8.1 enum library - rejected to avoid unnecessary dependency
- Keeping as string - rejected due to type safety concerns

### 2. API Resources vs Transformers

**Decision**: Use Laravel's native API Resources (`JsonResource` and `ResourceCollection`).

**Rationale**: Built into Laravel, minimal overhead, integrates with pagination, maintains response structure consistency.

**Alternatives considered**:

- Fractal transformers - rejected as external dependency, overkill for this use case
- Custom response classes - rejected, resources are simpler

### 3. Authorization Strategy

**Decision**: Create `ArticlePolicy` with ownership checks. Authorize actions against the first translation's `created_by` field.

**Rationale**: Simple ownership model fits current needs. Policies integrate with Laravel's authorization middleware.

**Implementation**:

```php
// ArticlePolicy
public function delete(User $user, Article $article): bool
{
    $translation = $article->translations->first();
    return $translation && $user->id === $translation->created_by;
}
```

### 4. Database Index Strategy

**Decision**: Add three indexes in a new migration:

- Composite index `['language_code', 'status']` - for filtered article lists
- Single index on `path` - for path-based lookups
- Single index on `updated_at` - for sorting by recency

**Rationale**: These columns appear in WHERE clauses, JOIN conditions, and ORDER BY clauses across the application's query patterns.

### 5. Enum-Derived Validation

**Decision**: Add static `values()` method to each enum class returning array of string values.

**Implementation**:

```php
// In each enum class
public static function values(): array
{
    return array_column(self::cases(), 'value');
}

// In Form Request
'node_type' => ['required', 'string', 'in:' . implode(',', NodeType::values())],
```

**Rationale**: Single source of truth for enum values. Adding new enum cases automatically updates validation rules.

## Risks / Trade-offs

| Risk | Mitigation |
|------|------------|
| Breaking existing API consumers with resource responses | Resource wraps data consistently; structure unchanged |
| Authorization fails for articles without translations | Policy checks for translation existence before ownership check |
| Index migration slow on large tables | Run during low-traffic period; indexes improve read performance |
| Enum change requires migration for database ENUM column | Migration already uses VARCHAR for status; only model cast changes |

## Migration Plan

1. **Deploy enum and model changes**: Add `TranslationStatus` enum, update `ArticleTranslation` cast
2. **Deploy database indexes**: Run migration during maintenance window if tables are large
3. **Deploy API Resources**: Controller changes to use resources (no breaking changes)
4. **Deploy Form Request changes**: Enum-derived validation values
5. **Deploy authorization**: Create policy, register in `AuthServiceProvider`, update Form Requests

**Rollback**: Each change is reversible via migration rollback and code revert. No data migration required.
