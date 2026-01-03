<?= $this->include('backend/emails/inc/header') ?>

<!-- TITLE, TEXT & BUTTON -->
<tr>
  <td style="padding: 20px 15px 20px;">
    <table width="100%">
      <tr>
        <td style="padding: 20px 20px 20px">
          <h1 class="welcome-title">
            Verify Your
            <span style="color: #27aae0">Email Address!</span>
          </h1>
        </td>
      </tr>

      <tr>
        <td style="padding: 20px">
          <p>Dear <?= esc($firstName) ?>,</p> <br>

          <p>Thank you for registering with KEWASNET. To complete your registration and activate your account, please verify your email address.</p> <br />

          <p>Click the button below to verify your email address:</p> <br />

          <!-- Call to Action Button -->
          <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
            <tr>
              <td align="center">
                <a href="<?= esc($verificationUrl) ?>" 
                   style="display: inline-block; padding: 15px 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                          color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: bold; 
                          box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                  Verify Email Address
                </a>
              </td>
            </tr>
          </table>

          <p>Alternatively, you can copy and paste the following verification code:</p> <br />

          <div style="background-color: #f3f4f6; padding: 15px; border-radius: 8px; border-left: 4px solid #27aae0; margin: 20px 0;">
            <p style="font-size: 16px; font-weight: bold; color: #1f2937; margin: 0; word-break: break-all;">
              Verification Code: <span style="color: #27aae0;"><?= esc($token) ?></span>
            </p>
          </div>

          <p><strong>Note:</strong> This verification link will expire in 24 hours for security purposes.</p> <br />

          <p>If you did not create an account with KEWASNET, please disregard this email or contact our support team at info@kewasnet.co.ke.</p> <br />

          <p>Thank you for joining KEWASNET!</p> <br/>

          <p>Best regards,<br/>
          The KEWASNET Team</p>
        </td>
      </tr>
    </table>
  </td>
</tr>

<?= $this->include('backend/emails/inc/footer') ?>
