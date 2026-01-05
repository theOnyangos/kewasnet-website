<?= $this->include('backend/emails/inc/header') ?>

<!-- TITLE, TEXT & BUTTON -->
<tr>
  <td style="padding: 0;">
    <!-- Header Banner -->
    <table width="100%" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
      <tr>
        <td style="padding: 30px 20px; text-align: center;">
          <h1 style="color: #ffffff; font-size: 28px; margin: 0 0 10px 0; font-weight: 600;">Your Event Tickets</h1>
          <p style="color: rgba(255, 255, 255, 0.9); font-size: 16px; margin: 0;">Booking Confirmation</p>
        </td>
      </tr>
    </table>

    <!-- Content -->
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td style="padding: 30px 20px; background: #ffffff;">
          <p style="color: #334155; font-size: 16px; margin: 0 0 20px 0;">Dear Attendee,</p>

          <p style="color: #475569; font-size: 15px; line-height: 1.6; margin: 0 0 25px 0;">
            Thank you for booking tickets for <strong style="color: #0ea5e9;"><?= esc($event['title']) ?></strong>.
          </p>

          <!-- Booking Details Card -->
          <table width="100%" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: 10px; padding: 20px; margin: 0 0 25px 0; border-left: 4px solid #0ea5e9;">
            <tr>
              <td>
                <h2 style="color: #0ea5e9; font-size: 18px; margin: 0 0 15px 0; font-weight: 600;">Booking Details</h2>
                <table width="100%" cellpadding="5" cellspacing="0">
                  <tr>
                    <td style="padding: 8px 0; color: #0ea5e9; font-weight: 600; width: 40%; font-size: 14px;">Booking Number:</td>
                    <td style="padding: 8px 0; color: #334155; font-size: 14px;"><?= esc($booking['booking_number']) ?></td>
                  </tr>
                  <tr>
                    <td style="padding: 8px 0; color: #0ea5e9; font-weight: 600; font-size: 14px;">Event:</td>
                    <td style="padding: 8px 0; color: #334155; font-size: 14px;"><?= esc($event['title']) ?></td>
                  </tr>
                  <tr>
                    <td style="padding: 8px 0; color: #0ea5e9; font-weight: 600; font-size: 14px;">Date:</td>
                    <td style="padding: 8px 0; color: #334155; font-size: 14px;"><?= date('F j, Y', strtotime($event['start_date'])) ?><?= $event['start_time'] ? ' at ' . date('g:i A', strtotime($event['start_time'])) : '' ?></td>
                  </tr>
                  <?php if (!empty($event['venue'])): ?>
                  <tr>
                    <td style="padding: 8px 0; color: #0ea5e9; font-weight: 600; font-size: 14px;">Venue:</td>
                    <td style="padding: 8px 0; color: #334155; font-size: 14px;"><?= esc($event['venue']) ?></td>
                  </tr>
                  <?php endif; ?>
                  <tr>
                    <td style="padding: 8px 0; color: #0ea5e9; font-weight: 600; font-size: 14px;">Total Amount:</td>
                    <td style="padding: 8px 0; color: #334155; font-size: 14px; font-weight: 600;">KES <?= number_format($booking['total_amount'], 2) ?></td>
                  </tr>
                  <tr>
                    <td style="padding: 8px 0; color: #0ea5e9; font-weight: 600; font-size: 14px;">Payment Status:</td>
                    <td style="padding: 8px 0; color: #334155; font-size: 14px;">
                      <span style="background: <?= $booking['payment_status'] === 'paid' ? '#10b981' : '#f59e0b' ?>; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                        <?= ucfirst($booking['payment_status']) ?>
                      </span>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

          <!-- Tickets Info -->
          <div style="background: #ffffff; border: 2px solid #e0f2fe; border-radius: 10px; padding: 20px; margin: 0 0 25px 0;">
            <h2 style="color: #0ea5e9; font-size: 18px; margin: 0 0 10px 0; font-weight: 600;">Your Tickets</h2>
            <p style="color: #475569; font-size: 15px; margin: 0; line-height: 1.6;">
              You have <strong style="color: #0ea5e9;"><?= count($tickets) ?></strong> ticket(s) for this event.
            </p>
          </div>

          <!-- Call to Action Button -->
          <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
            <tr>
              <td align="center">
                <a href="<?= esc($ticketDownloadUrl) ?>" 
                   style="display: inline-block; padding: 16px 45px; background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); 
                          color: #ffffff; text-decoration: none; border-radius: 50px; font-size: 16px; font-weight: 600; 
                          box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4); transition: all 0.3s ease;">
                  View & Download Tickets
                </a>
              </td>
            </tr>
          </table>

          <!-- Important Notice -->
          <div style="background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 8px; padding: 15px; margin: 25px 0;">
            <p style="color: #92400e; font-size: 14px; margin: 0; line-height: 1.6;">
              <strong>Important:</strong> Please bring your ticket(s) to the event. The QR code on each ticket will be scanned at the entrance.
            </p>
          </div>

          <p style="color: #475569; font-size: 15px; line-height: 1.6; margin: 25px 0 0 0;">
            If you have any questions or need assistance, please contact our support team at 
            <a href="mailto:support@kewasnet.co.ke" style="color: #0ea5e9; text-decoration: none; font-weight: 600;">support@kewasnet.co.ke</a>.
          </p>

          <p style="color: #475569; font-size: 15px; line-height: 1.6; margin: 20px 0;">
            We look forward to seeing you at the event!
          </p>

          <p style="color: #334155; font-size: 15px; margin: 25px 0 5px 0;">Best regards,</p>
          <p style="color: #0ea5e9; font-size: 16px; font-weight: 600; margin: 0;">KEWASNET Team</p>
        </td>
      </tr>
    </table>
  </td>
</tr>

<?= $this->include('backend/emails/inc/footer') ?>

