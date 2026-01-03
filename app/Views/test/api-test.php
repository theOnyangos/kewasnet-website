<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Test - KEWASNET Tracking</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-8">Activity Tracking API Test</h1>
                
                <div class="space-y-6">
                    <!-- Consent Controls -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Cookie Consent Simulation</h2>
                        <div class="flex space-x-4">
                            <button onclick="setConsent('all')" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                Accept All Cookies
                            </button>
                            <button onclick="setConsent('analytics')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                Accept Analytics Only
                            </button>
                            <button onclick="setConsent('necessary')" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                                Necessary Cookies Only
                            </button>
                            <button onclick="clearConsent()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                                Clear Consent
                            </button>
                        </div>
                        <div id="consentStatus" class="mt-2 text-sm text-gray-600"></div>
                    </div>

                    <!-- API Tests -->
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">API Endpoint Tests</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <button onclick="testInitSession()" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                                Test Init Session
                            </button>
                            <button onclick="testTrackPage()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                                Test Track Page
                            </button>
                            <button onclick="testTrackEvent()" class="bg-pink-600 text-white px-4 py-2 rounded hover:bg-pink-700">
                                Test Track Event
                            </button>
                            <button onclick="testUpdatePage()" class="bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700">
                                Test Update Page
                            </button>
                            <button onclick="testBatchTrack()" class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700">
                                Test Batch Track
                            </button>
                            <button onclick="testDashboard()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                                Test Dashboard (Admin)
                            </button>
                            <button onclick="testDebugRealTime()" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                                Test Real-time (Debug)
                            </button>
                        </div>
                    </div>

                    <!-- Results -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Test Results</h2>
                        <div id="testResults" class="space-y-2 text-sm font-mono">
                            <p class="text-gray-600">Click a test button to see results...</p>
                        </div>
                    </div>

                    <!-- Current Status -->
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Current Session Status</h2>
                        <div id="sessionStatus" class="text-sm">
                            <p><strong>Session ID:</strong> <span id="currentSessionId">Not initialized</span></p>
                            <p><strong>Page View ID:</strong> <span id="currentPageViewId">Not tracked</span></p>
                            <p><strong>Analytics Consent:</strong> <span id="analyticsConsent">Unknown</span></p>
                            <p><strong>Marketing Consent:</strong> <span id="marketingConsent">Unknown</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentSessionId = null;
        let currentPageViewId = null;

        // Cookie consent functions
        function setConsent(type) {
            switch(type) {
                case 'all':
                    setCookie('cookieConsent', 'all', 30);
                    setCookie('analyticsCookies', 'accepted', 30);
                    setCookie('marketingCookies', 'accepted', 30);
                    break;
                case 'analytics':
                    setCookie('cookieConsent', 'partial', 30);
                    setCookie('analyticsCookies', 'accepted', 30);
                    setCookie('marketingCookies', 'rejected', 30);
                    break;
                case 'necessary':
                    setCookie('cookieConsent', 'necessary', 30);
                    setCookie('analyticsCookies', 'rejected', 30);
                    setCookie('marketingCookies', 'rejected', 30);
                    break;
            }
            updateConsentStatus();
            logResult('SUCCESS', `Cookie consent set to: ${type}`);
        }

        function clearConsent() {
            deleteCookie('cookieConsent');
            deleteCookie('analyticsCookies');
            deleteCookie('marketingCookies');
            updateConsentStatus();
            logResult('INFO', 'Cookie consent cleared');
        }

        function setCookie(name, value, days) {
            const expires = new Date(Date.now() + days * 864e5).toUTCString();
            document.cookie = name + '=' + encodeURIComponent(value) + '; expires=' + expires + '; path=/';
        }

        function deleteCookie(name) {
            document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        }

        function getCookie(name) {
            return document.cookie.split('; ').reduce((r, v) => {
                const parts = v.split('=');
                return parts[0] === name ? decodeURIComponent(parts[1]) : r;
            }, '');
        }

        function updateConsentStatus() {
            const cookieConsent = getCookie('cookieConsent') || 'Not set';
            const analytics = getCookie('analyticsCookies') || 'Not set';
            const marketing = getCookie('marketingCookies') || 'Not set';
            
            document.getElementById('consentStatus').innerHTML = 
                `Consent: ${cookieConsent} | Analytics: ${analytics} | Marketing: ${marketing}`;
            
            document.getElementById('analyticsConsent').textContent = analytics;
            document.getElementById('marketingConsent').textContent = marketing;
        }

        // API test functions
        async function testInitSession() {
            try {
                const analyticsConsent = getCookie('analyticsCookies') === 'accepted';
                const marketingConsent = getCookie('marketingCookies') === 'accepted';

                const response = await fetch('/api/tracking/init-session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        analytics_consent: analyticsConsent,
                        marketing_consent: marketingConsent
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    currentSessionId = data.session_id;
                    document.getElementById('currentSessionId').textContent = currentSessionId || 'Initialized';
                    logResult('SUCCESS', `Session initialized: ${JSON.stringify(data)}`);
                } else {
                    logResult('ERROR', `Session init failed: ${data.message}`);
                }
            } catch (error) {
                logResult('ERROR', `Session init error: ${error.message}`);
            }
        }

        async function testTrackPage() {
            try {
                const response = await fetch('/api/tracking/track-page', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        page_url: '/api-test',
                        page_title: 'API Test Page',
                        page_category: 'Test'
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    currentPageViewId = data.page_view_id;
                    document.getElementById('currentPageViewId').textContent = currentPageViewId || 'Tracked';
                    logResult('SUCCESS', `Page tracked: ${JSON.stringify(data)}`);
                } else {
                    logResult('WARNING', `Page tracking: ${data.message}`);
                }
            } catch (error) {
                logResult('ERROR', `Page tracking error: ${error.message}`);
            }
        }

        async function testTrackEvent() {
            try {
                const response = await fetch('/api/tracking/track-event', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        event_type: 'click',
                        event_action: 'test_click',
                        event_label: 'API Test Button',
                        event_category: 'Test'
                    })
                });

                const data = await response.json();
                logResult(data.success ? 'SUCCESS' : 'WARNING', `Event tracking: ${JSON.stringify(data)}`);
            } catch (error) {
                logResult('ERROR', `Event tracking error: ${error.message}`);
            }
        }

        async function testUpdatePage() {
            try {
                const response = await fetch('/api/tracking/update-page', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        time_on_page: 45,
                        scroll_depth: 75,
                        is_exit: false
                    })
                });

                const data = await response.json();
                logResult(data.success ? 'SUCCESS' : 'WARNING', `Page update: ${JSON.stringify(data)}`);
            } catch (error) {
                logResult('ERROR', `Page update error: ${error.message}`);
            }
        }

        async function testBatchTrack() {
            try {
                const events = [
                    {
                        type: 'event',
                        event_type: 'click',
                        event_action: 'batch_test_1',
                        event_label: 'Batch Test 1'
                    },
                    {
                        type: 'event',
                        event_type: 'form_submit',
                        event_action: 'batch_test_2',
                        event_label: 'Batch Test 2'
                    }
                ];

                const response = await fetch('/api/tracking/batch-track', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ events: events })
                });

                const data = await response.json();
                logResult(data.success ? 'SUCCESS' : 'ERROR', `Batch tracking: ${JSON.stringify(data)}`);
            } catch (error) {
                logResult('ERROR', `Batch tracking error: ${error.message}`);
            }
        }

        async function testDashboard() {
            try {
                const response = await fetch('/api/tracking/admin/dashboard', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();
                
                if (response.status === 401) {
                    logResult('WARNING', 'Dashboard access requires admin authentication (401)');
                } else if (data.success) {
                    logResult('SUCCESS', `Dashboard data: ${JSON.stringify(data).substring(0, 200)}...`);
                } else {
                    logResult('ERROR', `Dashboard error: ${data.message}`);
                }
            } catch (error) {
                logResult('ERROR', `Dashboard error: ${error.message}`);
            }
        }

        async function testDebugRealTime() {
            try {
                const response = await fetch('/api/tracking/debug/real-time', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    logResult('SUCCESS', `Real-time debug data: ${JSON.stringify(data)}`);
                } else {
                    logResult('ERROR', `Real-time debug error: ${data.error}`);
                }
            } catch (error) {
                logResult('ERROR', `Real-time debug error: ${error.message}`);
            }
        }

        function logResult(type, message) {
            const resultsDiv = document.getElementById('testResults');
            const timestamp = new Date().toLocaleTimeString();
            const colorClass = {
                'SUCCESS': 'text-green-600',
                'WARNING': 'text-yellow-600',
                'ERROR': 'text-red-600',
                'INFO': 'text-blue-600'
            }[type];

            const resultElement = document.createElement('div');
            resultElement.className = `p-2 border-l-4 border-${type === 'SUCCESS' ? 'green' : type === 'WARNING' ? 'yellow' : type === 'ERROR' ? 'red' : 'blue'}-500 bg-white`;
            resultElement.innerHTML = `
                <div class="flex justify-between">
                    <span class="${colorClass} font-semibold">[${type}]</span>
                    <span class="text-gray-500 text-xs">${timestamp}</span>
                </div>
                <div class="text-gray-700 text-sm break-all">${message}</div>
            `;

            resultsDiv.insertBefore(resultElement, resultsDiv.firstChild);

            // Keep only last 10 results
            while (resultsDiv.children.length > 10) {
                resultsDiv.removeChild(resultsDiv.lastChild);
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateConsentStatus();
            logResult('INFO', 'API Test page loaded. Set cookie consent and test the endpoints.');
        });
    </script>
</body>
</html>
