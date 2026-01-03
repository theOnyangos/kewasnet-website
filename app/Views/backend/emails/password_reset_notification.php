<?= $this->include('backend/emails/inc/header') ?>

<!-- TITLE, TEXT & BUTTON -->
<tr>
  <td style="padding: 20px 15px 20px;">
    <table width="100%">
      <tr>
        <td style="padding: 20px 20px 20px">
          <h1 class="welcome-title">
            Password
            <span style="color: #27aae0">Reset!</span>
          </h1>
        </td>
      </tr>

      <tr>
        <td style="padding: 20px">
          <p>Dear <?= esc($firstName) ?>,</p> <br>

          <p>Your password has been reset by an administrator. Below is your new temporary password:</p> <br />

          <div style="background-color: #f3f4f6; padding: 15px; border-radius: 8px; border-left: 4px solid #27aae0; margin: 20px 0;">
            <p style="font-size: 18px; font-weight: bold; color: #1f2937; margin: 0;">
              New Password: <span style="color: #27aae0;"><?= esc($newPassword) ?></span>
            </p>
          </div>

          <p>For your security, we strongly recommend that you change this password after logging in.</p> <br />

          <p>To log in with your new password:</p> <br />

          <p>1. Visit the KEWASNET login page: <a href="<?= esc($loginUrl) ?>" style="color: #27aae0">Click here to login</a>.</p> <br />

          <p>2. Enter your email address.</p> <br />

          <p>3. Use the new password provided above.</p> <br />

          <p>4. Navigate to your profile settings and change your password to something memorable.</p> <br />

          <p>If you did not request this password reset or have any concerns about the security of your account, please contact our support team immediately at info@kewasnet.co.ke.</p> <br />

          <p>Thank you for your cooperation.</p> <br/>

          <p>Best regards,<br/>
          The KEWASNET Team</p>
        </td>
      </tr>
    </table>
  </td>
</tr>

<?= $this->include('backend/emails/inc/footer') ?>
