<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Changed</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2563eb;
            margin: 0;
        }
        .content {
            background-color: white;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .password-box {
            background-color: #f3f4f6;
            border-left: 4px solid #2563eb;
            padding: 15px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: bold;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 20px;
        }
        .warning {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Password Changed</h1>
        </div>

        <div class="content">
            <p>Hello <?= esc($firstName) ?>,</p>

            <p>Your password has been changed by an administrator. You can now use the following credentials to log in to your account:</p>

            <div class="password-box">
                New Password: <?= esc($newPassword) ?>
            </div>

            <div class="warning">
                <strong>⚠️ Important Security Notice:</strong><br>
                For your security, we strongly recommend changing this password immediately after logging in. Please choose a strong, unique password that you haven't used elsewhere.
            </div>

            <p style="text-align: center;">
                <a href="<?= esc($loginUrl) ?>" class="button">Login to Your Account</a>
            </p>

            <p>If you did not request this password change or have any concerns about your account security, please contact support immediately.</p>

            <p>Best regards,<br>
            <strong>KEWASNET Team</strong></p>
        </div>

        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; <?= date('Y') ?> KEWASNET. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
