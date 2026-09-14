<?php

namespace App\Http\Requests\Auth;

use App\Enums\ContentProfile;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'alpha_dash',
                'min:3',
                'max:40',
                Rule::unique(User::class),
                Rule::notIn(['admin', 'studio', 'login', 'register', 'logout', 'forgot-password', 'reset-password', 'impersonation', 'livewire', 'locale', 'up']),
            ],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'confirmed', Password::defaults()],
            'content_profile' => ['nullable', Rule::enum(ContentProfile::class)],
            'marketing_opt_in' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('username')) {
            $this->merge([
                'username' => strtolower((string) $this->input('username')),
            ]);
        }
    }
}
