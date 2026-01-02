@component('mail::message')
# Feedback Reply Received

Hello {{ $user->name }},

Your feedback has received a new reply.

## Feedback Details
**Title:** {{ $feedback->title }}
**Tracking Code:** {{ $feedback->tracking_code }}

## Reply From
**Name:** {{ $reply->name }}
**Date:** {{ $reply->created_at->format('Y-m-d H:i') }}

## Reply Content
{{ $reply->content }}

@component('mail::button', ['url' => route('feedback.track', ['id' => $feedback->tracking_code])])
View Full Feedback
@endcomponent

If you have any questions, please feel free to reply to this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
