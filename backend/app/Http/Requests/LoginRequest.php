<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        $emailRule = 'email:rfc';

        if (app()->environment('production')) {
            $emailRule = 'email:rfc,dns';
        }

        return [
            'email'    => ['required', 'string', 'max:255', $emailRule],
            'password' => ['required', 'string', 'min:8', 'max:128'],
        ];
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'email' => $this->email ? strtolower(trim($this->email)) : null,
        ]);
    }
}
