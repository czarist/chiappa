<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediaUploadRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->isActive() ?? false; }
    public function rules(): array { return ['file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif,svg', 'max:5120'], 'alt_text' => ['nullable', 'string', 'max:200']]; }
}
