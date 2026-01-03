<?php 

  $db = \Config\Database::connect();
  $builder = $db->table('social_links');
  $social_links = $builder->get()->getResultArray();
?>
        <!-- BORDER -->
        <tr>
          <td height="2" style="background-color: #080808"></td>
        </tr>

        <!-- FOOTER SECTION -->
        <tr>
          <td
            style="
              padding: 20px 0 0;
              text-align: center;
              background-color: #101112;
              color: #fff;
            "
          >
            <table width="100%">
              <tr>
                <td style="padding: 25px 20px">
                  <p style="padding: 3px; color: #fff; font-weight: 500;">The Kenya Water and Sanitation Civil Society Network (KEWASNET)</p>

                  <p
                    style="
                      font-family: sans-serif;
                      font-size: 12px;
                      line-height: 18px;
                      color: #979797;
                      margin: 0;
                      padding: 10px 20px;
                    "
                  >
                    This e-mail has been sent to <?= $email ?? 'info@kewasnet.co.ke' ?>. <br>
                    You are receiving this email as a registered company account <br>
                    holder. If you have any queries regarding this email, email <br>
                    info@kewasnet.co.ke and one of our Customer Support Agents will <br>
                    get back to you. <a style="color: #fff;" href="<?= base_url() ?>">info@kewasnet.co.ke</a>
                  </p>

                  <a href="<?= $social_links[0]['facebook'] ?>">
                    <img
                      src="https://spintowinkenya.com/images/facebook.png"
                      alt="Facebook"
                      class="social"
                      title="Facebook"
                    />
                  </a>
                  <a href="<?= $social_links[0]['twitter'] ?>">
                    <img
                      src="https://spintowinkenya.com/images/twitter.png"
                      alt="Twitter"
                      class="social"
                      title="Twitter"
                    />
                  </a>
                  <a href="<?= $social_links[0]['instagram'] ?>">
                    <img
                      src="https://spintowinkenya.com/images/instagram.png"
                      alt="Instagram"
                      class="social"
                      title="Instagram"
                    />
                  </a>
                  <a href="<?= $social_links[0]['linkedin'] ?>">
                    <img
                      src="https://spintowinkenya.com/images/linkedin.png"
                      alt="LinkedIn"
                      class="social"
                      title="linkedin"
                    />
                  </a>

                  <p style="padding: 5px; border-bottom: 1px solid #494949"></p>

                  <p style="color: #747373;padding-top: 10px;">
                    &copy; <?= date("Y") ?> KEWASNET. All rights reserved
                  </p>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>

      <!-- End Main Class -->
    </center>
    <!-- End Wrapper -->
  </body>
</html>

