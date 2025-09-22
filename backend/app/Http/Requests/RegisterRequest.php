<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        $emailRules = [
            'required',
            'string',
            'max:255',
            Rule::unique('users', 'email'),
            'email:rfc',
        ];

        if (app()->environment('production')) {
            $idx = array_search('email:rfc', $emailRules, true);
            if ($idx !== false) {
                $emailRules[$idx] = 'email:rfc,dns';
            }
        }

        return [
            'username' => ['required', 'string', 'max:255'],
            'email'    => $emailRules,
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', 'string', Rule::in(['student', 'mentor'])],
        ];
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'username' => $this->username ? trim($this->username) : null,
            'email'    => $this->email ? strtolower(trim($this->email)) : null,
            'role'     => $this->role ? strtolower(trim($this->role)) : null,
        ]);
    }

    public function messages(): array {
        return [
            'email.unique' => 'This email is already registered.',
            'role.in'      => 'Role must be either student or mentor.',
        ];
    }
}
