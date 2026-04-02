<?php

namespace App\Http\Requests;

use App\Enums\NodeType;
use App\Enums\TranslationStatus;
use App\Enums\Visibility;
use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends FormRequest
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
        return $article && $this->user()->can('update', $article);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Resolve the translation_id to ignore in the unique check.
        // The route parameter is {id} (article_id); we look up the translation
        // for the given language_code so we can exclude it from the unique constraint.
        $languageCode = $this->input('language_code');
        $articleId    = $this->route('id');

        $translationId = null;
        if ($languageCode && $articleId) {
            $translationId = \App\Models\ArticleTranslation::where('article_id', $articleId)
                ->where('language_code', $languageCode)
                ->value('article_translation_id');
        }

        return [
            'node_type'     => ['sometimes', 'string', 'in:' . implode(',', NodeType::values())],
            'visibility'    => ['sometimes', 'string', 'in:' . implode(',', Visibility::values())],
            'language_code' => ['sometimes', 'string', 'size:2', 'exists:site_languages,language_code'],
            'title'         => ['sometimes', 'string', 'max:70'],
            'path'          => [
                'sometimes',
                'string',
                'max:70',
                Rule::unique('article_translations', 'path')
                    ->where('language_code', $languageCode)
                    ->ignore($translationId, 'article_translation_id'),
            ],
            'content'       => ['sometimes', 'string'],
            'status'        => ['sometimes', 'string', 'in:' . implode(',', TranslationStatus::values())],
            'summary'       => ['sometimes', 'nullable', 'string', 'max:180'],
            'keywords'      => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'path.unique' => 'The path has already been taken for the selected language.',
        ];
    }
}
