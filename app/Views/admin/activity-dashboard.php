<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Tracking Dashboard - KEWASNET Admin</title>
    <!-- Custom CSS Files (contains .data-table styles) -->
    <link rel="stylesheet" href="<?= base_url("assets/css/styles.css") ?>">
    <link rel="stylesheet" href="<?= base_url("assets/css/new.css") ?>">
    <link rel="stylesheet" href="<?= base_url("assets/css/dashboard.css") ?>">
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <!-- jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <style>
        /* CSS Variables for consistent theming */
        :root {
            --color-primary-500: #3b82f6;
            --color-primary-600: #2563eb;
            --color-primary-700: #1d4ed8;
            --color-secondary: #10b981;
            --color-slate-50: #f8fafc;
            --color-slate-100: #f1f5f9;
            --color-slate-200: #e2e8f0;
            --color-slate-500: #64748b;
            --color-slate-700: #334155;
            --color-slate-800: #1e293b;
            --color-primary-10: rgba(59, 130, 246, 0.1);
            --color-primary-30: rgba(59, 130, 246, 0.3);
        }
        
        .metric-card {
            transition: transform 0.2s;
        }
        .metric-card:hover {
            transform: translateY(-2px);
        }
        
        /* Data-table styles - ensure they override default DataTables styles */
        .data-table {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }
        
        .data-table thead th {
            background-color: var(--color-primary-500) !important;
            color: var(--color-slate-50) !important;
            padding: 12px 15px !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            font-size: 0.75rem !important;
            border: none !important;
            text-align: left !important;
        }
        
        .data-table thead th:hover {
            background-color: var(--color-primary-600) !important;
        }
        
        .data-table tbody tr {
            transition: background-color 0.2s ease !important;
        }
        
        .data-table tbody tr:nth-child(even) {
            background-color: var(--color-primary-10) !important;
        }
        
        .data-table tbody tr:hover {
            background-color: var(--color-primary-30) !important;
        }
        
        .data-table tbody td {
            padding: 12px 15px !important;
            border-top: 1px solid var(--color-slate-200) !important;
        }
        
        /* DataTables wrapper styling */
        .dataTables_wrapper {
            margin-top: 1rem;
        }
        
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }
        
        /* Button styling */
        .dt-buttons .dt-button {
            background: linear-gradient(to bottom, var(--color-secondary) 0%, var(--color-primary-500) 100%) !important;
            color: var(--color-slate-50) !important;
            border: none !important;
            border-radius: 6px !important;
            padding: 8px 16px !important;
            margin-right: 8px !important;
            transition: background-color 0.2s ease !important;
        }
        
        .dt-buttons .dt-button:hover {
            background: linear-gradient(to bottom, var(--color-primary-500) 0%, var(--color-secondary) 100%) !important;
        }
        
        /* Pagination styling */
        .dataTables_paginate .paginate_button {
            padding: 6px 12px !important;
            margin: 0 4px !important;
            border-radius: 6px !important;
            border: 1px solid var(--color-slate-200) !important;
        }
        
        .dataTables_paginate .paginate_button.current {
            background: var(--color-primary-500) !important;
            color: var(--color-slate-50) !important;
            border-color: var(--color-primary-500) !important;
        }
        
        .dataTables_paginate .paginate_button:hover {
            background: var(--color-primary-500) !important;
            color: var(--color-slate-50) !important;
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
                    <!-- Page Views Over Time (Line Chart) -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Page Views Over Time</h3>
                        <canvas id="pageViewsChart" width="400" height="200"></canvas>
                    </div>

                    <!-- Page Views Over Time (Bar Chart) -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Page Views Over Time (Bar)</h3>
                        <canvas id="pageViewsBarChart" width="400" height="200"></canvas>
                    </div>

                    <!-- Device Types (Pie Chart) -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Device Types</h3>
                        <canvas id="deviceChart" width="400" height="200"></canvas>
                    </div>

                    <!-- Event Types (Pie Chart) -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Event Types Distribution</h3>
                        <canvas id="eventTypesChart" width="400" height="200"></canvas>
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
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Label</th>
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

                <!-- Events by Category -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Events by Category</h3>
                    </div>
                    <div class="p-6">
                        <div id="eventsByCategory" class="space-y-2">
                            <!-- Data will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Form Submissions & Downloads -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Form Submissions -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Form Submissions</h3>
                        </div>
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Form Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submissions</th>
                                    </tr>
                                </thead>
                                <tbody id="formSubmissionsTable" class="bg-white divide-y divide-gray-200">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Downloads -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Downloads</h3>
                        </div>
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Downloads</th>
                                    </tr>
                                </thead>
                                <tbody id="downloadsTable" class="bg-white divide-y divide-gray-200">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Search Queries -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Top Search Queries</h3>
                    </div>
                    <div class="p-6">
                        <div id="searchQueries" class="space-y-2">
                            <!-- Data will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Real-time Activity -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-slate-800">Real-time Activity</h2>
                        <p class="mt-1 text-sm text-slate-500">View and monitor all recent user activities and events</p>
                    </div>
                    <table id="realTimeActivityTable" class="data-table stripe hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded by DataTables -->
                        </tbody>
                    </table>
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
                this.charts = {
                    pageViews: null,
                    pageViewsBar: null,
                    devices: null,
                    eventTypes: null
                };
                this.realTimeInterval = null;
                this.activitiesTable = null;
                this.init();
            }

            async init() {
                // Setup event listeners
                document.getElementById('refreshBtn').addEventListener('click', () => this.loadDashboard());
                document.getElementById('timeRange').addEventListener('change', () => this.loadDashboard());

                // Load initial data
                await this.loadDashboard();

                // Initialize activities data table
                this.initActivitiesTable();

                // Start real-time updates (for stats only, table auto-refreshes)
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
                        this.updateEventDetails(data.data);
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

            destroyAllCharts() {
                Object.keys(this.charts).forEach(key => {
                    if (this.charts[key]) {
                        try {
                            this.charts[key].destroy();
                        } catch (e) {
                            console.warn('Error destroying chart:', key, e);
                        }
                        this.charts[key] = null;
                    }
                });
            }

            updateCharts(data) {
                // Check if Chart.js is loaded
                if (typeof Chart === 'undefined') {
                    console.error('Chart.js is not loaded!');
                    return;
                }
                
                // Destroy existing charts first
                this.destroyAllCharts();
                
                console.log('Updating charts with data:', data);
                console.log('Chart.js available:', typeof Chart !== 'undefined');
                
                // Page Views Line Chart
                const pageViewsCtx = document.getElementById('pageViewsChart');
                if (!pageViewsCtx) {
                    console.error('Page Views chart canvas element not found!');
                } else {
                    try {
                        const timelineData = data.page_views_timeline || { labels: [], data: [] };
                        console.log('Page Views Timeline Data:', timelineData);
                        
                        // If no data, show empty chart with message
                        if (!timelineData.labels || timelineData.labels.length === 0) {
                            timelineData.labels = ['No Data'];
                            timelineData.data = [0];
                        }
                        
                        this.charts.pageViews = new Chart(pageViewsCtx.getContext('2d'), {
                            type: 'line',
                            data: {
                                labels: timelineData.labels || [],
                                datasets: [{
                                    label: 'Page Views',
                                    data: timelineData.data || [],
                                    borderColor: 'rgb(59, 130, 246)',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top'
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1
                                        }
                                    }
                                }
                            }
                        });
                        console.log('Page Views chart created successfully');
                    } catch (error) {
                        console.error('Error creating Page Views chart:', error);
                    }
                }

                // Page Views Bar Chart
                const pageViewsBarCtx = document.getElementById('pageViewsBarChart');
                if (!pageViewsBarCtx) {
                    console.error('Page Views Bar chart canvas not found');
                } else {
                    try {
                        const timelineData = data.page_views_timeline || { labels: [], data: [] };
                        
                        // If no data, show empty chart with message
                        if (!timelineData.labels || timelineData.labels.length === 0) {
                            timelineData.labels = ['No Data'];
                            timelineData.data = [0];
                        }
                        
                        this.charts.pageViewsBar = new Chart(pageViewsBarCtx.getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: timelineData.labels || [],
                                datasets: [{
                                    label: 'Page Views',
                                    data: timelineData.data || [],
                                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                                    borderColor: 'rgb(59, 130, 246)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top'
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1
                                        }
                                    }
                                }
                            }
                        });
                        console.log('Page Views Bar chart created successfully');
                    } catch (error) {
                        console.error('Error creating Page Views Bar chart:', error);
                    }
                }

                // Device Types Pie Chart
                const deviceCtx = document.getElementById('deviceChart');
                if (!deviceCtx) {
                    console.error('Device chart canvas element not found!');
                } else {
                    try {
                        const deviceData = data.device_stats || { labels: [], data: [] };
                        console.log('Device Stats Data:', deviceData);
                        console.log('Device Stats Labels:', deviceData.labels);
                        console.log('Device Stats Data Values:', deviceData.data);
                        
                        // Ensure we have valid data structure
                        if (!deviceData.labels || !Array.isArray(deviceData.labels) || 
                            !deviceData.data || !Array.isArray(deviceData.data)) {
                            console.warn('Invalid device data structure, using defaults');
                            deviceData.labels = ['Desktop', 'Mobile', 'Tablet'];
                            deviceData.data = [0, 0, 0];
                        }
                        
                        // Check if we have valid data (at least one non-zero value)
                        const hasData = deviceData.labels.length > 0 && 
                                       deviceData.data.length > 0 &&
                                       deviceData.data.some(v => v > 0);
                        
                        if (!hasData) {
                            console.warn('No device data found (all zeros), showing empty chart');
                            // Keep the structure but show it's empty
                            // Don't replace with "No Data Available" - show the actual categories with 0
                        }
                        
                        const deviceColors = this.generateColors(deviceData.labels.length || 0);
                        
                        this.charts.devices = new Chart(deviceCtx.getContext('2d'), {
                            type: 'pie',
                            data: {
                                labels: deviceData.labels || [],
                                datasets: [{
                                    data: deviceData.data || [],
                                    backgroundColor: deviceColors,
                                    borderWidth: 2,
                                    borderColor: '#fff'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'right'
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                const label = context.label || '';
                                                const value = context.parsed || 0;
                                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                                return `${label}: ${value} (${percentage}%)`;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                        console.log('Device chart created successfully');
                    } catch (error) {
                        console.error('Error creating Device chart:', error);
                    }
                }

                // Event Types Pie Chart
                const eventTypesCtx = document.getElementById('eventTypesChart');
                if (eventTypesCtx) {
                    try {
                        const eventData = data.event_stats_chart || { labels: [], data: [] };
                        console.log('Event Stats Chart Data:', eventData);
                        
                        // If no data, show empty chart with message
                        if (!eventData.labels || eventData.labels.length === 0) {
                            eventData.labels = ['No Data'];
                            eventData.data = [0];
                        }
                        
                        const eventColors = this.generateColors(eventData.labels?.length || 0);
                        
                        this.charts.eventTypes = new Chart(eventTypesCtx.getContext('2d'), {
                            type: 'pie',
                            data: {
                                labels: eventData.labels || [],
                                datasets: [{
                                    data: eventData.data || [],
                                    backgroundColor: eventColors,
                                    borderWidth: 2,
                                    borderColor: '#fff'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'right'
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                const label = context.label || '';
                                                const value = context.parsed || 0;
                                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                                return `${label}: ${value} (${percentage}%)`;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                        console.log('Event Types chart created successfully');
                    } catch (error) {
                        console.error('Error creating Event Types chart:', error);
                    }
                } else {
                    console.error('Event Types chart canvas not found');
                }
            }

            generateColors(count) {
                const baseColors = [
                    'rgb(59, 130, 246)',   // Blue
                    'rgb(16, 185, 129)',   // Green
                    'rgb(245, 158, 11)',    // Yellow
                    'rgb(239, 68, 68)',    // Red
                    'rgb(139, 92, 246)',   // Purple
                    'rgb(236, 72, 153)',   // Pink
                    'rgb(14, 165, 233)',   // Cyan
                    'rgb(251, 146, 60)',   // Orange
                    'rgb(34, 197, 94)',    // Emerald
                    'rgb(168, 85, 247)'    // Violet
                ];
                
                const colors = [];
                for (let i = 0; i < count; i++) {
                    colors.push(baseColors[i % baseColors.length]);
                }
                return colors;
            }

            updateTables(data) {
                // Popular Pages
                const popularPagesTable = document.getElementById('popularPagesTable');
                popularPagesTable.innerHTML = '';

                if (data.popular_pages && data.popular_pages.length > 0) {
                    data.popular_pages.forEach(page => {
                        const row = document.createElement('tr');
                        const viewCount = page.views || page.view_count || 0;
                        const avgTime = page.avg_time || page.avg_time_on_page || 0;
                        const minutes = Math.floor(avgTime / 60);
                        const seconds = Math.floor(avgTime % 60);
                        
                        row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${page.page_url || 'N/A'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${viewCount.toLocaleString()}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${minutes}m ${seconds}s</td>
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
                        const eventCount = event.event_count || event.count || 0;
                        const eventAction = event.event_action || 'N/A';
                        const eventType = event.event_type || 'N/A';
                        const eventCategory = event.event_category || 'N/A';
                        const eventLabel = event.event_label || 'N/A';
                        
                        row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${eventAction}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    ${eventType}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${eventCategory}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="${eventLabel}">${eventLabel}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${eventCount.toLocaleString()}</td>
                        `;
                        topEventsTable.appendChild(row);
                    });
                } else {
                    topEventsTable.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No events tracked yet</td></tr>';
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
                        // Extract activities from the response
                        const activities = data.data?.activities || data.data || [];
                        this.updateRealTimeActivity(activities);
                    }
                } catch (error) {
                    console.error('Real-time update failed:', error);
                }
            }

            updateEventDetails(data) {
                // Events by Category
                const eventsByCategory = document.getElementById('eventsByCategory');
                eventsByCategory.innerHTML = '';

                if (data.events_by_category && data.events_by_category.length > 0) {
                    data.events_by_category.forEach(category => {
                        const item = document.createElement('div');
                        item.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';
                        const count = category.count || 0;
                        item.innerHTML = `
                            <span class="text-sm font-medium text-gray-900">${category.event_category || 'Uncategorized'}</span>
                            <span class="text-sm text-gray-500">${count.toLocaleString()} events</span>
                        `;
                        eventsByCategory.appendChild(item);
                    });
                } else {
                    eventsByCategory.innerHTML = '<p class="text-gray-500 text-center py-4">No category data available</p>';
                }

                // Form Submissions
                const formSubmissionsTable = document.getElementById('formSubmissionsTable');
                formSubmissionsTable.innerHTML = '';

                if (data.form_submissions && data.form_submissions.length > 0) {
                    data.form_submissions.forEach(form => {
                        const row = document.createElement('tr');
                        const submissions = form.submissions || 0;
                        row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${form.form_name || 'Unknown Form'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${submissions.toLocaleString()}</td>
                        `;
                        formSubmissionsTable.appendChild(row);
                    });
                } else {
                    formSubmissionsTable.innerHTML = '<tr><td colspan="2" class="px-6 py-4 text-center text-gray-500">No form submissions</td></tr>';
                }

                // Downloads
                const downloadsTable = document.getElementById('downloadsTable');
                downloadsTable.innerHTML = '';

                if (data.downloads && data.downloads.length > 0) {
                    data.downloads.forEach(download => {
                        const row = document.createElement('tr');
                        const downloadCount = download.downloads || 0;
                        const fileName = download.file_name || 'Unknown File';
                        row.innerHTML = `
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 max-w-xs truncate" title="${fileName}">${fileName}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${downloadCount.toLocaleString()}</td>
                        `;
                        downloadsTable.appendChild(row);
                    });
                } else {
                    downloadsTable.innerHTML = '<tr><td colspan="2" class="px-6 py-4 text-center text-gray-500">No downloads</td></tr>';
                }

                // Search Queries
                const searchQueries = document.getElementById('searchQueries');
                searchQueries.innerHTML = '';

                if (data.search_queries && data.search_queries.length > 0) {
                    data.search_queries.forEach(query => {
                        const item = document.createElement('div');
                        item.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';
                        const searches = query.searches || 0;
                        item.innerHTML = `
                            <span class="text-sm font-medium text-gray-900">"${query.search_query || 'Unknown'}"</span>
                            <span class="text-sm text-gray-500">${searches.toLocaleString()} searches</span>
                        `;
                        searchQueries.appendChild(item);
                    });
                } else {
                    searchQueries.innerHTML = '<p class="text-gray-500 text-center py-4">No search queries</p>';
                }
            }

            initActivitiesTable() {
                if (this.activitiesTable) {
                    this.activitiesTable.destroy();
                }

                this.activitiesTable = $('#realTimeActivityTable').DataTable({
                    processing: true,
                    serverSide: true,
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search activities...",
                        processing: "<div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Loading Activities...</span></div>"
                    },
                    ajax: {
                        url: '/api/tracking/admin/activities-datatable',
                        type: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        dataSrc: function(json) {
                            // Debug the response
                            console.log("Activities DataTable response:", json);
                            if (json.error) {
                                console.error("Server error:", json.error);
                                return [];
                            }
                            return json.data || [];
                        },
                        error: (xhr, error, thrown) => {
                            console.error('DataTable error:', error, thrown);
                            if (xhr.status === 401) {
                                this.showError('Access denied. Please refresh the page.');
                            } else {
                                this.showError('Failed to load activities data');
                            }
                        }
                    },
                    columns: [
                        {
                            data: 'type',
                            render: (data, type, row) => {
                                // If type is missing, derive from category
                                let eventType = data;
                                if (!eventType || eventType === 'N/A' || eventType === '') {
                                    const category = (row.category || '').toLowerCase();
                                    if (category.includes('media') || category.includes('social')) {
                                        eventType = 'media';
                                    } else if (category.includes('form')) {
                                        eventType = 'form_submit';
                                    } else if (category.includes('ai') || category.includes('assistant')) {
                                        eventType = 'ai_chat';
                                    } else {
                                        eventType = 'custom';
                                    }
                                }
                                
                                const badgeColors = {
                                    'page_view': 'bg-blue-100 text-blue-800',
                                    'click': 'bg-green-100 text-green-800',
                                    'form_submit': 'bg-purple-100 text-purple-800',
                                    'download': 'bg-yellow-100 text-yellow-800',
                                    'search': 'bg-indigo-100 text-indigo-800',
                                    'custom': 'bg-gray-100 text-gray-800',
                                    'ai_chat': 'bg-pink-100 text-pink-800',
                                    'newsletter': 'bg-cyan-100 text-cyan-800',
                                    'contact': 'bg-orange-100 text-orange-800',
                                    'media': 'bg-teal-100 text-teal-800',
                                    'social_click': 'bg-teal-100 text-teal-800',
                                    'video_play': 'bg-teal-100 text-teal-800'
                                };
                                const color = badgeColors[eventType] || 'bg-gray-100 text-gray-800';
                                const displayName = eventType.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                                return `<span class="px-2 py-1 text-xs font-semibold rounded-full ${color}">${displayName}</span>`;
                            }
                        },
                        {
                            data: 'description',
                            render: (data, type, row) => {
                                const url = row.url && row.url !== 'N/A' && row.url !== '/' ? 
                                    `<a href="${row.url}" target="_blank" class="text-primary hover:text-secondary transition-colors">${data}</a>` : 
                                    `<span class="text-sm font-medium text-dark">${data}</span>`;
                                return url;
                            }
                        },
                        {
                            data: 'category',
                            render: (data) => {
                                return $('<span>').addClass('text-sm font-medium text-dark').text(data || 'N/A')[0].outerHTML;
                            }
                        },
                        {
                            data: 'created_at',
                            render: (data) => {
                                const timeAgo = this.getTimeAgo(new Date(data));
                                return $('<span>').addClass('text-sm font-medium text-gray-500').text(timeAgo)[0].outerHTML;
                            }
                        }
                    ],
                    order: [[3, 'desc']], // Sort by time (created_at) descending
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                    dom: '<"flex flex-col md:flex-row md:justify-between md:items-center gap-4"<"flex flex-col"l><"flex flex-col"f>><"my-2"B>rt<"flex flex-col md:flex-row md:justify-between md:items-center mt-2 gap-4"ip>',
                    buttons: [
                        {
                            extend: 'copy',
                            className: 'bg-gradient-to-r from-primary to-secondary text-white px-4 py-2 rounded'
                        },
                        {
                            extend: 'csv',
                            className: 'bg-gradient-to-r from-primary to-secondary text-white px-4 py-2 rounded'
                        },
                        {
                            extend: 'excel',
                            className: 'bg-gradient-to-r from-primary to-secondary text-white px-4 py-2 rounded'
                        },
                        {
                            extend: 'pdf',
                            className: 'bg-gradient-to-r from-primary to-secondary text-white px-4 py-2 rounded'
                        }
                    ],
                    responsive: true,
                    initComplete: function() {
                        // Optional: Add any initialization logic here
                    },
                    drawCallback: function() {
                        // Optional: Add any post-render logic here
                    }
                });

                // Auto-refresh the table every 30 seconds
                setInterval(() => {
                    if (this.activitiesTable) {
                        this.activitiesTable.ajax.reload(null, false); // false = don't reset paging
                    }
                }, 30000);
            }

            updateRealTimeActivity(activities) {
                // This method is kept for backward compatibility but is no longer used
                // The DataTable handles updates automatically
                if (this.activitiesTable) {
                    this.activitiesTable.ajax.reload(null, false);
                }
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
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for Chart.js to be fully loaded
            if (typeof Chart !== 'undefined') {
                new ActivityDashboard();
            } else {
                console.error('Chart.js not loaded, retrying...');
                setTimeout(function() {
                    if (typeof Chart !== 'undefined') {
                        new ActivityDashboard();
                    } else {
                        console.error('Chart.js failed to load. Please check your internet connection.');
                        alert('Chart.js library failed to load. Please refresh the page.');
                    }
                }, 1000);
            }
        });
    </script>
</body>
</html>
