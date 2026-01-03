<?= $this->include('backend/emails/inc/header') ?>

<!-- TITLE, TEXT & BUTTON -->
<tr>
  <td style="padding: 20px 15px 20px;">
    <table width="100%">
      <tr>
        <td style="padding: 20px 20px 20px">
          <h1 class="welcome-title">
            Welcome
            <span style="color: #27aae0">Karibu!</span>
          </h1>
        </td>
      </tr>

      <tr>
            <td style="padding: 20px">
                <p>Dear <?= $user_name ?>,</p> <br>

                <p>Welcome to KEWASNET! We are thrilled to have you as an administrator of our community.</p> <br />

                <p>Your registration is now complete, and you are officially part of a vibrant community dedicated to enhancing political will and collective development capacity and mobilizing resources for transformational change in WASH governance and water resource security in Kenya.</p> <br />

                <p>Use the button below to verify your account:</p> <br />

                <p style="text-align: center;"><a href="<?= $verification_url ?>" class="button">Verify Account</a></p> <br />

                <p>Here are a few things you can do to get started:</p> <br />

                <p>1. <b>Explore Your Dashboard:</b> Log in to your account and explore your personalized dashboard. From there, you can manage user roles, settings, and much more.</p> <br />

                <p>2. <b>Connect with Others:</b> Collaborate with fellow administrators and team members. Our community is all about teamwork and shared experiences.</p> <br />

                <p>3. <b>Stay Informed:</b> Keep an eye on your inbox for updates, newsletters, and important announcements. We'll keep you informed about the latest happenings and exciting features on KEWASNET.</p> <br />

                <p>If you have any questions or need assistance, don't hesitate to reach out to our support team at info@kewasnet.co.ke.</p> <br />

                <p>Once again, welcome aboard! We're looking forward to your contributions in shaping the community and achieving our collective goals.</p> <br/>

                <p>Your Account Details:</p>
                <ul>
                    <li><b>Date Joined:</b> <?= $join_date ?></li>
                    <li><b>Employee Number:</b> <?= $employee_number ?></li>
                    <li><b>Login Credentials:</b> Your username is <b style="color: #27aae0"><?= $email ?></b> and your temporary password is <b style="color: #27aae0"><?= $password ?>.</b> </li>
                </ul><br />

                <p>Please log in and change your password for security reasons.</p><br />

                <p>Best regards,</p>
                <p>KEWASNET Team</p>
            </td>
        </tr>

      </tr>
    </table>
  </td>
</tr>

<?= $this->include('backend/emails/inc/footer') ?>
