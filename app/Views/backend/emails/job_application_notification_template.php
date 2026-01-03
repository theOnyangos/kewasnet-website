<?= $this->include('backend/emails/inc/header') ?>

<!-- TITLE, TEXT & BUTTON -->
<tr>
  <td style="padding: 20px 15px 20px;">
    <table width="100%">
      <tr>
        <td style="padding: 20px 20px 20px">
          <h1 class="welcome-title">
            <span style="color: #080808;">New Job Application Received</span>
          </h1>
        </td>
      </tr>

      <tr>
        <td style="padding: 20px">
          <p>Dear Administrator,</p> <br>

          <p>You have received a new job application for the <b><?= $job_title ?></b> position.</p><br />

          <p><b>Applicant Details:</b></p>
          <ul>
            <li><b>Full Name:</b> <?= $full_name ?></li>
            <li><b>Email:</b> <?= $email ?></li>
            <li><b>Job Reference Number:</b> <?= $job_reference_number ?></li>
            <li><b>Job Type:</b> <?= $job_type ?></li>
          </ul><br />

          <p><b>Attached Document:</b> <a href="<?= $document_path ?>" target="_blank">Download Here</a></p><br />

          <p>Please review the application and take the necessary action.</p><br />

          <p>Best regards,</p>
          <p>KEWASNET System</p>
        </td>
      </tr>
    </table>
  </td>
</tr>

<?= $this->include('backend/emails/inc/footer') ?>
