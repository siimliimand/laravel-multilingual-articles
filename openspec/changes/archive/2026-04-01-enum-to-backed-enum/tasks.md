## 1. Create Enum Classes

- [x] 1.1 Create `app/Enums/` directory
- [x] 1.2 Create `app/Enums/NodeType.php` with `ARTICLE` and `USER_AGREEMENT` cases
- [x] 1.3 Add `label()` method to `NodeType` enum returning human-readable names
- [x] 1.4 Add `isArticle()` method to `NodeType` enum
- [x] 1.5 Create `app/Enums/Visibility.php` with `PUBLIC` and `PRIVATE` cases
- [x] 1.6 Add `label()` method to `Visibility` enum returning human-readable names
- [x] 1.7 Add `isPublic()` method to `Visibility` enum

## 2. Database Migration

- [x] 2.1 Create migration file to alter articles table columns
- [x] 2.2 Implement `up()` method to convert `node_type` from ENUM to VARCHAR(50)
- [x] 2.3 Implement `up()` method to convert `visibility` from ENUM to VARCHAR(50)
- [x] 2.4 Implement `down()` method to revert columns back to ENUM
- [x] 2.5 Run migration and verify data integrity

## 3. Update Article Model

- [x] 3.1 Add `use App\Enums\NodeType;` and `use App\Enums\Visibility;` imports
- [x] 3.2 Update `$casts` array to cast `node_type` to `NodeType::class`
- [x] 3.3 Update `$casts` array to cast `visibility` to `Visibility::class`

## 4. Update Form Requests

- [x] 4.1 Update `StoreArticleRequest` validation rules for `node_type` to use `in:article,user_agreement`
- [x] 4.2 Update `StoreArticleRequest` validation rules for `visibility` to use `in:public,private`
- [x] 4.3 Update `UpdateArticleRequest` validation rules for `node_type`
- [x] 4.4 Update `UpdateArticleRequest` validation rules for `visibility`

## 5. Update ArticleService

- [x] 5.1 Review `ArticleService` for any string comparisons on `node_type` or `visibility`
- [x] 5.2 Update queries to use enum values or instances as needed
- [x] 5.3 Verify service handles enum instances correctly in create/update operations

## 6. Update Tests

- [x] 6.1 Update `ArticleTest.php` to use enum constants instead of raw strings
- [x] 6.2 Add test for `NodeType` enum methods (`label()`, `isArticle()`)
- [x] 6.3 Add test for `Visibility` enum methods (`label()`, `isPublic()`)
- [x] 6.4 Add test for model returning enum instances
- [x] 6.5 Add test for enum serialization to JSON
- [x] 6.6 Run full test suite and verify all tests pass

## 7. Verification

- [x] 7.1 Run `php artisan migrate:fresh --seed` to verify migrations work
- [x] 7.2 Test API endpoints return correct string values for enum fields
- [x] 7.3 Verify rollback migration works correctly
- [x] 7.4 Run PHPStan or static analysis to verify type safety
