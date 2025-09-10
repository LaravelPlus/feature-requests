<?php

namespace LaravelPlus\FeatureRequests\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('manage feature request categories');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $categoryId = $this->route('category');
        
        return [
            'name' => 'sometimes|required|string|max:255|unique:feature_request_categories,name,' . $categoryId,
            'description' => 'sometimes|nullable|string|max:1000',
            'color' => 'sometimes|nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'sometimes|nullable|string|max:100',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'sometimes|nullable|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'name.unique' => 'The name has already been taken.',
            'description.max' => 'The description may not be greater than 1000 characters.',
            'color.regex' => 'The color must be a valid hex color code (e.g., #FF0000).',
            'icon.max' => 'The icon may not be greater than 100 characters.',
            'is_active.boolean' => 'The active field must be true or false.',
            'sort_order.integer' => 'The sort order must be an integer.',
            'sort_order.min' => 'The sort order must be at least 0.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'name',
            'description' => 'description',
            'color' => 'color',
            'icon' => 'icon',
            'is_active' => 'active status',
            'sort_order' => 'sort order',
        ];
    }
}
