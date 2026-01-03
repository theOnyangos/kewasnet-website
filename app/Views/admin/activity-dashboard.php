<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Tracking Dashboard - KEWASNET Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .metric-card {
            transition: transform 0.2s;
        }
        .metric-card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-gray-900">Activity Tracking Dashboard</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                            <span class="text-sm text-gray-600">Live</span>
                        </div>
                        <select id="timeRange" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                        </select>
                        <button id="refreshBtn" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                            Refresh
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Loading State -->
            <div id="loadingState" class="flex items-center justify-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                <span class="ml-3 text-gray-600">Loading dashboard data...</span>
            </div>

            <!-- Dashboard Content -->
            <div id="dashboardContent" class="hidden space-y-8">
                <!-- Overview Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="metric-card bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Page Views</dt>
                                        <dd class="text-lg font-medium text-gray-900" id="totalPageViews">-</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="metric-card bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Active Sessions</dt>
                                        <dd class="text-lg font-medium text-gray-900" id="activeSessions">-</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="metric-card bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Events</dt>
                                        <dd class="text-lg font-medium text-gray-900" id="totalEvents">-</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="metric-card bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.736 6.979C9.208 6.193 9.696 6 10 6s.792.193 1.264.979a1 1 0 001.715-1.029C12.279 4.784 11.232 4 10 4s-2.279.784-2.979 1.95c-.285.475-.507 1-.67 1.55H6a1 1 0 000 2h.013a9.358 9.358 0 000 1H6a1 1 0 100 2h.351c.163.55.385 1.075.67 1.55C7.721 15.216 8.768 16 10 16s2.279-.784 2.979-1.95a1 1 0 10-1.715-1.029C10.792 13.807 10.304 14 10 14s-.792-.193-1.264-.979a4.265 4.265 0 01-.264-.521H9a1 1 0 110-2h-.013a9.358 9.358 0 010-1H9a1 1 0 010-2h-.472a4.265 4.265 0 01.208-.521z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Avg. Session Duration</dt>
                                        <dd class="text-lg font-medium text-gray-900" id="avgSessionDuration">-</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Page Views Chart -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Page Views Over Time</h3>
                        <canvas id="pageViewsChart" width="400" height="200"></canvas>
                    </div>

                    <!-- Device Types Chart -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Device Types</h3>
                        <canvas id="deviceChart" width="400" height="200"></canvas>
                    </div>
                </div>

                <!-- Tables Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Popular Pages -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Popular Pages</h3>
                        </div>
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Page</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Time</th>
                                    </tr>
                                </thead>
                                <tbody id="popularPagesTable" class="bg-white divide-y divide-gray-200">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Top Events -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Top Events</h3>
                        </div>
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                                    </tr>
                                </thead>
                                <tbody id="topEventsTable" class="bg-white divide-y divide-gray-200">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Real-time Activity -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Real-time Activity</h3>
                    </div>
                    <div class="p-6">
                        <div id="realTimeActivity" class="space-y-3">
                            <!-- Real-time data will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error State -->
            <div id="errorState" class="hidden">
                <div class="bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Error Loading Dashboard</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p id="errorMessage">Unable to load dashboard data. Please try refreshing the page.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        class ActivityDashboard {
            constructor() {
                this.charts = {};
                this.realTimeInterval = null;
                this.init();
            }

            async init() {
                // Setup event listeners
                document.getElementById('refreshBtn').addEventListener('click', () => this.loadDashboard());
                document.getElementById('timeRange').addEventListener('change', () => this.loadDashboard());

                // Load initial data
                await this.loadDashboard();

                // Start real-time updates
                this.startRealTimeUpdates();
            }

            async loadDashboard() {
                this.showLoading();

                try {
                    const timeRange = document.getElementById('timeRange').value;
                    const response = await fetch(`/api/tracking/admin/dashboard?range=${timeRange}`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.success) {
                        this.updateMetrics(data.data);
                        this.updateCharts(data.data);
                        this.updateTables(data.data);
                        this.showDashboard();
                    } else {
                        throw new Error(data.message || 'Failed to load dashboard data');
                    }
                } catch (error) {
                    console.error('Dashboard loading error:', error);
                    this.showError(error.message);
                }
            }

            updateMetrics(data) {
                document.getElementById('totalPageViews').textContent = data.overview?.total_page_views?.toLocaleString() || '0';
                document.getElementById('activeSessions').textContent = data.overview?.active_sessions?.toLocaleString() || '0';
                document.getElementById('totalEvents').textContent = data.overview?.total_events?.toLocaleString() || '0';
                
                const avgDuration = data.overview?.avg_session_duration || 0;
                const minutes = Math.floor(avgDuration / 60);
                const seconds = avgDuration % 60;
                document.getElementById('avgSessionDuration').textContent = `${minutes}m ${seconds}s`;
            }

            updateCharts(data) {
                // Page Views Chart
                if (this.charts.pageViews) {
                    this.charts.pageViews.destroy();
                }

                const pageViewsCtx = document.getElementById('pageViewsChart').getContext('2d');
                this.charts.pageViews = new Chart(pageViewsCtx, {
                    type: 'line',
                    data: {
                        labels: data.page_views_timeline?.labels || [],
                        datasets: [{
                            label: 'Page Views',
                            data: data.page_views_timeline?.data || [],
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Device Chart
                if (this.charts.devices) {
                    this.charts.devices.destroy();
                }

                const deviceCtx = document.getElementById('deviceChart').getContext('2d');
                this.charts.devices = new Chart(deviceCtx, {
                    type: 'doughnut',
                    data: {
                        labels: data.device_stats?.labels || [],
                        datasets: [{
                            data: data.device_stats?.data || [],
                            backgroundColor: [
                                'rgb(59, 130, 246)',
                                'rgb(16, 185, 129)',
                                'rgb(245, 158, 11)',
                                'rgb(239, 68, 68)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true
                    }
                });
            }

            updateTables(data) {
                // Popular Pages
                const popularPagesTable = document.getElementById('popularPagesTable');
                popularPagesTable.innerHTML = '';

                if (data.popular_pages && data.popular_pages.length > 0) {
                    data.popular_pages.forEach(page => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${page.page_url}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${page.view_count.toLocaleString()}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${Math.floor(page.avg_time_on_page / 60)}m ${page.avg_time_on_page % 60}s</td>
                        `;
                        popularPagesTable.appendChild(row);
                    });
                } else {
                    popularPagesTable.innerHTML = '<tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">No data available</td></tr>';
                }

                // Top Events
                const topEventsTable = document.getElementById('topEventsTable');
                topEventsTable.innerHTML = '';

                if (data.top_events && data.top_events.length > 0) {
                    data.top_events.forEach(event => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${event.event_action}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${event.event_type}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${event.event_count.toLocaleString()}</td>
                        `;
                        topEventsTable.appendChild(row);
                    });
                } else {
                    topEventsTable.innerHTML = '<tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">No events tracked yet</td></tr>';
                }
            }

            async loadRealTimeActivity() {
                try {
                    const response = await fetch('/api/tracking/admin/real-time', {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        this.updateRealTimeActivity(data.data || []);
                    }
                } catch (error) {
                    console.error('Real-time update failed:', error);
                }
            }

            updateRealTimeActivity(activities) {
                const container = document.getElementById('realTimeActivity');
                container.innerHTML = '';

                if (activities.length === 0) {
                    container.innerHTML = '<p class="text-gray-500 text-center">No recent activity</p>';
                    return;
                }

                activities.slice(0, 10).forEach(activity => {
                    const item = document.createElement('div');
                    item.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';
                    
                    const timeAgo = this.getTimeAgo(new Date(activity.created_at));
                    
                    item.innerHTML = `
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                            <span class="text-sm text-gray-900">${activity.description}</span>
                        </div>
                        <span class="text-xs text-gray-500">${timeAgo}</span>
                    `;
                    container.appendChild(item);
                });
            }

            getTimeAgo(date) {
                const now = new Date();
                const diffMs = now - date;
                const diffSeconds = Math.floor(diffMs / 1000);
                const diffMinutes = Math.floor(diffSeconds / 60);
                const diffHours = Math.floor(diffMinutes / 60);

                if (diffSeconds < 60) return 'Just now';
                if (diffMinutes < 60) return `${diffMinutes}m ago`;
                if (diffHours < 24) return `${diffHours}h ago`;
                return date.toLocaleDateString();
            }

            startRealTimeUpdates() {
                this.loadRealTimeActivity();
                this.realTimeInterval = setInterval(() => {
                    this.loadRealTimeActivity();
                }, 30000); // Update every 30 seconds
            }

            showLoading() {
                document.getElementById('loadingState').classList.remove('hidden');
                document.getElementById('dashboardContent').classList.add('hidden');
                document.getElementById('errorState').classList.add('hidden');
            }

            showDashboard() {
                document.getElementById('loadingState').classList.add('hidden');
                document.getElementById('dashboardContent').classList.remove('hidden');
                document.getElementById('errorState').classList.add('hidden');
            }

            showError(message) {
                document.getElementById('errorMessage').textContent = message;
                document.getElementById('loadingState').classList.add('hidden');
                document.getElementById('dashboardContent').classList.add('hidden');
                document.getElementById('errorState').classList.remove('hidden');
            }
        }

        // Initialize dashboard when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new ActivityDashboard();
        });
    </script>
</body>
</html>
