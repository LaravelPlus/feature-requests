<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'content' => 'sometimes|required|string|min:3|max:2000',
            'is_approved' => 'sometimes|boolean',
            'is_pinned' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'content.required' => 'The content field is required.',
            'content.min' => 'The content must be at least 3 characters.',
            'content.max' => 'The content may not be greater than 2000 characters.',
            'is_approved.boolean' => 'The approved field must be true or false.',
            'is_pinned.boolean' => 'The pinned field must be true or false.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'content' => 'content',
            'is_approved' => 'approved status',
            'is_pinned' => 'pinned status',
        ];
    }
}
