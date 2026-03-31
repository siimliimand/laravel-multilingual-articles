<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListArticleRequest;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
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
    public function index(ListArticleRequest $request): JsonResponse
    {
        $isPrivate = (bool) $request->attributes->get('is_private_access', false);

        $filters = array_merge(
            $request->validated(),
            ['is_private' => $isPrivate]
        );

        $paginator = $this->articleService->list($filters);

        return response()->json([
            'data'    => $paginator->items(),
            'message' => 'Articles retrieved successfully.',
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ], JsonResponse::HTTP_OK);
    }

    /**
     * GET /api/articles/by-path/{path}
     *
     * Retrieves a single article translation by its path.
     * Visibility is enforced at service level based on the API key presence.
     */
    public function showByPath(Request $request, string $path): JsonResponse
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

        return response()->json([
            'data'    => $translation,
            'message' => 'Article retrieved successfully.',
        ], JsonResponse::HTTP_OK);
    }

    /**
     * GET /api/articles/{id}
     *
     * Retrieves a single article with all its translations by article_id.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $article = $this->articleService->getById($id);
        } catch (ModelNotFoundException) {
            return response()->json([
                'data'    => null,
                'message' => 'Article not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data'    => $article,
            'message' => 'Article retrieved successfully.',
        ], JsonResponse::HTTP_OK);
    }

    /**
     * POST /api/articles
     *
     * Creates a new article with its initial translation. Returns HTTP 201.
     */
    public function store(StoreArticleRequest $request): JsonResponse
    {
        $article = $this->articleService->create($request->validated());

        return response()->json([
            'data'    => $article,
            'message' => 'Article created successfully.',
        ], JsonResponse::HTTP_CREATED);
    }

    /**
     * PUT /api/articles/{id}
     *
     * Updates an existing article and/or its translation. Returns HTTP 200.
     */
    public function update(UpdateArticleRequest $request, int $id): JsonResponse
    {
        try {
            $article = $this->articleService->update($id, $request->validated());
        } catch (ModelNotFoundException) {
            return response()->json([
                'data'    => null,
                'message' => 'Article not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data'    => $article,
            'message' => 'Article updated successfully.',
        ], JsonResponse::HTTP_OK);
    }
}
