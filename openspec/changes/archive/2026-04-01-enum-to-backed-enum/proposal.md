## Why

The `articles` table currently uses MySQL ENUM columns for `node_type` and `visibility` fields. This is widely considered an anti-pattern in the Laravel community because it tightly couples database schema to business logic, requires migrations to add/remove values, is database-specific (not portable), and becomes difficult to maintain as the application grows.

PHP 8.1+ Backed Enums provide type safety at the application layer with IDE autocomplete, refactoring support, and the ability to add helper methods—all while using database-agnostic VARCHAR columns that don't require schema changes when adding new values.

## What Changes

- Create PHP 8.1+ Backed Enum classes: `App\Enums\NodeType` and `App\Enums\Visibility`
- Create migration to convert `node_type` and `visibility` columns from ENUM to VARCHAR(50)
- Update `Article` model to cast attributes using the new enum classes
- Add helper methods to enums (e.g., `label()`, `isPublic()`, `isArticle()`)
- Update any queries or validation rules that reference these fields

## Capabilities

### New Capabilities

- `php-enums`: PHP 8.1+ Backed Enum classes for article type and visibility status, providing type-safe enumeration with helper methods for labels and state checks.

### Modified Capabilities

- `article-management`: Article creation and update operations will accept and return enum instances instead of raw strings. API contracts remain unchanged (string values), but internal handling uses enum classes.

## Impact

- **Database**: Migration will alter `articles` table columns from ENUM to VARCHAR(50)
- **Models**: `Article` model `$casts` property will reference enum classes
- **Requests**: `StoreArticleRequest` and `UpdateArticleRequest` validation rules may need enum value validation
- **Services**: `ArticleService` may need updates to work with enum instances
- **API**: No breaking changes to API responses (enums serialize to string values)
- **Tests**: Test fixtures and assertions may need updates for enum handling
