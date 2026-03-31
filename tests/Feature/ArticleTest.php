<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\SiteLanguage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The valid API key used throughout the tests.
     * Overrides the API_KEY env in setUp() so CheckApiKey / OptionalApiKey middleware
     * validates against this value.
     */
    private string $apiKey = 'test-secret-key';

    /**
     * Seed required site_languages rows and configure API_KEY before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Override the API_KEY environment variable so the middleware uses our test key.
        // Laravel's env() helper reads from $_ENV first (after the initial bootstrap),
        // so this is the correct way to override it in tests.
        $_ENV['API_KEY']    = $this->apiKey;
        $_SERVER['API_KEY'] = $this->apiKey;

        // Seed base languages required by the FK constraint on article_translations
        SiteLanguage::insert([
            ['language_code' => 'en', 'language_name' => 'English',  'created_at' => now(), 'updated_at' => now()],
            ['language_code' => 'et', 'language_name' => 'Estonian', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Create a public article with one translation.
     *
     * @param array $translationOverrides Merges into the translation row.
     * @param array $articleOverrides     Merges into the article row.
     */
    private function makePublicArticle(
        array $translationOverrides = [],
        array $articleOverrides = []
    ): Article {
        $article = Article::create(array_merge([
            'node_type'  => 'article',
            'visibility' => 'public',
        ], $articleOverrides));

        $article->translations()->create(array_merge([
            'language_code' => 'en',
            'title'         => 'Public Article',
            'path'          => 'public-article-' . $article->article_id,
            'content'       => 'Content here.',
            'status'        => 'published',
        ], $translationOverrides));

        return $article->load('translations');
    }

    /**
     * Create a private article with one translation.
     */
    private function makePrivateArticle(array $translationOverrides = []): Article
    {
        $article = Article::create([
            'node_type'  => 'article',
            'visibility' => 'private',
        ]);

        $article->translations()->create(array_merge([
            'language_code' => 'en',
            'title'         => 'Private Article',
            'path'          => 'private-article-' . $article->article_id,
            'content'       => 'Private content.',
            'status'        => 'published',
        ], $translationOverrides));

        return $article->load('translations');
    }

    // =========================================================================
    // 9.2 – Article list: sorted by updated_at DESC by default
    // =========================================================================

    /** @test */
    public function article_list_is_sorted_by_updated_at_desc(): void
    {
        $older = $this->makePublicArticle(['title' => 'Older', 'path' => 'older-article']);
        $newer = $this->makePublicArticle(['title' => 'Newer', 'path' => 'newer-article']);

        // Use raw DB updates to set updated_at, bypassing Eloquent auto-timestamps
        DB::table('article_translations')
            ->where('translation_id', $older->translations->first()->translation_id)
            ->update(['updated_at' => now()->subDays(2)->toDateTimeString()]);

        DB::table('article_translations')
            ->where('translation_id', $newer->translations->first()->translation_id)
            ->update(['updated_at' => now()->toDateTimeString()]);

        $response = $this->getJson('/api/articles');

        $response->assertOk()
            ->assertJsonPath('data.0.title', 'Newer')
            ->assertJsonPath('data.1.title', 'Older');
    }

    // =========================================================================
    // 9.3 – Filter by title
    // =========================================================================

    /** @test */
    public function filter_by_title_returns_only_matching_articles(): void
    {
        $this->makePublicArticle(['title' => 'Laravel Guide', 'path' => 'laravel-guide']);
        $this->makePublicArticle(['title' => 'Vue Tutorial',  'path' => 'vue-tutorial']);

        $response = $this->getJson('/api/articles?title=Laravel');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Laravel Guide');
    }

    // =========================================================================
    // 9.4 – Filter by status
    // =========================================================================

    /** @test */
    public function filter_by_status_returns_only_matching_articles(): void
    {
        $this->makePublicArticle(['title' => 'Draft One',     'path' => 'draft-one',     'status' => 'draft']);
        $this->makePublicArticle(['title' => 'Published One', 'path' => 'published-one', 'status' => 'published']);

        $response = $this->getJson('/api/articles?status=draft');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Draft One');
    }

    // =========================================================================
    // 9.5 – Filter by language_code
    // =========================================================================

    /** @test */
    public function filter_by_language_code_returns_only_matching_articles(): void
    {
        $enArticle = Article::create(['node_type' => 'article', 'visibility' => 'public']);
        $enArticle->translations()->create([
            'language_code' => 'en', 'title' => 'English Article',
            'path' => 'english-article', 'content' => 'EN content.', 'status' => 'published',
        ]);

        $etArticle = Article::create(['node_type' => 'article', 'visibility' => 'public']);
        $etArticle->translations()->create([
            'language_code' => 'et', 'title' => 'Estonian Article',
            'path' => 'estonian-article', 'content' => 'ET content.', 'status' => 'published',
        ]);

        $response = $this->getJson('/api/articles?language_code=et');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Estonian Article');
    }

    // =========================================================================
    // 9.6 – Filter by node_type
    // =========================================================================

    /** @test */
    public function filter_by_node_type_returns_only_matching_articles(): void
    {
        $artArticle = Article::create(['node_type' => 'article', 'visibility' => 'public']);
        $artArticle->translations()->create([
            'language_code' => 'en', 'title' => 'Normal Article',
            'path' => 'normal-article', 'content' => 'c', 'status' => 'published',
        ]);

        $uaArticle = Article::create(['node_type' => 'user_agreement', 'visibility' => 'public']);
        $uaArticle->translations()->create([
            'language_code' => 'en', 'title' => 'User Agreement',
            'path' => 'user-agreement', 'content' => 'ua', 'status' => 'published',
        ]);

        $response = $this->getJson('/api/articles?node_type=user_agreement');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'User Agreement');
    }

    // =========================================================================
    // 9.7 – Filter by updated_at_from / updated_at_to
    // =========================================================================

    /** @test */
    public function filter_by_date_range_returns_articles_in_range(): void
    {
        $old   = $this->makePublicArticle(['title' => 'Old',   'path' => 'old-article']);
        $mid   = $this->makePublicArticle(['title' => 'Mid',   'path' => 'mid-article']);
        $fresh = $this->makePublicArticle(['title' => 'Fresh', 'path' => 'fresh-article']);

        DB::table('article_translations')
            ->where('translation_id', $old->translations->first()->translation_id)
            ->update(['updated_at' => now()->subDays(10)->toDateTimeString()]);

        DB::table('article_translations')
            ->where('translation_id', $mid->translations->first()->translation_id)
            ->update(['updated_at' => now()->subDays(5)->toDateTimeString()]);

        DB::table('article_translations')
            ->where('translation_id', $fresh->translations->first()->translation_id)
            ->update(['updated_at' => now()->toDateTimeString()]);

        // Range: 7 days ago to 3 days ago – 'Mid' (5 days ago) is the only match
        // Validation requires Y-m-d format (date_format:Y-m-d rule in ListArticleRequest)
        $from = now()->subDays(7)->toDateString();
        $to   = now()->subDays(3)->toDateString();

        $response = $this->getJson("/api/articles?updated_at_from={$from}&updated_at_to={$to}");

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Mid');
    }

    // =========================================================================
    // 9.8 – Combined filters
    // =========================================================================

    /** @test */
    public function combined_filters_narrow_results_correctly(): void
    {
        $match = Article::create(['node_type' => 'article', 'visibility' => 'public']);
        $match->translations()->create([
            'language_code' => 'en', 'title' => 'Laravel Tips',
            'path' => 'laravel-tips', 'content' => 'c', 'status' => 'published',
        ]);

        $noMatch = Article::create(['node_type' => 'article', 'visibility' => 'public']);
        $noMatch->translations()->create([
            'language_code' => 'et', 'title' => 'Laravel Tips ET',
            'path' => 'laravel-tips-et', 'content' => 'c', 'status' => 'draft',
        ]);

        $response = $this->getJson('/api/articles?title=Laravel&status=published&language_code=en');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Laravel Tips');
    }

    // =========================================================================
    // 9.9 – Pagination
    // =========================================================================

    /** @test */
    public function pagination_returns_correct_per_page_count_and_metadata(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->makePublicArticle(['title' => "Article {$i}", 'path' => "article-pg-{$i}"]);
        }

        $response = $this->getJson('/api/articles?per_page=2');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.total', 5)
            ->assertJsonPath('meta.current_page', 1);

        $this->assertGreaterThan(1, $response->json('meta.last_page'));
    }

    // =========================================================================
    // 9.10 – Retrieve public article by path without API key
    // =========================================================================

    /** @test */
    public function retrieve_public_article_by_path_without_api_key_succeeds(): void
    {
        $article = $this->makePublicArticle(['title' => 'Public Post', 'path' => 'public-post']);
        $path    = $article->translations->first()->path;

        $response = $this->getJson("/api/articles/by-path/{$path}");

        $response->assertOk()
            ->assertJsonPath('data.path', $path)
            ->assertJsonPath('message', 'Article retrieved successfully.');
    }

    // =========================================================================
    // 9.11 – Retrieve private article by path without API key returns 404
    // =========================================================================

    /** @test */
    public function retrieve_private_article_by_path_without_api_key_returns_404(): void
    {
        $article = $this->makePrivateArticle(['path' => 'private-post']);
        $path    = $article->translations->first()->path;

        $response = $this->getJson("/api/articles/by-path/{$path}");

        $response->assertNotFound()
            ->assertJsonPath('message', 'Article not found.');
    }

    // =========================================================================
    // 9.12 – Retrieve private article by path with valid API key returns 200
    // =========================================================================

    /** @test */
    public function retrieve_private_article_by_path_with_valid_api_key_returns_200(): void
    {
        $article = $this->makePrivateArticle(['path' => 'secure-post']);
        $path    = $article->translations->first()->path;

        // The by-path route uses OptionalApiKey middleware: a valid key sets
        // is_private_access = true, granting access to private articles.
        $response = $this->withHeader('X-API-KEY', $this->apiKey)
            ->getJson("/api/articles/by-path/{$path}");

        $response->assertOk()
            ->assertJsonPath('data.path', $path);
    }

    // =========================================================================
    // 9.13 – Invalid API key on a protected route returns 401
    // =========================================================================

    /** @test */
    public function invalid_api_key_returns_401(): void
    {
        // POST /api/articles requires api.key middleware; wrong key → 401
        $response = $this->withHeader('X-API-KEY', 'wrong-key')
            ->postJson('/api/articles', [
                'node_type'     => 'article',
                'visibility'    => 'public',
                'language_code' => 'en',
                'title'         => 'Test',
                'path'          => 'test-path',
                'content'       => 'Content.',
                'status'        => 'draft',
            ]);

        $response->assertUnauthorized();
    }

    // =========================================================================
    // 9.14 – Create article with valid data returns 201
    // =========================================================================

    /** @test */
    public function create_article_with_valid_data_returns_201_and_persists(): void
    {
        $payload = [
            'node_type'     => 'article',
            'visibility'    => 'public',
            'language_code' => 'en',
            'title'         => 'Brand New Article',
            'path'          => 'brand-new-article',
            'content'       => 'Some content here.',
            'status'        => 'published',
        ];

        $response = $this->withHeader('X-API-KEY', $this->apiKey)
            ->postJson('/api/articles', $payload);

        $response->assertCreated()
            ->assertJsonPath('message', 'Article created successfully.')
            ->assertJsonPath('data.node_type', 'article');

        $this->assertDatabaseHas('article_translations', [
            'title' => 'Brand New Article',
            'path'  => 'brand-new-article',
        ]);
    }

    // =========================================================================
    // 9.15 – Create article with invalid data returns 422
    // =========================================================================

    /** @test */
    public function create_article_with_invalid_data_returns_422(): void
    {
        // Missing required fields: title, path, content, status
        $payload = [
            'node_type'     => 'article',
            'visibility'    => 'public',
            'language_code' => 'en',
        ];

        $response = $this->withHeader('X-API-KEY', $this->apiKey)
            ->postJson('/api/articles', $payload);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'path', 'content', 'status']);
    }

    // =========================================================================
    // 9.16 – Update article with valid data returns 200
    // =========================================================================

    /** @test */
    public function update_article_with_valid_data_returns_200_and_updates_db(): void
    {
        $article = $this->makePublicArticle(['title' => 'Original Title', 'path' => 'original-path']);
        $id      = $article->article_id;

        $response = $this->withHeader('X-API-KEY', $this->apiKey)
            ->putJson("/api/articles/{$id}", [
                'language_code' => 'en',
                'title'         => 'Updated Title',
            ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Article updated successfully.');

        $this->assertDatabaseHas('article_translations', [
            'article_id' => $id,
            'title'      => 'Updated Title',
        ]);
    }
}
