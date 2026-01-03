<?= $this->include('backend/emails/inc/header') ?>

<!-- TITLE, TEXT & BUTTON -->
<tr>
  <td style="padding: 20px 15px 20px;">
    <table width="100%">
      <tr>
        <td style="padding: 20px 20px 20px">
          <h1 class="welcome-title">
            Account Deletion Confirmation
          </h1>
        </td>
      </tr>

      <tr>
            <td style="padding: 20px">
                <p>Dear <?= $user_name ?>,</p> <br>

                <p>We have received your request to delete your account from our platform. Before we proceed, we want to ensure you have the opportunity to retrieve any important data or information associated with your account.</p> <br />

                <p>Your account deletion request has initiated a 30-day grace period during which you can still access your account and retrieve any data you may need. After this period, your account and all associated data will be permanently deleted from our system and cannot be recovered.</p> <br />

                <p>To retrieve your account:</p><br />

                <ol>
                    <li>Visit <a href="https://kewasnet.co.ke" style="color: #27aae0">www.kewasnet.co.ke</a> website.</li>
                    <li>Log in to your account using your username and password.</li>
                    <li>Navigate to the settings or profile section.</li>
                    <li>Look for the option to cancel or revoke your account deletion request.</li>
                    <li>Follow the prompts to confirm the cancellation of your deletion request. </li>
                </ol><br />

                <p>If you have any trouble accessing your account or need assistance during this process, please don't hesitate to contact our support team at <a href="mailto:support@kewasnet.co.ke" style="color: #27aae0">support@kewasnet.co.ke</a>. We're here to help!</p><br />

                <p>Please note that if no action is taken within the 30-day period, your account and all associated data will be permanently deleted from our system, and we will not be able to recover it.</p><br />

                <p>Thank you for being a part of KEWASNET. We hope to see you back soon!</p><br />

                <p>Best regards,</p>
                <p>Admin</p>
                <p>KEWASNET</p>
            </td>
        </tr>

      </tr>
    </table>
  </td>
</tr>

<?= $this->include('backend/emails/inc/footer') ?>
