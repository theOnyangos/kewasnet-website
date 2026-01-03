<?php $this->extend('backendV2/layouts/main'); ?>

<?php $this->section('title'); ?>
Sitemap Settings
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
    <main class="flex-1 overflow-y-auto p-6">
        <?= $this->include('backendV2/pages/settings/partials/settings_navigation'); ?>

        <!-- Sitemap Panel -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Sitemap Management</h2>
                    <p class="mt-1 text-sm text-gray-500">Generate and manage XML sitemaps for search engines</p>
                </div>
                <button onclick="generateSitemap()" class="gradient-btn text-white px-8 py-2 rounded-[50px] hover:bg-blue-700 transition-colors">
                    <i data-lucide="refresh-cw" class="w-4 h-4 mr-2 inline z-10"></i>
                    <span>Generate Sitemap</span>
                </button>
            </div>
            
            <!-- Sitemap Status -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i data-lucide="file-text" class="w-8 h-8 text-blue-600 mr-3"></i>
                        <div>
                            <h3 class="text-lg font-semibold text-blue-900">Sitemap Status</h3>
                            <p class="text-sm text-blue-700" id="sitemapStatus">Loading...</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i data-lucide="calendar" class="w-8 h-8 text-green-600 mr-3"></i>
                        <div>
                            <h3 class="text-lg font-semibold text-green-900">Last Generated</h3>
                            <p class="text-sm text-green-700" id="lastGenerated">Loading...</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i data-lucide="link" class="w-8 h-8 text-purple-600 mr-3"></i>
                        <div>
                            <h3 class="text-lg font-semibold text-purple-900">Active URLs</h3>
                            <p class="text-sm text-purple-700" id="totalUrls">Loading...</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i data-lucide="hard-drive" class="w-8 h-8 text-orange-600 mr-3"></i>
                        <div>
                            <h3 class="text-lg font-semibold text-orange-900">XML File</h3>
                            <p class="text-sm text-orange-700" id="xmlFileStatus">Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Detailed Statistics -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-8" id="detailedStats" style="display: none;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Detailed Statistics</h3>
                    <button onclick="toggleDetailedStats()" class="text-gray-500 hover:text-gray-700">
                        <i data-lucide="chevron-up" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="categoryStats">
                    <!-- Category statistics will be populated here -->
                </div>
                
                <div class="mt-4 p-4 bg-white rounded-lg border">
                    <h4 class="font-medium text-gray-900 mb-2">Summary</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Total URLs:</span>
                            <span class="font-medium text-gray-900" id="summaryTotal">-</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Active:</span>
                            <span class="font-medium text-green-600" id="summaryActive">-</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Inactive:</span>
                            <span class="font-medium text-red-600" id="summaryInactive">-</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Categories:</span>
                            <span class="font-medium text-blue-600" id="summaryCategories">-</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statistics Toggle Button -->
            <div class="text-center mb-6">
                <button onclick="toggleDetailedStats()" class="text-secondary hover:text-blue-800 font-medium text-sm flex items-center mx-auto gap-2" id="statsToggleBtn">
                    <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                    <span>Show Detailed Statistics</span>
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </button>
            </div>
            
            <!-- Sitemap Configuration -->
            <form id="sitemapForm" class="space-y-6">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center mb-3">
                        <i data-lucide="settings" class="w-5 h-5 text-yellow-600 mr-2"></i>
                        <h3 class="text-sm font-medium text-yellow-800">Sitemap Configuration</h3>
                    </div>
                    <p class="text-sm text-yellow-700">Configure what content to include in your XML sitemap</p>
                </div>
                
                <!-- Content Types -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-4">Include Content Types</h4>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="content_types[]" value="static" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Static Pages</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="content_types[]" value="blog" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Blog Posts</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="content_types[]" value="resources" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Resources</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="content_types[]" value="pillars" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">WASH Topics (Pillars)</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="content_types[]" value="programs" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Our Programs</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="content_types[]" value="events" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Events</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="content_types[]" value="jobs" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Job Opportunities</span>
                            </label>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-4">Frequency Settings</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Default Change Frequency</label>
                                <select name="changefreq_default" class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="always">Always</option>
                                    <option value="hourly">Hourly</option>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly" selected>Monthly</option>
                                    <option value="yearly">Yearly</option>
                                    <option value="never">Never</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Default Priority</label>
                                <select name="priority_default" class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="0.1">0.1 - Lowest</option>
                                    <option value="0.3">0.3 - Low</option>
                                    <option value="0.5" selected>0.5 - Medium</option>
                                    <option value="0.8">0.8 - High</option>
                                    <option value="1.0">1.0 - Highest</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Advanced Settings -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Advanced Options</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maximum URLs per Sitemap</label>
                            <select name="max_urls" class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1000">1,000 URLs</option>
                                <option value="5000">5,000 URLs</option>
                                <option value="10000" selected>10,000 URLs</option>
                                <option value="50000">50,000 URLs</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Auto-Generate</label>
                            <select name="auto_generate" class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="never">Never</option>
                                <option value="daily">Daily</option>
                                <option value="weekly" selected>Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-4 space-y-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="ping_google" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Ping Google when sitemap is updated</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" name="ping_bing" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Ping Bing when sitemap is updated</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" name="exclude_noindex" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Exclude pages with noindex meta tag</span>
                        </label>
                    </div>
                </div>
                
                <!-- Exclude Patterns -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Exclude Patterns</h4>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">URL Patterns to Exclude (one per line)</label>
                        <textarea name="exclude_patterns" rows="5" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="/admin&#10;/private&#10;/test-*&#10;*.pdf"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Enter URL patterns to exclude from the sitemap (supports wildcards)</p>
                    </div>
                </div>
                
                <!-- Save Configuration -->
                <div class="border-t border-gray-200 pt-6">
                    <button type="button" onclick="saveSitemapConfig()" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i data-lucide="save" class="w-4 h-4 mr-2 inline"></i>
                        Save Configuration
                    </button>
                </div>
            </form>
            
            <!-- Current Sitemap -->
            <div class="border-t border-gray-200 pt-6 mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Current Sitemap</h3>
                
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">XML Sitemap</h4>
                            <p class="text-sm text-gray-500" id="sitemapUrl"><?= base_url('sitemap.xml') ?></p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="<?= base_url('sitemap.xml') ?>" target="_blank" class="bg-secondary text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors">
                                <i data-lucide="external-link" class="w-4 h-4 mr-1 inline"></i>
                                View
                            </a>
                            <button onclick="downloadSitemap()" class="bg-gray-600 text-white px-3 py-1 rounded text-sm hover:bg-gray-700 transition-colors">
                                <i data-lucide="download" class="w-4 h-4 mr-1 inline"></i>
                                Download
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Submit to Search Engines -->
                <div class="mt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Submit to Search Engines</h4>
                    <div class="flex flex-wrap gap-4">
                        <button onclick="submitToGoogle()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                            <i data-lucide="search" class="w-4 h-4 mr-2 inline"></i>
                            Submit to Google
                        </button>
                        
                        <button onclick="submitToBing()" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="globe" class="w-4 h-4 mr-2 inline"></i>
                            Submit to Bing
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    // Generate sitemap
    function generateSitemap() {
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="w-4 h-4 mr-2 inline animate-spin" data-lucide="loader"></i> Generating...';
        button.disabled = true;
        
        $.ajax({
            url: '<?= base_url("auth/settings/generateSitemap") ?>',
            method: 'POST',
            data: new FormData(document.getElementById('sitemapForm')),
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showNotification('success', 'Sitemap generated successfully!');
                    loadSitemapStatus();
                } else {
                    showNotification('error', response.message || 'Failed to generate sitemap');
                }
            },
            error: function() {
                showNotification('error', 'An error occurred while generating sitemap');
            },
            complete: function() {
                button.innerHTML = originalText;
                button.disabled = false;
                lucide.createIcons();
            }
        });
    }
    
    // Save sitemap configuration
    function saveSitemapConfig() {
        const formData = new FormData(document.getElementById('sitemapForm'));
        
        $.ajax({
            url: '<?= base_url("auth/settings/saveSitemapConfig") ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showNotification('success', 'Sitemap configuration saved successfully!');
                } else {
                    showNotification('error', response.message || 'Failed to save configuration');
                }
            },
            error: function() {
                showNotification('error', 'An error occurred while saving configuration');
            }
        });
    }
    
    // Download sitemap
    function downloadSitemap() {
        window.open('<?= base_url("sitemap.xml") ?>', '_blank');
    }
    
    // Submit to search engines
    function submitToGoogle() {
        const sitemapUrl = encodeURIComponent('<?= base_url("sitemap.xml") ?>');
        window.open(`https://www.google.com/ping?sitemap=${sitemapUrl}`, '_blank');
        showNotification('success', 'Sitemap submitted to Google');
    }
    
    function submitToBing() {
        const sitemapUrl = encodeURIComponent('<?= base_url("sitemap.xml") ?>');
        window.open(`https://www.bing.com/ping?sitemap=${sitemapUrl}`, '_blank');
        showNotification('success', 'Sitemap submitted to Bing');
    }
    
    // Load sitemap status
    function loadSitemapStatus() {
        $.ajax({
            url: '<?= base_url("auth/settings/getSitemapStatus") ?>',
            method: 'GET',
            success: function(response) {
                if (response.status === 'success' && response.data) {
                    const data = response.data;
                    
                    // Update last generation info
                    if (data.last_generation && data.last_generation.generated_at) {
                        const lastGen = new Date(data.last_generation.generated_at);
                        document.getElementById('lastGenerated').textContent = lastGen.toLocaleString();
                    } else {
                        document.getElementById('lastGenerated').textContent = 'Never';
                    }
                    
                    // Update total URLs count
                    if (data.statistics) {
                        document.getElementById('totalUrls').textContent = data.statistics.active || '0';
                    } else {
                        document.getElementById('totalUrls').textContent = '0';
                    }
                    
                    // Update XML file status
                    let xmlStatusText = 'Not Found';
                    let xmlStatusColor = 'text-red-700';
                    
                    if (data.xml_exists) {
                        const fileSizeKB = Math.round(data.xml_size / 1024);
                        xmlStatusText = `Exists (${fileSizeKB} KB)`;
                        xmlStatusColor = 'text-green-700';
                    }
                    
                    const xmlStatusElement = document.getElementById('xmlFileStatus');
                    xmlStatusElement.textContent = xmlStatusText;
                    xmlStatusElement.className = `text-sm ${xmlStatusColor}`;
                    
                    // Update sitemap status based on XML file existence and data
                    let statusText = 'Not Generated';
                    let statusColor = 'text-red-700';
                    
                    if (data.xml_exists && data.statistics && data.statistics.active > 0) {
                        statusText = 'Generated & Active';
                        statusColor = 'text-green-700';
                    } else if (data.xml_exists) {
                        statusText = 'XML File Exists';
                        statusColor = 'text-yellow-700';
                    } else if (data.statistics && data.statistics.active > 0) {
                        statusText = 'Data Ready - XML Pending';
                        statusColor = 'text-blue-700';
                    }
                    
                    const statusElement = document.getElementById('sitemapStatus');
                    statusElement.textContent = statusText;
                    statusElement.className = `text-sm ${statusColor}`;
                    
                    // Update additional status info if available
                    updateSitemapStatistics(data.statistics);
                } else {
                    showNotification('error', response.message || 'Failed to load sitemap status');
                    // Set loading error state
                    document.getElementById('sitemapStatus').textContent = 'Error Loading';
                    document.getElementById('lastGenerated').textContent = 'Error Loading';
                    document.getElementById('totalUrls').textContent = 'Error Loading';
                    document.getElementById('xmlFileStatus').textContent = 'Error Loading';
                }
            },
            error: function() {
                showNotification('error', 'An error occurred while loading sitemap status');
                // Set error state
                document.getElementById('sitemapStatus').textContent = 'Connection Error';
                document.getElementById('lastGenerated').textContent = 'Connection Error';
                document.getElementById('totalUrls').textContent = 'Connection Error';
                document.getElementById('xmlFileStatus').textContent = 'Connection Error';
            }
        });
    }
    
    // Update additional statistics in the UI
    function updateSitemapStatistics(stats) {
        if (!stats) return;
        
        // Update summary statistics
        document.getElementById('summaryTotal').textContent = stats.total || '0';
        document.getElementById('summaryActive').textContent = stats.active || '0';
        document.getElementById('summaryInactive').textContent = stats.inactive || '0';
        document.getElementById('summaryCategories').textContent = stats.by_category ? stats.by_category.length : '0';
        
        // Update category statistics
        const categoryContainer = document.getElementById('categoryStats');
        if (categoryContainer && stats.by_category && Array.isArray(stats.by_category)) {
            categoryContainer.innerHTML = '';
            
            stats.by_category.forEach(category => {
                const categoryCard = document.createElement('div');
                categoryCard.className = 'bg-white p-4 rounded-lg border border-gray-200 shadow-sm';
                categoryCard.innerHTML = `
                    <div class="flex items-center justify-between">
                        <h5 class="font-medium text-gray-900 text-sm">${escapeHtml(category.category)}</h5>
                        <span class="text-lg font-bold text-blue-600">${category.count}</span>
                    </div>
                    <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-cyan-500 h-2 rounded-full" style="width: ${Math.min(100, (category.count / stats.active) * 100)}%"></div>
                    </div>
                    <div class="mt-1 text-xs text-gray-500">
                        ${Math.round((category.count / stats.active) * 100)}% of active URLs
                    </div>
                `;
                categoryContainer.appendChild(categoryCard);
            });
        }
        
        console.log('Sitemap statistics loaded:', stats);
    }
    
    // Toggle detailed statistics visibility
    function toggleDetailedStats() {
        const detailedStats = document.getElementById('detailedStats');
        const toggleBtn = document.getElementById('statsToggleBtn');
        const chevronIcon = toggleBtn.querySelector('[data-lucide="chevron-down"], [data-lucide="chevron-up"]');
        const spanText = toggleBtn.querySelector('span');
        
        if (detailedStats.style.display === 'none') {
            detailedStats.style.display = 'block';
            spanText.textContent = 'Hide Detailed Statistics';
            chevronIcon.setAttribute('data-lucide', 'chevron-up');
        } else {
            detailedStats.style.display = 'none';
            spanText.textContent = 'Show Detailed Statistics';
            chevronIcon.setAttribute('data-lucide', 'chevron-down');
        }
        
        // Refresh lucide icons
        lucide.createIcons();
    }
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    // Load existing configuration
    $(document).ready(function() {
        loadSitemapStatus();
        
        $.ajax({
            url: '<?= base_url("auth/settings/getSitemapConfig") ?>',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const form = document.getElementById('sitemapForm');
                    Object.keys(response.data).forEach(key => {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            if (input.type === 'checkbox') {
                                input.checked = response.data[key] == '1';
                            } else {
                                input.value = response.data[key] || '';
                            }
                        }
                    });
                }
            }
        });
    });
</script>
<?php $this->endSection(); ?>
