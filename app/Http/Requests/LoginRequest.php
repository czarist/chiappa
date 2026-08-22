<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
            'g-recaptcha-response' => [
                Rule::requiredIf(app()->environment('production')),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (app()->environment('production') && ! $this->recaptchaPassed($value)) {
                        $fail('Confirme que você não é um robô.');
                    }
                },
            ],
        ];
    }

    private function recaptchaPassed(?string $token): bool
    {
        if (! $token) {
            return false;
        }

        return (bool) Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $token,
            'remoteip' => $this->ip(),
        ])->json('success');
    }
}
