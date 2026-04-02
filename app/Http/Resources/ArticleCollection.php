<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractPaginator;

class ArticleCollection extends ResourceCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'data';

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'message' => 'Articles retrieved successfully.',
        ];
    }

    /**
     * Customize the pagination information for the resource.
     *
     * @param Request $request
     * @param array $paginated
     * @param array $default
     * @return array
     */
    public function paginationInformation(Request $request, array $paginated, array $default): array
    {
        // Only return meta, not links (to match current API format)
        return [
            'meta' => [
                'current_page' => $paginated['current_page'],
                'last_page'    => $paginated['last_page'],
                'per_page'     => $paginated['per_page'],
                'total'        => $paginated['total'],
            ],
        ];
    }

    /**
     * Customize the outgoing response for the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        if ($this->resource instanceof AbstractPaginator) {
            $paginated = $this->resource->toArray();
        }

        return parent::toResponse($request);
    }
}
