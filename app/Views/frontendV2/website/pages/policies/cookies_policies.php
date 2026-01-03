<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KEWASNET - Cookie Policy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        :root {
            --primary: #364391;
            --secondary: #27aae0;
            --dark: #3e405b;
            --light: #f4f4f4;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7ff 0%, #e6f3fa 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #334155;
        }
        
        .logo {
            color: var(--primary);
            font-weight: 700;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .cookie-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            max-width: 800px;
            width: 100%;
        }
        
        .cookie-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 20px 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .cookie-body {
            padding: 30px;
        }
        
        .cookie-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--primary);
        }
        
        .cookie-text {
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .cookie-types {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 25px 0;
        }
        
        .cookie-type {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            transition: all 0.3s ease;
        }
        
        .cookie-type:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .cookie-type-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
            margin-left: auto;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: var(--secondary);
        }
        
        input:checked + .slider:before {
            transform: translateX(24px);
        }
        
        .cookie-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: #2a3574;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: white;
            color: var(--primary);
            border: 1px solid #cbd5e1;
        }
        
        .btn-secondary:hover {
            background: #f8fafc;
            transform: translateY(-2px);
        }
        
        .cookie-consent-modal {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
            padding: 20px;
            z-index: 1000;
            transform: translateY(100%);
        }
        
        .consent-content {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .consent-text {
            flex: 1;
        }
        
        .consent-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--primary);
        }
        
        .consent-buttons {
            display: flex;
            gap: 10px;
        }
        
        .consent-btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 0.9rem;
        }
        
        .preferences-btn {
            background: white;
            color: var(--primary);
            border: 1px solid #cbd5e1;
        }
        
        .accept-all-btn {
            background: var(--primary);
            color: white;
        }
        
        @media (max-width: 768px) {
            .cookie-types {
                grid-template-columns: 1fr;
            }
            
            .consent-content {
                flex-direction: column;
                text-align: center;
            }
            
            .consent-buttons {
                width: 100%;
                justify-content: center;
            }
            
            .cookie-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="logo mb-8">
        <i class="fas fa-droplet text-3xl"></i>
        <span>KEWASNET WASH</span>
    </div>
    
    <div class="cookie-container">
        <div class="cookie-header">
            <i class="fas fa-cookie-bite text-2xl"></i>
            <h1>Cookie Policy</h1>
        </div>
        
        <div class="cookie-body">
            <h2 class="cookie-title">Our Use of Cookies</h2>
            <p class="cookie-text">
                At KEWASNET, we use cookies and similar technologies to enhance your experience on our website, 
                analyze site usage, and assist in our marketing efforts. By continuing to browse this site, you 
                agree to our use of cookies as described in this policy.
            </p>
            
            <h2 class="cookie-title">Cookie Preferences</h2>
            <p class="cookie-text">
                You can customize your cookie preferences below. Note that blocking some types of cookies may 
                impact your experience of the site and the services we are able to offer.
            </p>
            
            <div class="cookie-types">
                <div class="cookie-type">
                    <div class="flex items-center justify-between">
                        <h3 class="cookie-type-title">
                            <i class="fas fa-shield-alt"></i>
                            Necessary Cookies
                        </h3>
                        <label class="toggle-switch">
                            <input type="checkbox" checked disabled>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <p>Essential for the website to function properly. Cannot be disabled.</p>
                </div>
                
                <div class="cookie-type">
                    <div class="flex items-center justify-between">
                        <h3 class="cookie-type-title">
                            <i class="fas fa-chart-line"></i>
                            Analytics Cookies
                        </h3>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <p>Allow us to analyze site usage to improve performance and user experience.</p>
                </div>
                
                <div class="cookie-type">
                    <div class="flex items-center justify-between">
                        <h3 class="cookie-type-title">
                            <i class="fas fa-bullhorn"></i>
                            Marketing Cookies
                        </h3>
                        <label class="toggle-switch">
                            <input type="checkbox">
                            <span class="slider"></span>
                        </label>
                    </div>
                    <p>Used to track visitors across websites for marketing purposes.</p>
                </div>
            </div>
            
            <div class="cookie-actions">
                <button class="btn btn-secondary">Save Preferences</button>
                <button class="btn btn-primary">Accept All Cookies</button>
            </div>
        </div>
    </div>
    
    <div class="cookie-consent-modal" id="cookieConsent">
        <div class="consent-content">
            <div class="consent-text">
                <h3 class="consent-title">We value your privacy</h3>
                <p>KEWASNET uses cookies to enhance your browsing experience, analyze site traffic, and personalize content. By clicking "Accept All", you consent to our use of cookies.</p>
            </div>
            <div class="consent-buttons">
                <button class="consent-btn preferences-btn" id="preferencesBtn">Preferences</button>
                <button class="consent-btn accept-all-btn" id="acceptAllBtn">Accept All</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the cookie consent modal
            const cookieConsent = document.getElementById('cookieConsent');
            
            // Check if user has already made a choice
            if (!getCookie('cookieConsent')) {
                // Animate the cookie consent modal from the bottom
                gsap.to(cookieConsent, {
                    duration: 0.8,
                    y: 0,
                    ease: "power3.out",
                    delay: 0.5
                });
            }
            
            // Accept All button
            document.getElementById('acceptAllBtn').addEventListener('click', function() {
                setCookie('cookieConsent', 'all', 365);
                gsap.to(cookieConsent, {
                    duration: 0.5,
                    y: "100%",
                    ease: "power2.in"
                });
            });
            
            // Preferences button
            document.getElementById('preferencesBtn').addEventListener('click', function() {
                // Scroll to the cookie preferences section
                document.querySelector('.cookie-container').scrollIntoView({
                    behavior: 'smooth'
                });
                
                // Close the banner
                gsap.to(cookieConsent, {
                    duration: 0.5,
                    y: "100%",
                    ease: "power2.in"
                });
            });
            
            // Save preferences button
            document.querySelector('.btn-secondary').addEventListener('click', function() {
                // Get the state of each toggle
                const analyticsChecked = document.querySelector('.cookie-types .toggle-switch:nth-child(2) input').checked;
                const marketingChecked = document.querySelector('.cookie-types .toggle-switch:nth-child(3) input').checked;
                
                // Save preferences
                setCookie('cookieConsent', 'custom', 365);
                setCookie('analyticsCookies', analyticsChecked ? 'accepted' : 'rejected', 365);
                setCookie('marketingCookies', marketingChecked ? 'accepted' : 'rejected', 365);
                
                // Show confirmation
                alert('Your cookie preferences have been saved successfully.');
            });
            
            // Accept all from the main section
            document.querySelector('.btn-primary').addEventListener('click', function() {
                setCookie('cookieConsent', 'all', 365);
                alert('All cookies have been accepted.');
            });
            
            // Cookie functions
            function setCookie(name, value, days) {
                const d = new Date();
                d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
                const expires = "expires=" + d.toUTCString();
                document.cookie = name + "=" + value + ";" + expires + ";path=/";
            }
            
            function getCookie(name) {
                const cookieName = name + "=";
                const decodedCookie = decodeURIComponent(document.cookie);
                const cookieArray = decodedCookie.split(';');
                
                for(let i = 0; i < cookieArray.length; i++) {
                    let cookie = cookieArray[i];
                    while (cookie.charAt(0) == ' ') {
                        cookie = cookie.substring(1);
                    }
                    if (cookie.indexOf(cookieName) == 0) {
                        return cookie.substring(cookieName.length, cookie.length);
                    }
                }
                return "";
            }
        });
    </script>
</body>
</html>