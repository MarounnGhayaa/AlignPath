<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'username' => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email:rfc,dns', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', 'string', Rule::in(['student', 'mentor'])],
        ];
    }

    public function messages(): array {
        return [
            'email.unique' => 'This email is already registered.',
            'role.in'      => 'Role must be either student or mentor.',
        ];
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'username' => $this->username ? trim($this->username) : null,
            'email'    => $this->email ? strtolower(trim($this->email)) : null,
            'role'     => $this->role ? strtolower(trim($this->role)) : null,
        ]);
    }
}
