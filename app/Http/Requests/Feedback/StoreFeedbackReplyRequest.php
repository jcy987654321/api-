<?php

namespace App\Http\Requests\Feedback;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFeedbackReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'min:5', 'max:2000'],
            'send_email_notification' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'message.min' => 'The reply message must be at least 5 characters long.',
            'message.max' => 'The reply message may not exceed 2000 characters.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'message' => $this->string('message')?->trim(),
            'send_email_notification' => $this->boolean('send_email_notification', true),
        ]);
    }
}