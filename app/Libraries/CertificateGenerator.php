<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

class CertificateGenerator
{
    /**
     * Generate certificate PDF from HTML
     */
    public function generatePDF($user, $course, $certificateNumber, $verificationCode, $issuedAt = null)
    {
        // Get HTML content from certificate view
        $html = $this->renderCertificateHTML($user, $course, $certificateNumber, $verificationCode, $issuedAt);
        
        // Configure DomPDF options
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        $options->set('defaultFont', 'Arial');
        $options->set('dpi', 150);
        $options->set('chroot', [ROOTPATH . 'public']);
        
        // Create DomPDF instance
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        // Get PDF output
        $output = $dompdf->output();
        
        // Create certificates directory if it doesn't exist
        $certificatesDir = WRITEPATH . 'certificates/';
        if (!is_dir($certificatesDir)) {
            mkdir($certificatesDir, 0755, true);
        }

        $fileName = 'cert_' . $certificateNumber . '.pdf';
        $filePath = $certificatesDir . $fileName;
        
        // Save PDF file
        file_put_contents($filePath, $output);

        return base_url('writable/certificates/' . $fileName);
    }
    
    /**
     * Generate PDF and return as string for download
     */
    public function generatePDFForDownload($user, $course, $certificateNumber, $verificationCode, $issuedAt = null)
    {
        // Get HTML content from certificate view
        $html = $this->renderCertificateHTML($user, $course, $certificateNumber, $verificationCode, $issuedAt);
        
        // Configure DomPDF options
        $options = new Options();
        $options->set('isRemoteEnabled', false); // Disable remote for better performance
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        $options->set('defaultFont', 'Arial');
        $options->set('dpi', 96); // Lower DPI for better compatibility
        $options->set('chroot', [ROOTPATH . 'public']);
        
        // Create DomPDF instance
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Return PDF output as string
        return $dompdf->output();
    }
    
    /**
     * Render certificate HTML view
     */
    protected function renderCertificateHTML($user, $course, $certificateNumber, $verificationCode, $issuedAt = null)
    {
        $issuedAt = $issuedAt ?? date('Y-m-d H:i:s');
        
        // Get base URL helper
        if (!function_exists('base_url')) {
            function base_url($path = '') {
                return \Config\Services::request()->getServer('HTTP_HOST') 
                    ? (\Config\Services::request()->getServer('REQUEST_SCHEME') . '://' . \Config\Services::request()->getServer('HTTP_HOST') . '/' . ltrim($path, '/'))
                    : 'http://localhost/' . ltrim($path, '/');
            }
        }
        
        // Start output buffering
        ob_start();
        
        // Include certificate view (we'll need to create a version that can be included)
        include APPPATH . 'Views/frontendV2/ksp/pages/learning-hub/learning/certificate-pdf.php';
        
        // Get buffered content
        $html = ob_get_clean();
        
        return $html;
    }

    /**
     * Generate QR code for certificate verification
     */
    public function generateQRCode($verificationCode)
    {
        // TODO: Implement QR code generation
        // Use a library like endroid/qr-code
        // Return QR code image path or data URI
        
        return null; // Placeholder
    }
}

