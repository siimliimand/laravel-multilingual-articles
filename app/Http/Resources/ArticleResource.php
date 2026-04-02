<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * The custom message to return with the resource.
     */
    protected ?string $customMessage = null;

    /**
     * Set a custom message for the response.
     */
    public function setMessage(string $message): self
    {
        $this->customMessage = $message;

        return $this;
    }

    /**
     * Transform a single article/translation resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->resource instanceof \App\Models\Article) {
            return [
                'article_id'   => $this->resource->article_id,
                'node_type'   => $this->resource->node_type instanceof \BackedEnum
                    ? $this->resource->node_type->value
                    : $this->resource->node_type,
                'visibility'  => $this->resource->visibility instanceof \BackedEnum
                    ? $this->resource->visibility->value
                    : $this->resource->visibility,
                'translations' => $this->resource->translations->map(function ($translation) {
                    return [
                        'translation_id' => $translation->article_translation_id,
                        'language_code'  => $translation->language_code,
                        'title'          => $translation->title,
                        'path'           => $translation->path,
                        'content'        => $translation->content,
                        'summary'        => $translation->summary,
                        'keywords'       => $translation->keywords,
                        'status'         => $translation->status instanceof \BackedEnum
                            ? $translation->status->value
                            : $translation->status,
                        'created_by'     => $translation->created_by,
                        'modified_by'    => $translation->modified_by,
                        'created_at'     => $translation->created_at?->toIso8601String(),
                        'updated_at'     => $translation->updated_at?->toIso8601String(),
                    ];
                }),
                'created_at'  => $this->resource->created_at?->toIso8601String(),
                'updated_at'  => $this->resource->updated_at?->toIso8601String(),
            ];
        }

        return [
            'translation_id' => $this->resource['translation_id'] ?? null,
            'article_id'     => $this->resource['article_id'] ?? null,
            'language_code'  => $this->resource['language_code'] ?? null,
            'title'          => $this->resource['title'] ?? null,
            'path'           => $this->resource['path'] ?? null,
            'content'        => $this->resource['content'] ?? null,
            'summary'        => $this->resource['summary'] ?? null,
            'keywords'       => $this->resource['keywords'] ?? null,
            'status'         => $this->resource['status'] ?? null,
            'node_type'      => $this->resource['node_type'] ?? null,
            'visibility'     => $this->resource['visibility'] ?? null,
            'created_by'     => $this->resource['created_by'] ?? null,
            'modified_by'    => $this->resource['modified_by'] ?? null,
            'created_at'     => $this->resource['created_at'] ?? null,
            'updated_at'     => $this->resource['updated_at'] ?? null,
        ];
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
            'message' => $this->customMessage ?? 'Article retrieved successfully.',
        ];
    }
}
