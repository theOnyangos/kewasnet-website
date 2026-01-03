<?php

namespace App\Libraries;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;

class GenerateQrCode 
{
    public static function generate($data, $size = 300, $margin = 10, $foregroundColor = [0, 0, 0], $backgroundColor = [255, 255, 255], $logoPath = null, $logoResizeWidth = 50, $includeLabel = false, $labelText = 'Label', $labelTextColor = [255, 0, 0])
    {
        $writer = new PngWriter();
        $logo = null;
        $label = null;

        // Create QR code
        $qrCode = QrCode::create($data)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setSize($size)
            ->setMargin($margin)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(...$foregroundColor))
            ->setBackgroundColor(new Color(...$backgroundColor));

        // Add logo if provided
        if ($logoPath) {
            $logo = Logo::create($logoPath)
                ->setResizeToWidth($logoResizeWidth)
                ->setPunchoutBackground(true);
        }

        // Add label if specified
        if ($includeLabel) {
            $label = Label::create($labelText)
                ->setTextColor(new Color(...$labelTextColor));
        }

        // Write QR code
        $result = $writer->write($qrCode, $logo, $label);

        // Validate the result
        $writer->validateResult($result, $data);

        return $result;
    }

    // This method generates a QR code without logo and label
    public static function generateSimple($data, $size = 300, $margin = 10, $foregroundColor = [0, 0, 0], $backgroundColor = [255, 255, 255])
    {
        $writer = new PngWriter();

        // Create QR code
        $qrCode = QrCode::create($data)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->setSize($size)
            ->setMargin($margin)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(...$foregroundColor))
            ->setBackgroundColor(new Color(...$backgroundColor));

        // Write QR code
        $result = $writer->write($qrCode);

        // Validate the result
        $writer->validateResult($result, $data);

        return $result;
    }
}
