<?= $this->include('backend/emails/inc/header') ?>

<!-- TITLE, TEXT & BUTTON -->
<tr>
  <td style="padding: 20px 15px 20px;">
    <table width="100%">
      <tr>
        <td style="padding: 20px 20px 20px">
          <h1 class="welcome-title">
            <span style="color: #080808;">Customer contact</span> Email
          </h1>
        </td>
      </tr>

      <tr>
            <td style="padding: 20px">
                <p>Dear KEWASNET Team,</p> <br>

                <?= $message ?> <br>

                <p>Best regards,</p>
                <p><?= $full_name ?></p>
                <p><b>Contact:</b> <?= $phone ?> | <?= $email ?></p>
            </td>
        </tr>

      </tr>
    </table>
  </td>
</tr>

<?= $this->include('backend/emails/inc/footer') ?>
