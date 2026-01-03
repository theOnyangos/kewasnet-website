<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Tracking Test - KEWASNET</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Test Mode Cookie Setup -->
    <script>
        // Set test cookies for analytics consent
        function setCookie(name, value, days) {
            const expires = new Date();
            expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/';
        }
        
        // Enable analytics for testing
        setCookie('cookieConsent', 'all', 30);
        setCookie('analyticsCookies', 'accepted', 30);
        setCookie('marketingCookies', 'accepted', 30);
        console.log('Test cookies set for analytics tracking');
        
        // Debug console output
        console.log('Cookies set:', {
            cookieConsent: document.cookie.includes('cookieConsent=all'),
            analyticsCookies: document.cookie.includes('analyticsCookies=accepted'),
            marketingCookies: document.cookie.includes('marketingCookies=accepted')
        });
    </script>

    <div class="min-h-screen py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Test Controls -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <h2 class="text-lg font-semibold text-yellow-800 mb-2">🧪 Test Mode Active</h2>
                <p class="text-yellow-700 text-sm mb-3">Analytics cookies have been automatically set for testing purposes.</p>
                <div class="flex space-x-2">
                    <button onclick="clearTestCookies()" class="bg-yellow-600 text-white px-3 py-1 rounded text-sm hover:bg-yellow-700">
                        Clear Test Cookies
                    </button>
                    <button onclick="location.reload()" class="bg-gray-600 text-white px-3 py-1 rounded text-sm hover:bg-gray-700">
                        Refresh Page
                    </button>
                </div>
            </div>
            
            <div class="bg-white shadow rounded-lg p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-8">Activity Tracking Test Page</h1>
                
                <div class="space-y-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Automatic Tracking</h2>
                        <p class="text-gray-600 mb-4">The following elements are automatically tracked:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-3">
                                <button class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                    Click Me - Button
                                </button>
                                <a href="#" class="block bg-green-600 text-white px-4 py-2 rounded text-center hover:bg-green-700">
                                    Click Me - Link
                                </a>
                                <a href="https://example.com" target="_blank" class="block bg-purple-600 text-white px-4 py-2 rounded text-center hover:bg-purple-700">
                                    External Link
                                </a>
                            </div>
                            <div class="space-y-3">
                                <a href="tel:+254700000000" class="block bg-yellow-600 text-white px-4 py-2 rounded text-center hover:bg-yellow-700">
                                    Call Us
                                </a>
                                <a href="mailto:test@kewasnet.org" class="block bg-red-600 text-white px-4 py-2 rounded text-center hover:bg-red-700">
                                    Email Us
                                </a>
                                <a href="/sample.pdf" download class="block bg-indigo-600 text-white px-4 py-2 rounded text-center hover:bg-indigo-700">
                                    Download PDF
                                </a>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Form Tracking</h2>
                        <form class="space-y-4" onsubmit="return false;">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                            </div>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                                Submit Form
                            </button>
                        </form>
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Search Tracking</h2>
                        <form role="search" class="flex space-x-2" onsubmit="return false;">
                            <input type="search" name="query" placeholder="Search..." class="flex-1 border border-gray-300 rounded-md px-3 py-2">
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                Search
                            </button>
                        </form>
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Manual Tracking</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <button onclick="trackCustomEvent('manual_test', 'Manual Test Event')" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                                Track Custom Event
                            </button>
                            <button onclick="trackUserRegistration('Test Registration')" class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700">
                                Track Registration
                            </button>
                            <button onclick="trackNewsletterTest()" class="bg-pink-600 text-white px-4 py-2 rounded hover:bg-pink-700">
                                Track Newsletter
                            </button>
                            <button onclick="testDirectAPI()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                                Test API Directly
                            </button>
                            <button onclick="forceInitTracker()" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                                Force Init Tracker
                            </button>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Social Media Links</h2>
                        <div class="flex space-x-4">
                            <a href="https://facebook.com/kewasnet" target="_blank" class="text-blue-600 hover:text-blue-800">Facebook</a>
                            <a href="https://twitter.com/kewasnet" target="_blank" class="text-blue-400 hover:text-blue-600">Twitter</a>
                            <a href="https://linkedin.com/company/kewasnet" target="_blank" class="text-blue-700 hover:text-blue-900">LinkedIn</a>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Scroll Tracking</h2>
                        <p class="text-gray-600 mb-4">Scroll down to test scroll depth tracking:</p>
                        <div class="h-96 bg-gradient-to-b from-blue-100 to-blue-300 rounded-lg flex items-center justify-center">
                            <p class="text-blue-800 font-semibold">Scroll through this area to track scroll depth</p>
                        </div>
                        <div class="h-96 bg-gradient-to-b from-green-100 to-green-300 rounded-lg flex items-center justify-center mt-4">
                            <p class="text-green-800 font-semibold">More content to scroll through</p>
                        </div>
                        <div class="h-96 bg-gradient-to-b from-purple-100 to-purple-300 rounded-lg flex items-center justify-center mt-4">
                            <p class="text-purple-800 font-semibold">Even more content for scroll tracking</p>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Tracking Status</h2>
                        <div id="trackingStatus" class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-600">Loading tracking status...</p>
                        </div>
                        
                        <!-- Debug Output -->
                        <div id="debugOutput" class="mt-4 p-4 bg-blue-50 rounded-lg">
                            <h3 class="font-semibold text-blue-800 mb-2">Debug Information</h3>
                            <div id="debugContent" class="text-xs text-blue-700 font-mono"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include the tracking snippet -->
        <!-- Include the tracking snippet -->
    <?= view('tracking/tracking-snippet') ?>

    <!-- Debug tracker initialization -->
    <script>
        // Override console.error to capture any errors
        const originalConsoleError = console.error;
        const trackerErrors = [];
        console.error = function(...args) {
            trackerErrors.push(args.join(' '));
            originalConsoleError.apply(console, args);
        };

        // Function to update debug display
        function updateDebugDisplay() {
            const debugContent = document.getElementById('debugContent');
            
            const debugInfo = {
                'KewasnetTracker class': typeof window.KewasnetTracker,
                'kewasnetTracker instance': typeof window.kewasnetTracker,
                'Tracker errors': trackerErrors.length > 0 ? trackerErrors.join(', ') : 'None',
                'Current URL': window.location.href,
                'Document ready state': document.readyState,
                'Navigator online': navigator.onLine
            };
            
            if (window.kewasnetTracker) {
                debugInfo['Tracker initialized'] = window.kewasnetTracker.isInitialized;
                debugInfo['Has analytics consent'] = window.kewasnetTracker.hasAnalyticsConsent();
                debugInfo['Session ID'] = window.kewasnetTracker.sessionId || 'None';
                debugInfo['Base URL'] = window.kewasnetTracker.baseUrl;
                debugInfo['API Endpoint'] = window.kewasnetTracker.apiEndpoint;
            }
            
            let html = '';
            for (const [key, value] of Object.entries(debugInfo)) {
                html += `<div><strong>${key}:</strong> ${value}</div>`;
            }
            
            debugContent.innerHTML = html;
        }

        // Check tracker status multiple times
        setTimeout(updateDebugDisplay, 500);
        setTimeout(updateDebugDisplay, 1500);
        setTimeout(updateDebugDisplay, 3000);
    </script>

    <script>
        // Custom functions for manual tracking tests
        function trackCustomEvent(action, label) {
            if (window.trackCustomEvent) {
                window.trackCustomEvent(action, label, null, 'Test');
                alert('Custom event tracked: ' + action);
            } else {
                alert('Tracking not available');
            }
        }

        function trackNewsletterTest() {
            if (window.kewasnetTracker) {
                window.kewasnetTracker.trackNewsletterSignup();
                alert('Newsletter signup tracked');
            } else {
                alert('Tracking not available');
            }
        }

        // Display tracking status
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const statusDiv = document.getElementById('trackingStatus');
                const hasConsent = window.kewasnetTracker && window.kewasnetTracker.hasAnalyticsConsent();
                const isInitialized = window.kewasnetTracker && window.kewasnetTracker.isInitialized;
                const trackerExists = !!window.kewasnetTracker;
                
                console.log('Tracker Debug:', {
                    trackerExists,
                    hasConsent,
                    isInitialized,
                    sessionId: window.kewasnetTracker?.sessionId,
                    cookies: document.cookie
                });
                
                let status = '';
                if (hasConsent && isInitialized) {
                    status = '<span class="text-green-600">✓ Tracking is active and working</span>';
                } else if (!trackerExists) {
                    status = '<span class="text-red-600">✗ Tracking library not loaded</span>';
                } else if (!hasConsent) {
                    status = '<span class="text-yellow-600">⚠ Analytics consent not given - tracking disabled</span>';
                } else {
                    status = '<span class="text-red-600">✗ Tracking initialization failed</span>';
                }
                
                statusDiv.innerHTML = `
                    <p class="font-semibold">${status}</p>
                    <p class="text-sm text-gray-600 mt-2">
                        Tracker Loaded: ${trackerExists ? 'Yes' : 'No'} | 
                        Consent: ${hasConsent ? 'Given' : 'Not given'} | 
                        Initialized: ${isInitialized ? 'Yes' : 'No'} | 
                        Session ID: ${window.kewasnetTracker?.sessionId || 'None'}
                    </p>
                    <div class="mt-3 text-xs text-gray-500">
                        <p><strong>Cookies:</strong> ${document.cookie || 'None'}</p>
                        <p><strong>Tracker Class:</strong> ${typeof window.KewasnetTracker}</p>
                    </div>
                `;
            }, 2000);
        });

        // Function to clear test cookies
        function clearTestCookies() {
            document.cookie = 'cookieConsent=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            document.cookie = 'analyticsCookies=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            document.cookie = 'marketingCookies=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            console.log('Test cookies cleared');
            alert('Test cookies cleared. Refresh the page to see the effect.');
        }

        // Test API directly
        async function testDirectAPI() {
            try {
                const response = await fetch('/api/tracking/init-session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        analytics_consent: true,
                        marketing_consent: true
                    })
                });
                
                const data = await response.json();
                alert('Direct API test result: ' + JSON.stringify(data));
                updateDebugDisplay();
            } catch (error) {
                alert('Direct API test failed: ' + error.message);
            }
        }

        // Force initialize tracker
        async function forceInitTracker() {
            try {
                if (window.KewasnetTracker) {
                    window.kewasnetTracker = new window.KewasnetTracker();
                    alert('Tracker force initialized');
                    updateDebugDisplay();
                } else {
                    alert('KewasnetTracker class not available');
                }
            } catch (error) {
                alert('Force init failed: ' + error.message);
            }
        }
    </script>
</body>
</html>
