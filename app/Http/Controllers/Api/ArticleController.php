<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteArticleRequest;
use App\Http\Requests\ListArticleRequest;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Services\ArticleService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleService $articleService
    ) {}

    /**
     * GET /api/articles
     *
     * Returns a paginated, filtered list of article translations.
     * Public callers (no valid API key) only see articles with visibility=public.
     */
    public function index(ListArticleRequest $request): ArticleCollection
    {
        $isPrivate = (bool) $request->attributes->get('is_private_access', false);

        $filters = array_merge(
            $request->validated(),
            ['is_private' => $isPrivate]
        );

        $paginator = $this->articleService->list($filters);

        return new ArticleCollection($paginator);
    }

    /**
     * GET /api/articles/by-path/{path}
     *
     * Retrieves a single article translation by its path.
     * Visibility is enforced at service level based on the API key presence.
     */
    public function showByPath(Request $request, string $path): ArticleResource|JsonResponse
    {
        $isPrivate = (bool) $request->attributes->get('is_private_access', false);

        try {
            $translation = $this->articleService->getByPath($path, $isPrivate);
        } catch (ModelNotFoundException) {
            return response()->json([
                'data'    => null,
                'message' => 'Article not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return new ArticleResource($translation);
    }

    /**
     * GET /api/articles/{id}
     *
     * Retrieves a single article with all its translations by article_id.
     */
    public function show(Request $request, int $id): ArticleResource|JsonResponse
    {
        $isPrivate = (bool) $request->attributes->get('is_private_access', false);

        try {
            $article = $this->articleService->getById($id, $isPrivate);
        } catch (ModelNotFoundException) {
            return response()->json([
                'data'    => null,
                'message' => 'Article not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return new ArticleResource($article);
    }

    /**
     * POST /api/articles
     *
     * Creates a new article with its initial translation. Returns HTTP 201.
     */
    public function store(StoreArticleRequest $request): JsonResponse
    {
        $article = $this->articleService->create($request->validated());

        return (new ArticleResource($article))
            ->setMessage('Article created successfully.')
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    /**
     * PUT /api/articles/{id}
     *
     * Updates an existing article and/or its translation. Returns HTTP 200.
     */
    public function update(UpdateArticleRequest $request, int $id): ArticleResource|JsonResponse
    {
        try {
            $article = $this->articleService->update($id, $request->validated());
        } catch (ModelNotFoundException) {
            return response()->json([
                'data'    => null,
                'message' => 'Article not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $resource = new ArticleResource($article);
        $resource->setMessage('Article updated successfully.');

        return $resource;
    }

    /**
     * DELETE /api/articles/{id}
     *
     * Deletes an article. Returns HTTP 200 on success.
     */
    public function destroy(DeleteArticleRequest $request, int $id): JsonResponse
    {
        $this->articleService->delete($id);

        return response()->json([
            'data'    => null,
            'message' => 'Article deleted successfully.',
        ], JsonResponse::HTTP_OK);
    }
}
