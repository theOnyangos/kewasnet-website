<?= $this->include('backend/emails/inc/header') ?>

<!-- TITLE, TEXT & BUTTON -->
<tr>
  <td style="padding: 20px 15px 20px;">
    <table width="100%">
      <tr>
        <td style="padding: 20px 20px 20px">
          <h1 class="welcome-title">
            <span style="color: #080808;">Account</span> Retrieval Instructions
          </h1>
        </td>
      </tr>

      <tr>
            <td style="padding: 20px">
                <p>Dear <?= $user_name ?>,</p> <br>

                <p>We have received a request to retrieve your account information for KEWASNET web application. Below are the instructions to reset your account password:</p><br />

                <p>1. <b>Click on the Password Reset Link:</b> <a href="<?= $verification_url ?>" style="color: #27aae0">Click Me!</a> This link will expire in 24 hours, so please make sure to use it promptly.</p><br />
                <p>2. <b>Follow the Instructions on the Password Reset Page:</b> You will be directed to a page where you can securely reset your password. Follow the instructions provided to complete the process.</p><br />
                <p>3. <b>Didn't Request a Password Reset?</b> If you didn't initiate this request, please ignore this email. Your account is secure, and no action is required on your part.

                <p>If you encounter any difficulties or have any questions, please don't hesitate to contact our support team at <a href="mailto:support@kewasnet.co.ke" style="color: #27aae0">support@kewasnet.co.ke</a>.</p><br />

                <p>Thank you for using KEWASNET web application.</p><br />

                <p>Best regards,</p>
                <p>KEWASNET</p>
                <p><b>Contact:</b> +254 (705)- 530-499 | info@kewasnet.co.ke</p>
            </td>
        </tr>

      </tr>
    </table>
  </td>
</tr>

<?= $this->include('backend/emails/inc/footer') ?>
