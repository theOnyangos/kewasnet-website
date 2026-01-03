<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

class GeneratePDF {
    
    public function generate($html, $filename, $paper = 'A4', $orientation = "portrait") {
        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $options->set('isHtml5ParserEnabled', TRUE);
        $options->set('isPhpEnabled', TRUE);
        $options->set('defaultFont', 'sans-serif');
        $options->set('dpi', 150);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();
        $dompdf->stream($filename, array("Attachment" => false));
    }
}