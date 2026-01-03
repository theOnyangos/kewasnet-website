<?= $this->include('backend/emails/inc/header') ?>

<!-- TITLE, TEXT & BUTTON -->
<tr>
  <td style="padding: 20px 15px 20px;">
    <table width="100%">
      <tr>
        <td style="padding: 20px 20px 20px">
          <h1 class="welcome-title">
            Reset
            <span style="color: #27aae0">Password!</span>
          </h1>
        </td>
      </tr>

      <tr>
        <td style="padding: 20px">
          <p>Dear <?= $userName ?>,</p> <br>

            <p>We hope this message finds you well. It has come to our attention that you have requested a password reset for your [Your Company Name] account. To ensure the security of your account, we have generated a unique password reset code for you.</p> <br />

            <p>Your Password Reset Code: <b><?= $token ?></b></p> <br />

            <p>Use the button below to verify your account:</p> <br />

            <p>Please follow the steps below to reset your password:</p> <br />

            <p>1. Visit the KEWASNET login page: <a href="<?= base_url('client/login') ?>" style="color: #27aae0">Click here to login</a>.</p> <br />

            <p>2. Click on the "Forgot Password" link.</p> <br />

            <p>3. Enter your email address associated with your account.</p> <br />

            <p>4. When prompted, enter the provided password reset code.</p> <br />

            <p>5. Create a new password for your account following the specified guidelines.</p> <br />

            <p>Please note that the password reset code is valid for 30 minutes. If you did not request this password reset or have any concerns about the security of your account, please contact our support team immediately at info@kewasnet.co.ke.</p> <br />

            <p>Thank you for helping us maintain the security of your account. We appreciate your prompt attention to this matter.</p> <br/>

            <p>Best regards,</p>
        </td>
      </tr>
    </table>
  </td>
</tr>

<?= $this->include('backend/emails/inc/footer') ?>
