<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Moderator Contact - KEWASNET</title>
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
            background: linear-gradient(135deg, #374391, #27abe0); 
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
        .info-section { 
            background: #f8f9fa; 
            padding: 20px; 
            border-radius: 6px; 
            margin-bottom: 25px; 
        }
        .info-row { 
            margin-bottom: 15px; 
            padding-bottom: 15px; 
            border-bottom: 1px solid #e9ecef; 
        }
        .info-row:last-child { 
            margin-bottom: 0; 
            padding-bottom: 0; 
            border-bottom: none; 
        }
        .info-label { 
            font-weight: 600; 
            color: #374391; 
            margin-bottom: 5px; 
            font-size: 14px; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
        }
        .info-value { 
            color: #495057; 
            font-size: 15px; 
        }
        .message-box { 
            background: #ffffff; 
            border: 1px solid #dee2e6; 
            border-radius: 6px; 
            padding: 20px; 
            margin-top: 20px; 
            white-space: pre-wrap; 
            word-wrap: break-word; 
        }
        .discussion-link {
            display: inline-block;
            background: linear-gradient(135deg, #374391, #27abe0);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
            font-weight: 600;
        }
        .footer { 
            background: #f8f9fa; 
            padding: 20px; 
            text-align: center; 
            color: #6c757d; 
            font-size: 13px; 
        }
        .footer p { 
            margin: 5px 0; 
        }
        .footer a { 
            color: #374391; 
            text-decoration: none; 
        }
        @media only screen and (max-width: 600px) {
            .container { 
                margin: 0; 
                border-radius: 0; 
            }
            .content { 
                padding: 20px; 
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛡️ Forum Moderator Message</h1>
            <p>A member needs your attention</p>
        </div>

        <div class="content">
            <p style="font-size: 16px; margin-bottom: 20px;">Hello Moderator,</p>
            
            <p style="font-size: 15px; color: #495057; margin-bottom: 25px;">
                A forum member has sent you a message regarding a discussion. Please review the details below:
            </p>

            <div class="info-section">
                <div class="info-row">
                    <div class="info-label">From</div>
                    <div class="info-value"><?= esc($userName) ?> (<?= esc($userEmail) ?>)</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Discussion</div>
                    <div class="info-value"><?= esc($discussionTitle) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Subject</div>
                    <div class="info-value"><?= esc($subject) ?></div>
                </div>
            </div>

            <div class="info-label" style="margin-bottom: 10px;">Message:</div>
            <div class="message-box">
                <?= nl2br(esc($message)) ?>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="<?= $discussionUrl ?>" class="discussion-link">View Discussion</a>
            </div>

            <p style="font-size: 14px; color: #6c757d; margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6;">
                <strong>Note:</strong> You can reply to this message by responding directly to this email. The member will receive your response.
            </p>
        </div>

        <div class="footer">
            <p><strong>Kenya Water & Sanitation Network (KEWASNET)</strong></p>
            <p>
                <a href="https://www.kewasnet.co.ke">www.kewasnet.co.ke</a> | 
                <a href="mailto:info@kewasnet.co.ke">info@kewasnet.co.ke</a>
            </p>
            <p style="margin-top: 15px; font-size: 12px;">
                This is an automated message from the KEWASNET forum system.
            </p>
        </div>
    </div>
</body>
</html>
