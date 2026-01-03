<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Deleted</title>
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
            color: #ef4444;
            margin: 0;
        }
        .content {
            background-color: white;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .info-box {
            background-color: #fee2e2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin: 20px 0;
        }
        .info-box strong {
            color: #991b1b;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 20px;
        }
        .deleted-items {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }
        .deleted-items ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .deleted-items li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Account Deleted</h1>
        </div>

        <div class="content">
            <p>Hello <?= esc($firstName) ?>,</p>

            <p>This email confirms that your KEWASNET account has been permanently deleted by an administrator on <strong><?= esc($deletionDate) ?></strong>.</p>

            <div class="info-box">
                <strong>⚠️ Important Information:</strong><br>
                Your account and all associated data have been permanently removed from our system. This action cannot be undone.
            </div>

            <div class="deleted-items">
                <strong>The following data has been removed:</strong>
                <ul>
                    <li>Profile information and account credentials</li>
                    <li>Course enrollments and progress</li>
                    <li>Certificates and achievements</li>
                    <li>Forum posts and discussions</li>
                    <li>Blog comments and interactions</li>
                    <li>Event registrations</li>
                    <li>Bookmarks and saved content</li>
                    <li>All other user-related data</li>
                </ul>
            </div>

            <p>If you believe this deletion was made in error or have any questions, please contact our support team immediately at <a href="mailto:support@kewasnet.org">support@kewasnet.org</a>.</p>

            <p>If you wish to rejoin KEWASNET in the future, you will need to create a new account through our registration process.</p>

            <p>Thank you for being part of the KEWASNET community.</p>

            <p>Best regards,<br>
            <strong>KEWASNET Team</strong></p>
        </div>

        <div class="footer">
            <p>This is an automated notification. Please do not reply to this email.</p>
            <p>&copy; <?= date('Y') ?> KEWASNET. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
