<?php

namespace App\Http\Requests\Feedback;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

class StoreFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'visitor_name' => ['nullable', 'string', 'max:255'],
            'visitor_email' => [
                'required',
                'string',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@qq\.com$/i',
            ],
            'subject' => ['required', 'string', 'min:3', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'attachment' => [
                'nullable',
                'file',
                'max:5120', // 5MB
                'mimes:txt,doc,docx,pdf,jpg,jpeg,png,gif,webp,xls,xlsx',
            ],
            'consent' => ['required', 'accepted'],
            'honeypot' => ['nullable', 'string', 'max:0'], // Should be empty for humans
        ];
    }

    public function messages(): array
    {
        return [
            'visitor_email.regex' => 'Please provide a valid QQ email address (e.g., user@qq.com).',
            'subject.min' => 'The subject must be at least 3 characters long.',
            'message.min' => 'The message must be at least 10 characters long.',
            'message.max' => 'The message may not exceed 5000 characters.',
            'attachment.max' => 'The attachment may not be larger than 5MB.',
            'attachment.mimes' => 'The attachment must be one of the following types: text documents, PDFs, images, or spreadsheets.',
            'consent.accepted' => 'You must agree to the terms and conditions to submit feedback.',
            'honeypot.max' => 'Invalid submission detected.', // Will catch bots
        ];
    }

    public function getSafeAttachment(): ?UploadedFile
    {
        $attachment = $this->file('attachment');
        
        if (!$attachment) {
            return null;
        }

        // Additional security checks
        if ($attachment->getSize() > 5 * 1024 * 1024) { // 5MB
            throw new \InvalidArgumentException('Attachment too large');
        }

        $allowedMimes = [
            'text/plain',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        if (!in_array($attachment->getMimeType(), $allowedMimes)) {
            throw new \InvalidArgumentException('Invalid attachment type');
        }

        return $attachment;
    }

    protected function prepareForValidation(): void
    {
        // Trim string inputs
        $this->merge([
            'visitor_name' => $this->string('visitor_name')?->trim(),
            'visitor_email' => $this->string('visitor_email')?->trim(),
            'subject' => $this->string('subject')?->trim(),
            'message' => $this->string('message')?->trim(),
        ]);

        // Check honeypot field for bots
        if ($this->filled('honeypot')) {
            abort(422, 'Invalid submission detected.');
        }
    }
}