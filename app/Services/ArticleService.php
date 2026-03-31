<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleTranslation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ArticleService
{
    /**
     * Return a filtered, sorted, paginated list of article translations.
     *
     * Accepted filter keys:
     *   - title           (string)  – partial match on article_translations.title (LIKE)
     *   - node_type       (string)  – exact match on articles.node_type
     *   - status          (string)  – exact match on article_translations.status
     *   - language_code   (string)  – exact match on article_translations.language_code
     *   - updated_at_from (string)  – lower bound for article_translations.updated_at (>=)
     *   - updated_at_to   (string)  – upper bound for article_translations.updated_at (<=)
     *   - is_private      (bool)    – when false only public articles are included
     *   - per_page        (int)     – page size (default 15)
     *
     * Default sort: article_translations.updated_at DESC.
     */
    public function list(array $filters): LengthAwarePaginator
    {
        $query = ArticleTranslation::query()
            ->join('articles', 'article_translations.article_id', '=', 'articles.article_id')
            ->select('article_translations.*')
            ->whereNull('articles.deleted_at')
            ->whereNull('article_translations.deleted_at');

        // Visibility: public callers only see public articles
        $isPrivate = $filters['is_private'] ?? false;
        if (!$isPrivate) {
            $query->where('articles.visibility', 'public');
        }

        // Title – partial match
        if (!empty($filters['title'])) {
            $query->where('article_translations.title', 'LIKE', '%' . $filters['title'] . '%');
        }

        // node_type – exact match on articles table
        if (!empty($filters['node_type'])) {
            $query->where('articles.node_type', $filters['node_type']);
        }

        // status – exact match on article_translations table
        if (!empty($filters['status'])) {
            $query->where('article_translations.status', $filters['status']);
        }

        // language_code – exact match
        if (!empty($filters['language_code'])) {
            $query->where('article_translations.language_code', $filters['language_code']);
        }

        // updated_at_from – lower bound on article_translations.updated_at
        if (!empty($filters['updated_at_from'])) {
            $query->where('article_translations.updated_at', '>=', $filters['updated_at_from']);
        }

        // updated_at_to – upper bound on article_translations.updated_at
        if (!empty($filters['updated_at_to'])) {
            $query->where('article_translations.updated_at', '<=', $filters['updated_at_to']);
        }

        // Default sort: article_translations.updated_at DESC (fully qualified to avoid ambiguity)
        $query->orderBy('article_translations.updated_at', 'desc');

        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 15;

        return $query->paginate($perPage);
    }

    /**
     * Retrieve a single article translation by its path.
     *
     * Public callers ($isPrivate = false) can only access articles with
     * visibility = 'public'. Throws ModelNotFoundException when not found.
     */
    public function getByPath(string $path, bool $isPrivate): ArticleTranslation
    {
        $query = ArticleTranslation::query()
            ->join('articles', 'article_translations.article_id', '=', 'articles.article_id')
            ->select('article_translations.*')
            ->whereNull('articles.deleted_at')
            ->where('article_translations.path', $path);

        if (!$isPrivate) {
            $query->where('articles.visibility', 'public');
        }

        $translation = $query->first();

        if (!$translation) {
            throw new ModelNotFoundException("ArticleTranslation with path '{$path}' not found.");
        }

        return $translation;
    }

    /**
     * Retrieve an article with all its translations by article_id.
     *
     * Throws ModelNotFoundException when the article does not exist.
     */
    public function getById(int $id): Article
    {
        return Article::with('translations')
            ->findOrFail($id);
    }

    /**
     * Create a new article and its initial translation.
     *
     * Expected $data keys:
     *   - node_type     (string, required)
     *   - visibility    (string, required)
     *   - language_code (string, required)
     *   - title         (string, required)
     *   - path          (string, required)
     *   - content       (string, required)
     *   - status        (string, required)
     *   - summary       (string, optional)
     *   - keywords      (string, optional)
     *   - created_by    (int, optional)
     *   - modified_by   (int, optional)
     */
    public function create(array $data): Article
    {
        $article = Article::create([
            'node_type'  => $data['node_type'],
            'visibility' => $data['visibility'],
        ]);

        $article->translations()->create([
            'language_code' => $data['language_code'],
            'title'         => $data['title'],
            'path'          => $data['path'],
            'content'       => $data['content'],
            'status'        => $data['status'],
            'summary'       => $data['summary']    ?? null,
            'keywords'      => $data['keywords']   ?? null,
            'created_by'    => $data['created_by'] ?? null,
            'modified_by'   => $data['modified_by'] ?? null,
        ]);

        return $article->load('translations');
    }

    /**
     * Update an existing article and/or its translation.
     *
     * Article-level fields: node_type, visibility
     * Translation-level fields: language_code, title, path, content, status,
     *                            summary, keywords, modified_by
     *
     * Throws ModelNotFoundException when the article does not exist.
     */
    public function update(int $id, array $data): Article
    {
        $article = Article::findOrFail($id);

        // Update article-level fields if present
        $articleFields = array_filter([
            'node_type'  => $data['node_type']  ?? null,
            'visibility' => $data['visibility'] ?? null,
        ], fn ($v) => $v !== null);

        if (!empty($articleFields)) {
            $article->update($articleFields);
        }

        // Update translation if translation-level fields are present
        $translationFields = array_filter([
            'language_code' => $data['language_code'] ?? null,
            'title'         => $data['title']         ?? null,
            'path'          => $data['path']           ?? null,
            'content'       => $data['content']        ?? null,
            'status'        => $data['status']         ?? null,
            'summary'       => $data['summary']        ?? null,
            'keywords'      => $data['keywords']       ?? null,
            'modified_by'   => $data['modified_by']    ?? null,
        ], fn ($v) => $v !== null);

        if (!empty($translationFields)) {
            // Identify which translation to update: by language_code when supplied,
            // otherwise fall back to the first (or only) translation.
            $languageCode = $data['language_code'] ?? null;

            $translationQuery = $article->translations();
            if ($languageCode) {
                $translationQuery->where('language_code', $languageCode);
            }

            $translation = $translationQuery->first();

            if ($translation) {
                $translation->update($translationFields);
            } else {
                // Create a new translation for the given language
                $article->translations()->create($translationFields);
            }
        }

        return $article->load('translations');
    }
}
