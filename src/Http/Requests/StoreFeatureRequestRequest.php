<?php

namespace LaravelPlus\FeatureRequests\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeatureRequestRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'category_id' => 'nullable|exists:feature_request_categories,id',
            'priority' => 'nullable|in:low,medium,high,critical',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'is_public' => 'nullable|boolean',
            'due_date' => 'nullable|date|after:today',
            'estimated_effort' => 'nullable|integer|min:1|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'description.required' => 'The description field is required.',
            'description.min' => 'The description must be at least 10 characters.',
            'category_id.exists' => 'The selected category is invalid.',
            'priority.in' => 'The priority must be one of: low, medium, high, critical.',
            'tags.array' => 'The tags must be an array.',
            'tags.*.string' => 'Each tag must be a string.',
            'tags.*.max' => 'Each tag may not be greater than 50 characters.',
            'is_public.boolean' => 'The public field must be true or false.',
            'due_date.date' => 'The due date must be a valid date.',
            'due_date.after' => 'The due date must be a date after today.',
            'estimated_effort.integer' => 'The estimated effort must be an integer.',
            'estimated_effort.min' => 'The estimated effort must be at least 1 hour.',
            'estimated_effort.max' => 'The estimated effort may not be greater than 1000 hours.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'title',
            'description' => 'description',
            'category_id' => 'category',
            'priority' => 'priority',
            'tags' => 'tags',
            'is_public' => 'public visibility',
            'due_date' => 'due date',
            'estimated_effort' => 'estimated effort',
        ];
    }
}
