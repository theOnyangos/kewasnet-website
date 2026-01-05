<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Color\Color;

class TicketGenerator
{
    /**
     * Generate ticket PDF and return as string for download
     */
    public function generatePDFForDownload($ticket, $event, $booking)
    {
        // Get HTML content from ticket view
        $html = $this->renderTicketHTML($ticket, $event, $booking);
        
        // Configure DomPDF options
        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        $options->set('defaultFont', 'Helvetica');
        $options->set('dpi', 96);
        $options->set('chroot', [ROOTPATH . 'public', WRITEPATH]);
        
        // Create DomPDF instance
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Return PDF output as string
        return $dompdf->output();
    }

    /**
     * Generate QR code image as data URI for embedding in PDF
     */
    protected function generateQRCodeImage($qrCodeData)
    {
        try {
            $writer = new PngWriter();
            
            $qrCode = QrCode::create($qrCodeData)
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(ErrorCorrectionLevel::Medium)
                ->setSize(200)
                ->setMargin(10)
                ->setForegroundColor(new Color(0, 0, 0))
                ->setBackgroundColor(new Color(255, 255, 255));
            
            $result = $writer->write($qrCode);
            
            // Convert to base64 data URI for embedding in HTML/PDF
            $dataUri = $result->getDataUri();
            
            return $dataUri;
        } catch (\Exception $e) {
            log_message('error', 'TicketGenerator::generateQRCodeImage error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Render ticket HTML view
     */
    protected function renderTicketHTML($ticket, $event, $booking)
    {
        // Generate QR code data URI
        $qrCodeDataUri = $this->generateQRCodeImage($ticket['qr_code_data']);
        
        // Get base URL helper
        if (!function_exists('base_url')) {
            function base_url($path = '') {
                $request = \Config\Services::request();
                return $request->getServer('HTTP_HOST') 
                    ? ($request->getServer('REQUEST_SCHEME') . '://' . $request->getServer('HTTP_HOST') . '/' . ltrim($path, '/'))
                    : 'http://localhost/' . ltrim($path, '/');
            }
        }
        
        // Format dates
        $startDate = date('F j, Y', strtotime($event['start_date']));
        $startTime = $event['start_time'] ? date('g:i A', strtotime($event['start_time'])) : '';
        
        // Start output buffering
        ob_start();
        
        // Generate HTML
        ?>
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                        font-family: Helvetica, Arial, sans-serif;
                    }
                    body {
                        font-family: Helvetica, Arial, sans-serif;
                        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
                        padding: 20px;
                    }
                    .ticket-container {
                        background: white;
                        max-width: 600px;
                        margin: 0 auto;
                        padding: 0;
                        border: 3px solid #0ea5e9;
                        border-radius: 15px;
                        box-shadow: 0 10px 30px rgba(14, 165, 233, 0.2);
                        overflow: hidden;
                    }
                    .ticket-header {
                        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
                        color: white;
                        text-align: center;
                        padding: 30px 20px;
                        margin-bottom: 0;
                    }
                    .ticket-header .company-name {
                        font-size: 18px;
                        font-weight: bold;
                        margin-bottom: 10px;
                        letter-spacing: 1px;
                        text-transform: uppercase;
                        color: #000000;
                    }
                    .ticket-header h1 {
                        color: white;
                        font-size: 24px;
                        margin: 10px 0 5px 0;
                        font-weight: 600;
                        color: #000000;
                    }
                    .ticket-header .event-type {
                        color: rgba(220, 216, 216, 0.9);
                        font-size: 13px;
                        text-transform: uppercase;
                        letter-spacing: 2px;
                        font-weight: 300;
                        color: #000000;
                    }
                    .ticket-info {
                        margin: 30px;
                        margin-bottom: 20px;
                    }
                    .info-row {
                        display: table;
                        width: 100%;
                        margin-bottom: 18px;
                        padding-bottom: 12px;
                        border-bottom: 1px solid #e0f2fe;
                    }
                    .info-row:last-child {
                        border-bottom: none;
                    }
                    .info-label {
                        display: table-cell;
                        width: 35%;
                        font-weight: 600;
                        color: #000000;
                        vertical-align: top;
                        font-size: 14px;
                    }
                    .info-value {
                        display: table-cell;
                        color: #334155;
                        font-size: 14px;
                    }
                    .qr-section {
                        text-align: center;
                        margin: 30px;
                        padding: 25px;
                        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
                        border-radius: 10px;
                        border: 2px dashed #0ea5e9;
                    }
                    .qr-code {
                        margin: 15px auto;
                        padding: 15px;
                        background: white;
                        border-radius: 8px;
                        display: inline-block;
                    }
                    .qr-code img {
                        max-width: 180px;
                        height: auto;
                        display: block;
                    }
                    .ticket-number {
                        text-align: center;
                        font-size: 13px;
                        color: #0ea5e9;
                        margin-top: 15px;
                        font-weight: 600;
                        letter-spacing: 1px;
                    }
                    .ticket-footer {
                        background: #f8fafc;
                        margin-top: 0;
                        padding: 25px 30px;
                        border-top: 2px solid #e0f2fe;
                        text-align: center;
                        font-size: 12px;
                        color: #64748b;
                        line-height: 1.6;
                    }
                    .ticket-footer p {
                        margin: 5px 0;
                    }
                    .venue-info {
                        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
                        padding: 15px;
                        border-radius: 8px;
                        margin-top: 20px;
                        border-left: 4px solid #0ea5e9;
                    }
                </style>
            </head>
            <body>
                <div class="ticket-container">
                    <div class="ticket-header">
                        <div class="company-name">KEWASNET</div>
                        <h1><?= esc($event['title']) ?></h1>
                        <div class="event-type">Event Ticket</div>
                    </div>
                    
                    <div class="ticket-info">
                        <div class="info-row">
                            <div class="info-label">Attendee Name:</div>
                            <div class="info-value"><?= esc($ticket['attendee_name']) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Email:</div>
                            <div class="info-value"><?= esc($ticket['attendee_email']) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Date:</div>
                            <div class="info-value"><?= $startDate ?><?= $startTime ? ' at ' . $startTime : '' ?></div>
                        </div>
                        <?php if (!empty($event['venue'])): ?>
                        <div class="info-row">
                            <div class="info-label">Venue:</div>
                            <div class="info-value"><?= esc($event['venue']) ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($event['address'])): ?>
                        <div class="info-row">
                            <div class="info-label">Address:</div>
                            <div class="info-value"><?= esc($event['address']) ?><?= !empty($event['city']) ? ', ' . esc($event['city']) : '' ?><?= !empty($event['country']) ? ', ' . esc($event['country']) : '' ?></div>
                        </div>
                        <?php endif; ?>
                        <div class="info-row">
                            <div class="info-label">Booking Number:</div>
                            <div class="info-value"><?= esc($booking['booking_number']) ?></div>
                        </div>
                    </div>
                    
                    <?php if ($qrCodeDataUri): ?>
                    <div class="qr-section">
                        <div class="qr-code">
                            <img src="<?= $qrCodeDataUri ?>" alt="QR Code">
                        </div>
                        <div class="ticket-number">
                            Ticket #<?= esc($ticket['ticket_number']) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="ticket-footer">
                        <p>Please bring this ticket to the event. The QR code will be scanned at the entrance.</p>
                        <p style="margin-top: 10px;">This is a computer-generated ticket. No signature required.</p>
                    </div>
                </div>
            </body>
            </html>
        <?php
        
        // Get buffered content
        $html = ob_get_clean();
        
        return $html;
    }
}

