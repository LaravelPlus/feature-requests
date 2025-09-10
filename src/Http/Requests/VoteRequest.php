<?php

namespace LaravelPlus\FeatureRequests\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VoteRequest extends FormRequest
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
            'vote_type' => 'required|in:up,down',
            'comment' => 'nullable|string|max:500',
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
            'vote_type.required' => 'The vote type is required.',
            'vote_type.in' => 'The vote type must be either up or down.',
            'comment.string' => 'The comment must be a string.',
            'comment.max' => 'The comment may not be greater than 500 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'feature_request_id' => 'feature request',
            'vote_type' => 'vote type',
            'comment' => 'comment',
        ];
    }
}
