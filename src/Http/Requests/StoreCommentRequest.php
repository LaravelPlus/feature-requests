<?php

namespace LaravelPlus\FeatureRequests\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
            'feature_request_id' => 'required|exists:feature_requests,id',
            'parent_id' => 'nullable|exists:feature_request_comments,id',
            'content' => 'required|string|min:3|max:2000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'feature_request_id.required' => 'The feature request ID is required.',
            'feature_request_id.exists' => 'The selected feature request is invalid.',
            'parent_id.exists' => 'The selected parent comment is invalid.',
            'content.required' => 'The content field is required.',
            'content.min' => 'The content must be at least 3 characters.',
            'content.max' => 'The content may not be greater than 2000 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'feature_request_id' => 'feature request',
            'parent_id' => 'parent comment',
            'content' => 'content',
        ];
    }
}
