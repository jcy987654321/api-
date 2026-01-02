@component('mail::message')
# {{ $title }}

{{ $message }}

---

If you have any questions, please contact our support team.

Best regards,<br>
{{ config('app.name') }}
@endcomponent
