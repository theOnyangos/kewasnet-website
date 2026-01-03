<?= $this->include('backend/emails/inc/header') ?>

<!-- TITLE, TEXT & BUTTON -->
<tr>
  <td style="padding: 20px 15px 20px;">
    <table width="100%">
      <tr>
        <td style="padding: 20px 20px 20px">
          <h1 class="welcome-title"> Role <span style="color: #27aae0">Assignment!</span> </h1>
        </td>
      </tr>

      <tr>
        <td style="padding: 20px">
          <p>Dear <?= $user_name ?>,</p> <br>

          <p>We are thrilled to inform you that you have been assigned the role of Instructor at KEWASNET. Congratulations on this achievement!</p> <br />

          <p>Your expertise and dedication have been recognized, and we believe that you will excel in this new role. As an Instructor, you will play a crucial role in shaping the learning experiences of our team members and contributing to the growth and success of our company.</p> <br />

          <p>Your responsibilities will include:</p> <br />

          <p>1. Developing and delivering high-quality training materials.</p> <br />

          <p>2. Conducting engaging and informative training sessions.</p> <br />

          <p>3. Providing guidance and support to learners</p> <br />

          <p>4. Collaborating with other team members to enhance learning initiatives </p> <br />

          <p>We have full confidence in your abilities to inspire and educate others, and we look forward to seeing the positive impact you will make in your new role.</p> <br />

          <p>If you have any questions or need assistance as you transition into your new position, please don't hesitate to reach out to [Contact Person/Department].</p> <br/>

          <p>Once again, congratulations on your appointment as an Instructor. We wish you all the best as you embark on this exciting journey!</p> <br/>

          <p>Best regards,</p>
          <p>Administrator</p>

          <p>KEWASNET Organization.</p>
      </td>

      </tr>
    </table>
  </td>
</tr>

<?= $this->include('backend/emails/inc/footer') ?>
