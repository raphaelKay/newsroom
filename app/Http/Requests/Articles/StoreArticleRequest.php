<?php

namespace App\Http\Requests\Articles;

use App\Models\Article;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArticleRequest extends FormRequest
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
            'title'          => ['required', 'string', 'max:150', Rule::unique(Article::class, 'title')],
            'body'           => ['required', 'string'],
            'excerpt'        => ['required', 'string', 'max:500'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1028'],
            'status'         => ['required', 'string', Rule::in(['draft', 'scheduled', 'published'])],
            'is_breaking'    => ['sometimes', 'boolean'],
            'published_at'   => ['nullable', 'required_if:status,scheduled', 'date'],
            'author_id'      => ['required', 'integer', 'exists:users,id'],
            'category_id'    => ['required', 'integer', 'exists:categories,id'],
            'tags'           => ['required', 'array', 'min:2', 'max:5'],
            'tags.*'         => ['integer', 'exists:tags,id'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'title.required'           => 'an article title is required.',
            'title.unique'             => 'an article with this title already exists.',
            'title.max'                => 'the title cannot exceed 150 characters.',
            'body.required'            => 'the article content body cannot be empty.',
            'excerpt.required'         => 'please provide a short excerpt.',
            'featured_image.image'     => 'the featured image must be a valid image file.',
            'featured_image.mimes'     => 'the featured image must be a file of type: jpg, jpeg, png, webp.',
            'featured_image.max'       => 'the featured image size cannot exceed 1MB.',
            'status.in'                => 'the selected status is invalid.',
            'published_at.required_if' => 'a publish date is required when scheduling an article.',
            'category_id.exists'       => 'the selected category is invalid.',
            'author_id.exists'         => 'the assigned author is invalid.',
            'tags.required'            => 'please select at least 2 tags.',
            'tags.min'                 => 'please select at least 2 tags.',
            'tags.max'                 => 'you cannot select more than 5 tags.',
            'tags.*.exists'            => 'one or more selected tags are invalid.',
        ];
    }

    /**
     * Prepare inputs prior to validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_breaking' => $this->boolean('is_breaking'),
            'author_id'   => $this->author_id ?: $this->user()?->id,
            'status'      => $this->status ?: 'draft',
        ]);
    }

    /**
     * Handle featured image upload and return relative path.
     */
    public function handleImageUpload(): ?string
    {
        if ($this->hasFile('featured_image')) {
            return $this->file('featured_image')->store('articles/featured', 'public');
        }

        return null;
    }
}