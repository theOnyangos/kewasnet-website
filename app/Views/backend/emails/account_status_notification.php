<?= $this->include('backend/emails/inc/header') ?>

<!-- TITLE, TEXT & BUTTON -->
<tr>
  <td style="padding: 20px 15px 20px;">
    <table width="100%">
      <tr>
        <td style="padding: 20px 20px 20px">
          <h1 class="welcome-title">
            Account Status
            <span style="color: #27aae0">Update</span>
          </h1>
        </td>
      </tr>

      <tr>
        <td style="padding: 20px">
          <p>Dear <?= esc($firstName) ?>,</p> <br>

          <?php if ($status === 'inactive'): ?>
            <p>We are writing to inform you that your KEWASNET account has been <strong style="color: #dc2626;">suspended</strong> by an administrator.</p> <br />

            <div style="background-color: #fee2e2; padding: 15px; border-radius: 8px; border-left: 4px solid #dc2626; margin: 20px 0;">
              <p style="font-size: 16px; font-weight: bold; color: #991b1b; margin: 0;">
                Account Status: <span style="color: #dc2626;">SUSPENDED</span>
              </p>
            </div>

            <p>During this suspension period, you will not be able to:</p>
            <ul style="margin-left: 20px;">
              <li>Access your account dashboard</li>
              <li>Participate in courses or learning activities</li>
              <li>Interact with the community forums</li>
              <li>Access any subscription-based features</li>
            </ul>
            <br />

            <p>If you believe this action was taken in error or would like to discuss the matter, please contact our support team immediately at info@kewasnet.co.ke.</p> <br />

          <?php else: ?>
            <p>Great news! Your KEWASNET account has been <strong style="color: #059669;">activated</strong> by an administrator.</p> <br />

            <div style="background-color: #d1fae5; padding: 15px; border-radius: 8px; border-left: 4px solid #059669; margin: 20px 0;">
              <p style="font-size: 16px; font-weight: bold; color: #065f46; margin: 0;">
                Account Status: <span style="color: #059669;">ACTIVE</span>
              </p>
            </div>

            <p>You now have full access to all features, including:</p>
            <ul style="margin-left: 20px;">
              <li>Your account dashboard</li>
              <li>All courses and learning materials</li>
              <li>Community forums and discussions</li>
              <li>All subscription-based features</li>
            </ul>
            <br />

            <p>You can log in to your account using the button below:</p> <br />

            <!-- Call to Action Button -->
            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
              <tr>
                <td align="center">
                  <a href="<?= esc($loginUrl) ?>" 
                     style="display: inline-block; padding: 15px 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                            color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: bold; 
                            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                    Log In to Your Account
                  </a>
                </td>
              </tr>
            </table>

          <?php endif; ?>

          <p>If you have any questions or concerns about this account status update, please don't hesitate to reach out to our support team at info@kewasnet.co.ke.</p> <br />

          <p>Thank you for your understanding.</p> <br/>

          <p>Best regards,<br/>
          The KEWASNET Team</p>
        </td>
      </tr>
    </table>
  </td>
</tr>

<?= $this->include('backend/emails/inc/footer') ?>
