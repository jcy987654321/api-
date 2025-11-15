<!DOCTYPE html>
<html>
<head>
    <title>Friend Link Application Status</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2>Friend Link Application Status</h2>
        
        <p>Hello,</p>
        
        <p>Your friend link application for <strong>{{ $friendLink->name }}</strong> ({{ $friendLink->url }}) has been <strong>{{ $status }}</strong>.</p>
        
        @if($status == 'approved')
            <p style="color: green;">
                Congratulations! Your site will now be displayed in our friend links section.
            </p>
        @elseif($status == 'rejected')
            <p style="color: red;">
                Unfortunately, your application did not meet our criteria at this time.
            </p>
            @if($reason)
                <div style="background: #f8d7da; border-left: 4px solid #dc3545; padding: 10px; margin: 15px 0;">
                    <strong>Reason:</strong> {{ $reason }}
                </div>
            @endif
        @endif
        
        <p>If you have any questions, please feel free to contact us.</p>
        
        <p>
            Best regards,<br>
            The Admin Team
        </p>
    </div>
</body>
</html>
