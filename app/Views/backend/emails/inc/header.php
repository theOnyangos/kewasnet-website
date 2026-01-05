<?php 
  $db = \Config\Database::connect();
  $builder = $db->table('social_links');
  $social_links = $builder->get()->getResultArray();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KEWASNET Email Template</title>
    <style type="text/css">
      @import url("https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@700&display=swap");
      body {
        margin: 0;
      }
      table {
        border-spacing: 0;
      }
      td {
        padding: 0;
      }
      img {
        border: 0;
      }

      .wrapper {
        width: 100%;
        table-layout: fixed;
        background-color: #ecf7fb;
        padding-bottom: 100px;
      }

      .main {
        width: 100%;
        max-width: 600px;
        background-color: #ffffff;
        font-family: sans-serif;
        color: #4a4a4a;
        box-shadow: 0 0 25px rgba(0, 0, 0, 0.15);
      }

      .social {
        width: 38px;
      }

      .social-media {
        padding: 0 12px 0 62px;
      }

      .two-columns {
        font-size: 0;
        text-align: center;
      }

      .two-columns .column {
        width: 100%;
        max-width: 300px;
        display: inline-block;
        vertical-align: top;
      }

      h3 {
        font-family: "Roboto Condensed", sans-serif;
        font-size: 28px;
        font-weight: 700;
      }

      .button {
        display: inline-block;
        padding: 10px 20px;
        background-color: #27aae0; /* blue #27aae0 */
        color: #ffffff;
        font-family: sans-serif;
        font-size: 16px;
        text-decoration: none;
        border-radius: 4px;
      }

      .three-columns {
        font-size: 0;
        padding: 10px 0 20px;
      }
      .three-columns .column {
        width: 100%;
        max-width: 200px;
        display: inline-block;
        vertical-align: top;
      }
      .three-columns .padding {
        padding: 25px;
      }
      .three-columns .content {
        font-size: 16px;
        line-height: 20px;
      }

      .two-columns.last {
        padding: 35px 0;
      }

      .two-columns .padding {
        padding: 20px;
        line-height: 20px;
      }

      .two-columns .content {
        text-align: left;
      }

      p {
        font-family: sans-serif;
        font-size: 16px;
        line-height: 24px;
        color: #4a4a4a;
        margin: 0;
      }

      .welcome-title {
        font-family: 'Roboto Condensed', sans-serif;
        font-size: 30px;
        font-weight: 700;
        color: #27aae0;
        margin: 0;
        text-align: center;
      }

      @media screen and (max-width: 480px) {
        .social-media {
          padding: 0 12px 0 12px;
        }
        .two-columns .column {
          max-width: 100% !important;
          display: block !important;
        }
        .welcome-title {
          font-size: 18px;
        }
        p {
          font-size: 14px;
          line-height: 20px;
        }
      }
    </style>
  </head>
  <body>
    <center class="wrapper" style="padding-top: 60px">
      <table class="main" width="100%">
        <!-- BORDER -->
        <tr>
          <td height="8" style="background-color: #364391"></td>
        </tr>

        <!-- LOGO & SOCIAL MEDIA SECTION -->
        <tr>
          <td style="padding: 14px 0 4px">
            <table width="100%">
              <tr>
                <td class="columns">
                  <table align="center">
                    <tr>
                      <td style="padding: 0 auto">
                        <a href="https://kewasnet.co.ke">
                          <img
                            src="https://kewasnet.co.ke/kewasnet_logo.png"
                            alt="KEWASNET Logo"
                            width="300"
                            height="auto"
                            style="display: block; margin: 0 auto"
                            title="KEWASNET Logo"
                          />
                        </a>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>