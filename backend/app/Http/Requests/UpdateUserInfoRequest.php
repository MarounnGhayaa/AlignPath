<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserInfoRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        $userId = (int) ($this->route('id') ?? $this->route('user'));

        return [
            'username' => ['sometimes', 'required', 'string', 'min:3', 'max:30'],
            'email'    => [
                'sometimes',
                'required',
                'string',
                'email:rfc,dns',
                'max:100',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => ['sometimes', 'required', 'string', 'min:8', 'max:128'],
            'location' => ['sometimes', 'required', 'string', 'min:2', 'max:120'],
        ];
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'username' => $this->username ? trim($this->username) : null,
            'email'    => $this->email ? strtolower(trim($this->email)) : null,
            'location' => $this->location ? trim($this->location) : null,
        ]);
    }
}
