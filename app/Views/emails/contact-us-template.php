<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Submission - KEWASNET</title>
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
            margin: 12px 0; 
            display: flex; 
            align-items: flex-start; 
        }
        .label { 
            font-weight: 600; 
            color: #374391; 
            min-width: 120px; 
            margin-right: 10px; 
        }
        .value { 
            flex: 1; 
            color: #555; 
        }
        .message-section { 
            margin: 25px 0; 
        }
        .message-box { 
            background: white; 
            padding: 25px; 
            border-left: 4px solid #27abe0; 
            border-radius: 4px; 
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); 
        }
        .message-title { 
            margin: 0 0 15px 0; 
            color: #374391; 
            font-size: 18px; 
            font-weight: 600; 
        }
        .message-content { 
            margin: 0; 
            line-height: 1.6; 
            color: #444; 
        }
        .footer { 
            background: #f8f9fa; 
            padding: 20px; 
            text-align: center; 
            border-top: 1px solid #e9ecef; 
        }
        .footer p { 
            margin: 5px 0; 
            font-size: 13px; 
            color: #666; 
        }
        .footer a { 
            color: #374391; 
            text-decoration: none; 
        }
        .divider { 
            height: 1px; 
            background: #e9ecef; 
            margin: 20px 0; 
        }
        .brand-logo { 
            display: inline-block; 
            padding: 8px 16px; 
            background: rgba(255, 255, 255, 0.1); 
            border-radius: 4px; 
            margin-bottom: 10px; 
            font-weight: 600; 
            letter-spacing: 1px; 
        }
        @media only screen and (max-width: 600px) {
            .container { 
                margin: 10px; 
                border-radius: 4px; 
            }
            .content { 
                padding: 20px; 
            }
            .info-row { 
                flex-direction: column; 
            }
            .label { 
                min-width: auto; 
                margin-bottom: 5px; 
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="brand-logo">KEWASNET</div>
            <h1>New Contact Form Submission</h1>
            <p>Website Contact Form</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Contact Information -->
            <div class="info-section">
                <div class="info-row">
                    <span class="label">From:</span>
                    <span class="value"><?= esc($fullName) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Email:</span>
                    <span class="value">
                        <a href="mailto:<?= esc($contactData['email']) ?>"><?= esc($contactData['email']) ?></a>
                    </span>
                </div>
                <div class="info-row">
                    <span class="label">Organization:</span>
                    <span class="value"><?= esc($organization) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Subject:</span>
                    <span class="value"><?= esc($contactData['subject']) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Date:</span>
                    <span class="value"><?= date('F j, Y \a\t g:i A') ?></span>
                </div>
            </div>

            <div class="divider"></div>

            <!-- Message Section -->
            <div class="message-section">
                <div class="message-box">
                    <h3 class="message-title">Message:</h3>
                    <div class="message-content">
                        <?= nl2br(esc($contactData['message'])) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>This email was sent from the KEWASNET website contact form.</strong></p>
            <p>To reply, please respond directly to: <a href="mailto:<?= esc($contactData['email']) ?>"><?= esc($contactData['email']) ?></a></p>
            <p style="margin-top: 15px; color: #999;">
                KEWASNET - Kenya Water and Sanitation Network<br>
                Empowering Communities Through Water Access
            </p>
        </div>
    </div>
</body>
</html>
