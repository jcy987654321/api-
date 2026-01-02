@component('mail::message')
# Feedback Received

Hello {{ $feedback->name }},

Thank you for submitting your feedback. We have received it and will process it as soon as possible.

## Feedback Details
**Title:** {{ $feedback->title }}
**Type:** {{ $feedback->type_label }}
**Tracking Code:** {{ $feedback->tracking_code }}
**Status:** {{ ucfirst($feedback->status) }}

## What Happens Next?
- Our team will review your feedback
- You will receive notifications when there are updates
- You can track your feedback status using the tracking code below

@component('mail::button', ['url' => route('feedback.track', ['id' => $feedback->tracking_code])])
Track Your Feedback
@endcomponent

**Tracking Code:** `{{ $feedback->tracking_code }}`

Please save this tracking code so you can check the status of your feedback later.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
