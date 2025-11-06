<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Contact Form Submission</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(90deg, #1e3c72, #2a5298);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .field {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #1e3c72;
        }
        .field-label {
            font-weight: bold;
            color: #1e3c72;
            margin-bottom: 5px;
            display: block;
        }
        .field-value {
            color: #555;
        }
        .message-box {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 5px;
            padding: 15px;
            margin-top: 10px;
            white-space: pre-wrap;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 New Contact Form Submission</h1>
            <p>You have received a new message from your website contact form</p>
        </div>
        
        <div class="content">
            <div class="field">
                <span class="field-label">From:</span>
                <span class="field-value">
                    @if(isset($name) && is_string($name))
                        {{ $name }}
                    @else
                        N/A
                    @endif
                    (
                    @if(isset($email) && is_string($email))
                        {{ $email }}
                    @else
                        N/A
                    @endif
                    )
                </span>
            </div>
            
            <div class="field">
                <span class="field-label">Subject:</span>
                <span class="field-value">
                    @if(isset($subject) && is_string($subject))
                        {{ $subject }}
                    @else
                        No Subject
                    @endif
                </span>
            </div>
            
            <div class="field">
                <span class="field-label">Message:</span>
                <div class="message-box">
                    @if(isset($messageContent) && is_string($messageContent))
                        {{ $messageContent }}
                    @else
                        No message content
                    @endif
                </div>
            </div>
            
            <div style="margin-top: 30px; padding: 15px; background: #e8f5e8; border-radius: 5px; border-left: 4px solid #4caf50;">
                <strong>💡 Quick Action:</strong><br>
                You can reply directly to this email to respond to 
                @if(isset($name) && is_string($name))
                    {{ $name }}
                @else
                    the user
                @endif
            </div>
        </div>
        
        <div class="footer">
            <p>This email was sent from your website contact form on {{ now()->format('F j, Y \a\t g:i A') }}</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>