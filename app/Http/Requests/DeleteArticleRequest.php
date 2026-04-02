<?php

namespace App\Http\Requests;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;

class DeleteArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Uses ArticlePolicy to check ownership via the first translation's created_by field.
     */
    public function authorize(): bool
    {
        $article = Article::find($this->route('id'));
        return $article && $this->user()->can('delete', $article);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [];
    }
}
