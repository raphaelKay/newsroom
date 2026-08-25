<?php

namespace App\Http\Requests\Tags;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Tag;

class StoreTagRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'min:2', 'max:100', 'unique:' . Tag::class . ',name'],
            'created_by' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'this tag needs a name.',
            'name.string'  => 'the name must be a string.',
            'name.unique'   => 'this tag already exists.',
            'name.min'   => 'this name is too short.',
            'name.max'   => 'this name is too long.',
        ];
    }

    /**
     * Prepare data prior to validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'created_by' => $this->user()?->id,
        ]);
    }
}
