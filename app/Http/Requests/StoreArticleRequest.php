<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Authentication is handled by API key middleware.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'node_type'     => ['required', 'string', 'in:article,user_agreement'],
            'visibility'    => ['required', 'string', 'in:public,private'],
            'language_code' => ['required', 'string', 'size:2', 'exists:site_languages,language_code'],
            'title'         => ['required', 'string', 'max:70'],
            'path'          => [
                'required',
                'string',
                'max:70',
                'unique:article_translations,path,NULL,article_translation_id,language_code,' . $this->input('language_code'),
            ],
            'content'       => ['required', 'string'],
            'status'        => ['required', 'string', 'in:draft,published,unpublished'],
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
