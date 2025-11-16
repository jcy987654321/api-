<?php

namespace App\Http\Requests\Admin;

use App\Services\EncryptionService;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'encrypted_password' => ['required_if:password,null', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Decrypt RSA-encrypted password if provided
        if ($this->has('encrypted_password') && $this->encrypted_password) {
            try {
                $encryptionService = app(EncryptionService::class);
                $privateKey = session('rsa_private_key');

                if ($privateKey) {
                    $decrypted = $encryptionService->decryptRsa(
                        $this->encrypted_password,
                        $privateKey
                    );
                    $this->merge(['password' => $decrypted]);
                }
            } catch (\Exception $e) {
                // If decryption fails, continue with provided password
                $this->merge(['password' => $this->password ?? null]);
            }
        }
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid email address.',
            'password.required' => 'Password is required.',
        ];
    }
}
