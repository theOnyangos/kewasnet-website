<?= $this->include('backend/emails/inc/header') ?>

<!-- TITLE, TEXT & BUTTON -->
<tr>
  <td style="padding: 20px 15px 20px;">
    <table width="100%">
      <tr>
        <td style="padding: 20px 20px 20px">
          <h1 class="welcome-title">
            <span style="color: #080808;">Course </span> Announcement
          </h1>
        </td>
      </tr>

      <tr>
            <td style="padding: 20px">
                Dear User,

                We hope this email finds you well and thriving in your endeavors. We're thrilled to share some exciting news with you!

                [Announcement Details: Briefly introduce the announcement and its significance. Whether it's a new feature, a special event, or any other update, make sure to highlight its value to the subscribers.]

                We believe this [announcement] will greatly enhance your experience with [product/service], providing you with [benefits or improvements].

                Your continued support and feedback have been invaluable to us, and we're committed to constantly improving and innovating to better serve your needs.

                Thank you for being part of our community. Stay tuned for further updates!

                Warm regards,

                Admin,
                KEWASNET
            </td>
        </tr>

      </tr>
    </table>
  </td>
</tr>

<?= $this->include('backend/emails/inc/footer') ?>
