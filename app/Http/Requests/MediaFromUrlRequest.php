<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediaFromUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isActive() ?? false;
    }

    public function rules(): array
    {
        return [
            'url' => ['required', 'url', 'max:2000'],
            'alt_text' => ['nullable', 'string', 'max:200'],
        ];
    }
}
