<?php
// Certificate PDF template - optimized for DomPDF compatibility
// DomPDF has limited CSS3 support, so we use simpler styles

if (!function_exists('base_url')) {
    function base_url($path = '') {
        $baseURL = \Config\Services::request()->getServer('HTTP_HOST') 
            ? (\Config\Services::request()->getServer('REQUEST_SCHEME') . '://' . \Config\Services::request()->getServer('HTTP_HOST') . '/')
            : 'http://localhost/';
        return rtrim($baseURL, '/') . '/' . ltrim($path, '/');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Completion - KEWASNET</title>

    <style>
        /* Base Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            width: 100%;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            background:rgb(255, 255, 255);
            position: relative;
        }
        
        /* Base Reset & Layout */
        .cert-error {
            text-align: center;
            color: #dc2626;
            margin-top: 2.5rem;
        }
        
        .cert-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: transparent;
            padding: 2rem 1rem;
            padding-top: 2.5rem;
            position: relative;
            z-index: 1;
        }
        
        .cert-inner {
            position: relative;
            width: 100%;
            max-width: 56rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        /* Certificate Container */
        .certificate-container {
            position: relative;
            background-color: white;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 210mm; /* A4 portrait width */
            margin: 0 auto;
            min-height: 297mm; /* A4 portrait height */
            /* border: 8px solid transparent; */
            overflow: hidden;
            background-image: linear-gradient(white, white), 
                            linear-gradient(135deg, #3b82f6, #8b5cf6, #06b6d4, #10b981);
            /* background-origin: border-box;
            background-clip: padding-box, border-box; */
        }
        
        /* Pattern Background */
        .certificate-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.5;
            z-index: 0;
            pointer-events: none;
            /* border-radius: 1.5rem; */
            background-image:
                repeating-linear-gradient(0deg, transparent, transparent 19px, rgba(34, 197, 94, 0.15) 19px, rgba(34, 197, 94, 0.15) 20px, transparent 20px, transparent 39px, rgba(34, 197, 94, 0.15) 39px, rgba(34, 197, 94, 0.15) 40px),
                repeating-linear-gradient(90deg, transparent, transparent 19px, rgba(34, 197, 94, 0.15) 19px, rgba(34, 197, 94, 0.15) 20px, transparent 20px, transparent 39px, rgba(34, 197, 94, 0.15) 39px, rgba(34, 197, 94, 0.15) 40px),
                radial-gradient(circle at 20px 20px, rgba(16, 185, 129, 0.18) 2px, transparent 2px),
                radial-gradient(circle at 40px 40px, rgba(16, 185, 129, 0.18) 2px, transparent 2px);
            background-size: 40px 40px, 40px 40px, 40px 40px, 40px 40px;
        }
        
        .certificate-container::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.03;
            z-index: 0;
            pointer-events: none;
            border-radius: 1.5rem;
            background: radial-gradient(ellipse at center, transparent 30%, rgba(139, 92, 246, 0.08) 100%);
        }
        
        .certificate-content {
            position: relative;
            z-index: 1;
            padding: 2rem 4rem;
            padding-top: 6rem;
            padding-bottom: 6rem;
        }
        
        @media (max-width: 768px) {
            .certificate-content {
                padding: 2rem;
                padding-top: 6rem;
                padding-bottom: 6rem;
            }
        }
        
        @keyframes wave {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .wave-animation {
            animation: wave 3s ease-in-out infinite;
        }
        
        .rotate-180 {
            transform: rotate(180deg);
        }
        
        /* Side Decorations */
        .side-decor-left {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 0.5rem;
            height: 16rem;
            background: linear-gradient(to bottom, #3b82f6, #8b5cf6, #06b6d4);
            border-radius: 0 9999px 9999px 0;
            opacity: 0.2;
        }
        
        .side-decor-right {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 0.5rem;
            height: 16rem;
            background: linear-gradient(to bottom, #06b6d4, #8b5cf6, #10b981);
            border-radius: 9999px 0 0 9999px;
            opacity: 0.2;
        }
        
        /* Logo Section */
        .logo-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 6rem;
        }
        
        .logo-container {
            background-color: white;
            border-radius: 9999px;
            padding: 0.5rem;
        }
        
        .logo-img {
            height: 5rem;
            padding: 0.75rem;
        }
        
        /* Title Section */
        .title-section {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .cert-title {
            font-size: 2.25rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: #000000;
        }
        
        @media (min-width: 768px) {
            .cert-title {
                font-size: 3rem;
            }
        }
        
        .title-divider {
            width: 8rem;
            height: 0.25rem;
            background: linear-gradient(to right, #3b82f6, #8b5cf6, #06b6d4);
            margin: 0 auto;
            border-radius: 9999px;
        }
        
        /* Main Content */
        .main-content {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .main-content > * + * {
            margin-top: 1.5rem;
        }
        
        .cert-text {
            font-size: 1.25rem;
            color: #334155;
            font-weight: 500;
        }
        
        @media (min-width: 768px) {
            .cert-text {
                font-size: 1.5rem;
            }
        }
        
        /* Name Box */
        .name-box {
            padding: 1rem 1.5rem;
            background: transparent;
            border-radius: 1rem;
            border: 2px solid #dbeafe;
            display: inline-block;
        }
        
        .name-text {
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #000000;
        }
        
        @media (min-width: 768px) {
            .name-text {
                font-size: 2.25rem;
            }
        }
        
        /* Course Box */
        .course-box {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(to right, #eef2ff, #faf5ff, #fdf2f8);
            border-radius: 0.75rem;
            border: 1px solid #e9d5ff;
            display: inline-block;
            max-width: 48rem;
        }
        
        .course-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #312e81;
            font-style: italic;
        }
        
        @media (min-width: 768px) {
            .course-title {
                font-size: 1.875rem;
            }
        }
        
        /* Date */
        .date-text {
            font-size: 1.125rem;
            color: #475569;
            margin-top: 1.5rem;
        }
        
        @media (min-width: 768px) {
            .date-text {
                font-size: 1.25rem;
            }
        }
        
        .font-medium {
            font-weight: 500;
        }
        
        .date-value {
            color: #2563eb;
            font-weight: 600;
        }
        
        /* Footer */
        .cert-footer {
            margin-top: 8rem;
            padding-top: 2rem;
            border-top: 2px dashed #e2e8f0;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        @media (min-width: 768px) {
            .footer-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        .footer-item {
            text-align: center;
        }
        
        @media (min-width: 768px) {
            .footer-item-left {
                text-align: left;
            }
            
            .footer-item-right {
                text-align: right;
            }
        }
        
        .footer-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .cert-number {
            font-family: monospace;
            font-size: 0.875rem;
            font-weight: 700;
            color: #1e293b;
            background-color: #f8fafc;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            display: inline-block;
        }
        
        @media (min-width: 768px) {
            .cert-number {
                font-size: 1rem;
            }
        }
        
        .issued-by {
            font-weight: 700;
            color: #1e293b;
            font-size: 0.875rem;
            line-height: 1.5;
        }
        
        @media (min-width: 768px) {
            .issued-by {
                font-size: 1rem;
            }
        }
        
        .kewasnet-text {
            color: #2563eb;
        }
        
        /* Decorative Seal */
        .decorative-seal {
            position: absolute;
            bottom: 2rem;
            right: 2rem;
            display: none;
        }
        
        @media (min-width: 768px) {
            .decorative-seal {
                display: block;
            }
        }
        
        .seal-circle {
            width: 6rem;
            height: 6rem;
            border-radius: 9999px;
            background: linear-gradient(to bottom right, #60a5fa, #a78bfa, #22d3ee);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            opacity: 0.2;
        }
        
        .seal-icon {
            width: 3rem;
            height: 3rem;
            color: white;
        }
        
        /* Download Button */
        .download-section {
            margin-top: 2rem;
            text-align: center;
        }
        
        .download-btn {
            background: linear-gradient(to right, #2563eb, #9333ea);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 9999px;
            font-weight: 600;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            transform: scale(1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            cursor: pointer;
        }
        
        .download-btn:hover {
            background: linear-gradient(to right, #1d4ed8, #7e22ce);
            transform: scale(1.05);
            color: white;
            text-decoration: none;
        }
        
        .download-icon {
            width: 1.25rem;
            height: 1.25rem;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <!-- Top Border -->
        <div class="border-top"></div>
        
        <!-- Bottom Border -->
        <div class="border-bottom"></div>
        
        <div class="certificate-content">
            <div class="content-inner">
                <!-- Logo Section -->
                <div class="logo-wrapper">
                    <div class="logo-container">
                        <?php 
                        $logoPath = ROOTPATH . 'public/assets/new/site-logo-transparent.png';
                        if (file_exists($logoPath)) {
                            $logoData = base64_encode(file_get_contents($logoPath));
                            $logoMime = 'image/png';
                            echo '<img src="data:' . $logoMime . ';base64,' . $logoData . '" alt="KEWASNET Logo" class="logo-img">';
                        }
                        ?>
                    </div>
                </div>

                <!-- Certificate Title -->
                <div class="title-section">
                    <h1 class="cert-title">Certificate of Completion</h1>
                    <div class="title-divider"></div>
                </div>

                <!-- Main Certificate Text -->
                <div class="main-content">
                    <p class="cert-text">This is to certify that</p>
                    
                    <div class="name-box">
                        <div class="name-text">
                            <?= htmlspecialchars(ucwords($user['first_name'] . ' ' . $user['last_name'])) ?>
                        </div>
                    </div>
                    
                    <p class="cert-text">has successfully completed the course</p>
                    
                    <div class="course-box">
                        <div class="course-title">
                            "<?= htmlspecialchars($course['title']) ?>"
                        </div>
                    </div>
                    
                    <p class="date-text">
                        <span>Completed on:</span> 
                        <span class="date-value"><?= date('F j, Y', strtotime($issuedAt)) ?></span>
                    </p>
                </div>

                <!-- Certificate Details Footer -->
                <div class="cert-footer">
                    <div class="footer-grid">
                        <!-- Certificate ID -->
                        <div class="footer-item footer-item-left">
                            <div class="footer-label">Certificate Number</div>
                            <div class="cert-number"><?= htmlspecialchars($certificateNumber) ?></div>
                        </div>
                        
                        <!-- Issued By -->
                        <div class="footer-item footer-item-right">
                            <div class="footer-label">Issued by</div>
                            <div class="issued-by">
                                Kenya Water &amp; Sanitation<br>Civil Society Network<br>
                                <span class="kewasnet-text">(KEWASNET)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
