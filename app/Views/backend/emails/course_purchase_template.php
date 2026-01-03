<?= $this->include('backend/emails/inc/header') ?>

<!-- TITLE, TEXT & BUTTON -->
<tr>
  <td style="padding: 20px 15px 20px;">
    <table width="100%">
      <tr>
        <td style="padding: 20px 20px 20px">
          <h1 class="welcome-title">
            <span style="color: #080808;">Course</span> Purchase Information
          </h1>
        </td>
      </tr>

      <tr>
            <td style="padding: 20px">
                <p>Dear <?= $full_name ?>,</p> <br>

                <p>Thank you for purchasing the following course(s) from our platform:</p><br />

                <?php 
                    // Loop through the courses and display them
                    foreach ($courses as $index => $course) {
                        echo '<div style="display: flex; align-items: center; margin-bottom: 20px;border: 1px solid #c1c1c1;border-radius: 6px;padding: 10px;">';
                        
                        // Course image
                        echo '<img src="'.base_url($course['image_url']).'" alt="'.$course['title'].'" width="100" height="auto" style="margin-right: 20px;border-radius: 6px;" />';

                        // Course details
                        echo '<div>';
                        echo '<p>Course Name: <a href="'.base_url("client/courses/".$course['slug']).'"><b style="color: #364391">'.$course['title'].'</b></a></p>';
                        echo '<p>Price: <b>Ksh. '.number_format($course['discount_price']).'</b></p>';
                        echo '</div>';
                        
                        echo '</div>';
                    }
                ?>

                <p>Total Amount Paid: <b style="color: #27aae0">KES <?= number_format($amount, 2) ?></b></p>

                <p>Payment Method: <b style="color: #27aae0"><?= $payment_method ?></b></p>

                <p>Transaction Reference Number: <b style="color: #27aae0"><?= $reference_number ?></b></p> <br/>

                <p>Please keep this email for your records. If you have any questions or need further assistance, feel free to contact our support team at <a href="mailto:support@kewasnet.co.ke" style="color: #27aae0">support@kewasnet.co.ke</a>.</p><br />

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
