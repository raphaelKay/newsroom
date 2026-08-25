<?php

namespace App\Http\Requests\Categories;

use App\Models\Category;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $category = $this->route('category');
        $category_id = $category instanceof Category ? $category->id : Category::where('slug', $category)->value('id');

        return [
            'name' => ['required',  'string',  'min:2',  'max:100',  Rule::unique(Category::class, 'name')->ignore($category_id)],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id', Rule::notIn([$category_id])],
            'created_by' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required'     => 'this category needs a name.',
            'name.string'       => 'the name must be a string.',
            'name.unique'       => 'this category already exists.',
            'name.min'          => 'this name is too short.',
            'name.max'          => 'this name is too long.',
            'parent_id.exists'  => 'the selected parent category is invalid.',
            'parent_id.not_in'  => 'a category cannot be its own parent.',
        ];
    }

    /**
     * Prepare data prior to validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'parent_id'  => $this->parent_id ?: null,
            'created_by' => $this->user()?->id,
        ]);
    }
}