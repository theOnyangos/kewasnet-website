<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Notification - KEWASNET Forum</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            line-height: 1.6; 
            color: #333; 
            margin: 0; 
            padding: 0; 
            background-color: #f4f4f4; 
        }
        .container { 
            max-width: 600px; 
            margin: 20px auto; 
            background: white; 
            border-radius: 8px; 
            overflow: hidden; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
        }
        .header { 
            background: linear-gradient(135deg, #ef4444, #dc2626); 
            color: white; 
            padding: 30px 20px; 
            text-align: center; 
        }
        .header h1 { 
            margin: 0 0 10px 0; 
            font-size: 24px; 
            font-weight: 600; 
        }
        .header p { 
            margin: 0; 
            opacity: 0.9; 
            font-size: 16px; 
        }
        .content { 
            padding: 30px; 
            background: #ffffff; 
        }
        .alert-box {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        .alert-box h2 {
            margin: 0 0 10px 0;
            color: #991b1b;
            font-size: 18px;
        }
        .alert-box p {
            margin: 0;
            color: #7f1d1d;
        }
        .info-section { 
            background: #f8f9fa; 
            padding: 20px; 
            border-radius: 6px; 
            margin-bottom: 25px; 
        }
        .info-row { 
            padding: 12px 0; 
            border-bottom: 1px solid #e9ecef; 
        }
        .info-row:last-child { 
            border-bottom: none; 
        }
        .info-label { 
            font-weight: 600; 
            color: #495057; 
            margin-bottom: 5px; 
            font-size: 14px; 
        }
        .info-value { 
            color: #212529; 
            font-size: 15px; 
        }
        .message-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .message-box p {
            margin: 0;
            color: #856404;
        }
        .footer { 
            padding: 20px 30px; 
            background: #f8f9fa; 
            text-align: center; 
            font-size: 14px; 
            color: #6c757d; 
        }
        .footer a { 
            color: #374391; 
            text-decoration: none; 
        }
        .footer a:hover { 
            text-decoration: underline; 
        }
        .button { 
            display: inline-block; 
            padding: 12px 30px; 
            background: linear-gradient(135deg, #374391, #27abe0); 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
            margin: 20px 0; 
            font-weight: 600; 
        }
        .button:hover { 
            opacity: 0.9; 
        }
        ul {
            padding-left: 20px;
            margin: 15px 0;
        }
        ul li {
            margin: 8px 0;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>⚠️ Report Notification</h1>
            <p>KEWASNET Forum Community Standards</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="alert-box">
                <h2>You Have Been Reported</h2>
                <p>A member of the KEWASNET forum community has reported your behavior or content.</p>
            </div>

            <p>Hello <strong><?= esc($reported_user_name) ?></strong>,</p>
            
            <p>We are writing to inform you that <?= esc($reporter_name) ?> has submitted a report regarding your activity in the KEWASNET forum on <strong><?= esc($report_date) ?></strong>.</p>

            <!-- Report Details -->
            <div class="info-section">
                <div class="info-row">
                    <div class="info-label">Report Reason:</div>
                    <div class="info-value"><?= esc($reason) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Additional Details:</div>
                    <div class="info-value"><?= esc($description) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Context:</div>
                    <div class="info-value"><?= ucfirst(esc($context_type)) ?></div>
                </div>
            </div>

            <div class="message-box">
                <p><strong>What happens next?</strong></p>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    <li>Forum moderators will review this report</li>
                    <li>Your content and activity will be examined</li>
                    <li>Appropriate action will be taken if necessary</li>
                </ul>
            </div>

            <p><strong>Community Guidelines Reminder:</strong></p>
            <ul>
                <li>Be respectful and courteous to all members</li>
                <li>No spam, harassment, or offensive content</li>
                <li>Stay on topic and contribute meaningfully</li>
                <li>Respect intellectual property and cite sources</li>
            </ul>

            <p>If you believe this report was made in error or have concerns about the reporting process, please contact the forum moderators directly.</p>

            <center>
                <a href="<?= base_url('ksp/networking-corner') ?>" class="button">Visit Forum</a>
            </center>

            <p style="color: #6c757d; font-size: 14px; margin-top: 25px;">
                <strong>Note:</strong> Multiple violations of community guidelines may result in temporary suspension or permanent removal from the forum. We encourage all members to maintain a positive and respectful environment.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Kenya Water and Sanitation Network (KEWASNET)</strong></p>
            <p>
                <a href="<?= base_url() ?>">Visit Website</a> | 
                <a href="<?= base_url('ksp/networking-corner') ?>">Forum</a> | 
                <a href="mailto:<?= getenv('email.fromEmail') ?>">Contact Support</a>
            </p>
            <p style="margin-top: 15px; font-size: 12px;">
                This is an automated notification from the KEWASNET forum system.<br>
                Please do not reply directly to this email.
            </p>
        </div>
    </div>
</body>
</html>
