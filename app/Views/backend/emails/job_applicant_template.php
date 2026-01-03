<?= $this->include('backend/emails/inc/header') ?>

<!-- TITLE, TEXT & BUTTON -->
<tr>
  <td style="padding: 20px 15px 20px;">
    <table width="100%">
      <tr>
        <td style="padding: 20px 20px 20px">
          <h1 class="welcome-title">
            <span style="color: #080808;">Job Application</span> Reference Number
          </h1>
        </td>
      </tr>

        <tr>
            <td style="padding: 20px">
                <p>Dear <?= $full_name ?>,</p> <br>

                <p>Thank you for applying for the <?= $job_title ?> position at KEWASNET. We have received your application and would like to confirm that it has been successfully submitted.:</p><br />

                <p>Your application reference number is: <b style="color: #27aae0"><?= $job_reference_number ?></b></p><br />

                <p>Please keep this reference number for your records. If you have any questions or need further assistance, feel free to contact us.</p><br />

                <p>Best regards,</p>
                <p>Admin,</p>
                <p>KEWASNET</p>
                <p><b>Contact:</b> +254 (705)- 530-499 | info@kewasnet.co.ke</p>
            </td>
        </tr>

      </tr>
    </table>
  </td>
</tr>

<?= $this->include('backend/emails/inc/footer') ?>
