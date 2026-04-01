## Context

The application uses MySQL ENUM columns for `node_type` and `visibility` in the `articles` table. This creates tight coupling between database schema and application logic. Laravel 10 supports PHP 8.1+ Backed Enums with automatic Eloquent casting, providing a cleaner approach.

Current state:

- `node_type` ENUM('article', 'user_agreement')
- `visibility` ENUM('public', 'private')
- Model casts these as 'string' (raw string values)

## Goals / Non-Goals

**Goals:**

- Create type-safe PHP 8.1+ Backed Enums with helper methods
- Migrate database columns from ENUM to VARCHAR(50) for portability
- Enable IDE autocomplete and refactoring support
- Maintain backward-compatible API responses (enums serialize to string values)
- Add convenience methods like `label()`, `isPublic()`, `isArticle()`

**Non-Goals:**

- Adding new enum values (future task)
- Changing API contracts or response formats
- Modifying frontend consumers

## Decisions

### D1: Use String-Backed Enums (not Unit Enums)

**Rationale:** String-backed enums (`enum NodeType: string`) serialize directly to their string values, ensuring API responses remain unchanged. Unit enums would require custom serialization.

**Alternatives considered:**

- Unit enums with custom casting - More code, no benefit
- Integer-backed enums - Would break API, requires mapping layer

### D2: VARCHAR(50) Column Type

**Rationale:** Provides ample room for future values while being efficient. 50 characters is standard for enum-like strings.

**Alternatives considered:**

- VARCHAR(255) - Wasteful for short values
- VARCHAR(20) - Risk of being too short for future values

### D3: Enum Helper Methods

Each enum will implement:

- `label()`: Human-readable display name
- `isX()` methods for common checks (e.g., `isPublic()`, `isArticle()`)

**Rationale:** Encapsulates business logic within the enum, avoiding scattered string comparisons.

### D4: Three-Phase Migration Strategy

1. **Phase 1**: Create enum classes (no breaking changes, code compiles)
2. **Phase 2**: Run migration to convert columns
3. **Phase 3**: Update model casts to use enum classes

**Rationale:** Allows for staged deployment with rollback points at each phase.

## Risks / Trade-offs

| Risk | Mitigation |
|------|------------|
| Existing data contains invalid values | Migration includes validation step; log and fix anomalies before conversion |
| Queries using raw strings need casting | Enum instances serialize to strings; most queries work unchanged |
| Tests may expect string values | Update tests to use enum instances or string values (both work) |
| Validation rules use `in:` syntax | Enum `tryFrom()` handles validation; update form requests |

## Migration Plan

### Phase 1: Create Enum Classes

1. Create `app/Enums/` directory
2. Create `NodeType.php` enum with cases: `ARTICLE`, `USER_AGREEMENT`
3. Create `Visibility.php` enum with cases: `PUBLIC`, `PRIVATE`
4. Add `label()` method to each enum
5. Add convenience methods (`isArticle()`, `isPublic()`, etc.)

### Phase 2: Database Migration

1. Create migration to ALTER columns: ENUM → VARCHAR(50)
2. Migration preserves existing data (values remain unchanged)
3. Run migration on staging first, verify data integrity

### Phase 3: Update Model

1. Update `Article::$casts` to use enum class names
2. Update `StoreArticleRequest` and `UpdateArticleRequest` validation
3. Update `ArticleService` if needed for enum handling
4. Update tests to use enum instances

### Rollback Strategy

1. Migration `down()` method converts VARCHAR back to ENUM
2. Revert model casts to 'string'
3. Delete enum classes if complete rollback needed

## Open Questions

- None. The approach is well-established in the Laravel community.
