@component('mail::message')
# Feedback Resolved

Hello {{ $feedback->name }},

Great news! Your feedback has been resolved.

## Feedback Details
**Title:** {{ $feedback->title }}
**Tracking Code:** {{ $feedback->tracking_code }}
**Resolved At:** {{ $feedback->resolved_at->format('Y-m-d H:i') }}

## What Was Done?
We have reviewed and addressed your feedback. If you have any further questions or concerns, please don't hesitate to reach out.

@component('mail::button', ['url' => route('feedback.track', ['id' => $feedback->tracking_code])])
View Feedback Details
@endcomponent

We appreciate your feedback and helping us improve!

Best regards,<br>
{{ config('app.name') }}
@endcomponent
