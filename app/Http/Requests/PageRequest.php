<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->isActive() ?? false; }
    public function rules(): array
    {
        return ['title' => ['required', 'string', 'max:150'], 'slug' => ['nullable', 'string', 'max:180', Rule::unique('pages', 'slug')->ignore($this->route('page'))],
            'status' => ['required', Rule::in(['draft', 'published', 'scheduled'])], 'template' => ['required', 'string', 'max:80'],
            'published_at' => ['nullable', 'date'], 'featured_image' => ['nullable', 'string', 'max:500'],
            'seo' => ['array'], 'seo.title' => ['nullable', 'string', 'max:180'], 'seo.description' => ['nullable', 'string', 'max:320'],
            'seo.canonical_url' => ['nullable', 'url'], 'seo.og_title' => ['nullable', 'string', 'max:180'], 'seo.og_description' => ['nullable', 'string', 'max:320'],
            'seo.og_image' => ['nullable', 'string', 'max:500'], 'seo.indexable' => ['nullable', 'boolean'], 'seo.followable' => ['nullable', 'boolean'],
            'sections_json' => ['nullable', 'json'], 'sections' => ['nullable', 'array'], 'sections.*.id' => ['nullable', 'integer'], 'sections.*.type' => ['required', 'string'],
            'sections.*.active' => ['nullable', 'boolean'], 'sections.*.content' => ['nullable', 'array'], 'sections.*.settings' => ['nullable', 'array']];
    }
}
