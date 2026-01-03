<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Event Attendance Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        .wrapper {
          padding-top: 50px;
          padding-bottom: 50px;
          background-color: #ecf7fb;
        }
        h1 {
            color: #333333;
        }
        p {
            color: #666666;
            line-height: 1.5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #dddddd;
        }
        table th {
            background-color: #f8f8f8;
        }
        .event-details img {
            width: 200px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .social-links a {
            margin: 0 5px;
            text-decoration: none;
            color: #1a73e8;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                padding: 15px;
            }
            table th, table td {
                display: block;
                width: 100%;
                text-align: left;
            }
        }
    </style>
</head>
<body class="wrapper">
    <div class="email-container">
        <table align="center">
            <tr>
                <td style="padding: 0 auto">
                <a href="https://dev.kewasnet.co.ke">
                    <img
                    src="https://dev.kewasnet.co.ke/kewasnet_logo.png"
                    alt="KEWASNET Logo"
                    width="200"
                    height="auto"
                    style="display: block; margin: 0 auto"
                    title="KEWASNET Logo"
                    />
                </a>
                </td>
            </tr>
        </table>

        <h1>Event Attendance Confirmation</h1>
        <p>Dear <?= esc($full_name) ?>,</p>
        <p>We are excited to confirm your attendance at our upcoming event. Below are the details:</p>
        <table>
            <tr>
                <th>Event Title:</th>
                <td><?= esc($event_title) ?></td>
            </tr>
            <tr>
                <th>Date:</th>
                <td><?= esc($event_start_date) ?> - <?= esc($event_end_date) ?></td>
            </tr>
            <tr>
                <th>Time:</th>
                <td><?= esc($event_start_time) ?> - <?= esc($event_end_time) ?></td>
            </tr>
            <tr>
                <th>Location:</th>
                <td><?= esc($event_location) ?></td>
            </tr>
            <tr>
                <th>Reg. Number:</th>
                <td>KERN-<?= esc($registration_number) ?></td>
            </tr>
        </table>
        <img src="<?= esc($event_cover_image) ?>" alt="Event Cover Image" class="event-details" style="width: 200px;">

        <?php if($is_paid): ?>
        <p>Please note that this is a paid event. Once your payment is received, you will be assigned a ticket number. This is your registration number: <b style="color: #27aae0">KERN-<?= $registration_number ?></b></p><br />
        <?php else: ?>
        <p>This is a free event. You will be notified once your registration is confirmed.</p><br />
        <?php endif; ?>

        <p>We look forward to seeing you there!</p>
        <p>Stay connected with us on social media:</p>
        <div class="social-links">
            <?php foreach ($social_links as $platform => $link): ?>
                <a href="<?= esc($link) ?>" target="_blank"><?= ucfirst($platform) ?></a>
            <?php endforeach; ?>
        </div>
        <p>Best Regards,<br>KEWASNET</p>
    </div>
</body>
</html>

