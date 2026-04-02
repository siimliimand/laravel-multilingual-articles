<?php

namespace App\Http\Requests;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;

class DeleteArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * For API key auth, access is granted via the middleware.
     * For user auth, uses ArticlePolicy to check ownership via the first translation's created_by field.
     */
    public function authorize(): bool
    {
        // API key authentication grants access via middleware
        if ($this->attributes->get('is_private_access')) {
            return true;
        }

        // For user authentication, check policy
        if (!$this->user()) {
            return false;
        }

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
