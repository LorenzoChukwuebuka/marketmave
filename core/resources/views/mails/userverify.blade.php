<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        .email-container {
            width: 100%;
            background-color: #f4f4f4;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .email-content {
            max-width: 600px;
            background-color: #ffffff;
            padding: 20px;
            margin: 0 auto;
            border-radius: 8px;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .email-header {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .email-body {
            font-size: 16px;
            line-height: 1.5;
            color: #333333;
            margin-bottom: 20px;
        }
        .email-button {
            display: inline-block;
            background-color: #3490dc;
            color: #ffffff;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
        }
        .email-footer {
            font-size: 12px;
            color: #999999;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-content">
            <div class="email-header">
                Verification Mail
            </div>
            <div class="email-body">
                Please click on the button below to verify your email address.
            </div>
            <a href="{{ $url }}" class="email-button">Verify</a>
            <div class="email-footer">
                Thanks,<br>
                {{ config('app.name') }}
            </div>
        </div>
    </div>
</body>
</html>
