<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
            'title'            => ['sometimes', 'nullable', 'string', 'max:70'],
            'node_type'        => ['sometimes', 'nullable', 'string', 'in:article,user_agreement'],
            'status'           => ['sometimes', 'nullable', 'string', 'in:draft,published,unpublished'],
            'language_code'    => ['sometimes', 'nullable', 'string', 'size:2'],
            'updated_at_from'  => ['sometimes', 'nullable', 'date', 'date_format:Y-m-d'],
            'updated_at_to'    => ['sometimes', 'nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:updated_at_from'],
            'page'             => ['sometimes', 'nullable', 'integer', 'min:1'],
            'per_page'         => ['sometimes', 'nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'updated_at_from.date_format' => 'The updated_at_from must be in Y-m-d format.',
            'updated_at_to.date_format'   => 'The updated_at_to must be in Y-m-d format.',
            'updated_at_to.after_or_equal' => 'The updated_at_to must be a date on or after updated_at_from.',
        ];
    }
}
