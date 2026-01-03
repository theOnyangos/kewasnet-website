<!-- File: app/Views/sitemap.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/sitemap.css') ?>">
</head>
<body class="py-8 px-4">
    <div class="max-w-[80%] mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 flex items-center justify-center gap-3">
                <i class="fas fa-sitemap text-blue-500"></i> KEWASNET Sitemap
            </h1>
            <p class="text-gray-600 mt-2">Explore all pages on our website</p>
        </div>

        <div class="sitemap-container p-6 mb-8">
            <div class="sitemap-header text-white py-4 px-6 -mx-6 -mt-6 mb-6 rounded-t-lg">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold">Website Structure</h2>
                        <p class="opacity-90">All pages are organized by category</p>
                        <?php if (isset($statistics)): ?>
                        <div class="flex gap-4 mt-2 text-sm opacity-90">
                            <span><i class="fas fa-globe"></i> Total URLs: <?= $statistics['total'] ?></span>
                            <span><i class="fas fa-check-circle"></i> Active: <?= $statistics['active'] ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Layout Toggle -->
                    <div class="mt-4 sm:mt-0">
                        <div class="flex items-center gap-2 text-sm">
                            <span class="opacity-90">Layout:</span>
                            <button onclick="toggleLayout('masonry')" id="masonry-btn" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-3 py-1 rounded transition-colors">
                                <i class="fas fa-th-large"></i> Masonry
                            </button>
                            <button onclick="toggleLayout('grid')" id="grid-btn" class="bg-white bg-opacity-10 hover:bg-opacity-20 px-3 py-1 rounded transition-colors">
                                <i class="fas fa-th"></i> Grid
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flexible layout container -->
            <div id="sitemap-container" class="sitemap-masonry">
                <?php foreach ($categories as $categoryName => $categoryUrls): ?>
                    <?php if (!empty($categoryUrls)): ?>
                    <div class="category-card p-5">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-folder text-blue-500"></i> 
                            <?= $categoryName ?> 
                            <span class="text-sm text-gray-500">(<?= count($categoryUrls) ?>)</span>
                        </h3>
                        <ul class="space-y-2">
                            <?php foreach ($categoryUrls as $urlData): ?>
                                <?php 
                                // Ensure URL is properly formatted
                                $cleanUrl = trim($urlData->url ?? '');
                                $fullUrl = empty($cleanUrl) ? base_url() : base_url($cleanUrl);
                                ?>
                                <li class="url-item pl-3 py-2">
                                    <a href="<?= $fullUrl ?>" class="text-gray-700 hover:text-blue-600 transition-colors flex items-center gap-2">
                                        <i class="fas fa-link text-gray-400 text-xs"></i> 
                                        <?= esc($urlData->title ?: ($urlData->url ?: 'Home')) ?>
                                    </a>
                                    <?php if (!empty($urlData->description)): ?>
                                    <p class="text-xs text-gray-500 mt-1 pl-4"><?= esc($urlData->description) ?></p>
                                    <?php endif; ?>
                                    <div class="text-xs text-gray-500 mt-1 flex items-center gap-2 pl-4">
                                        <span><i class="fas fa-calendar"></i> Updated: <?= date('M j, Y', strtotime($urlData->last_modified)) ?></span>
                                        <span><i class="fas fa-star"></i> Priority: <?= $urlData->priority ?></span>
                                        <span><i class="fas fa-clock"></i> <?= ucfirst($urlData->changefreq) ?></span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="sitemap-container p-6">
            <div class="sitemap-header text-white py-4 px-6 -mx-6 -mt-6 mb-6 rounded-t-lg">
                <h2 class="text-xl font-semibold">Sitemap Tools</h2>
                <p class="opacity-90">Access our sitemap in different formats</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="<?= base_url('sitemap.xml') ?>" target="_blank" class="bg-gray-100 hover:bg-gray-200 p-4 rounded-lg text-center transition-colors">
                    <div class="text-blue-500 text-2xl mb-2">
                        <i class="fas fa-code"></i>
                    </div>
                    <h3 class="font-semibold">XML Sitemap</h3>
                    <p class="text-sm text-gray-600 mt-1">For search engines</p>
                </a>

                <a href="<?= base_url('sitemap/view') ?>" class="bg-gray-100 hover:bg-gray-200 p-4 rounded-lg text-center transition-colors">
                    <div class="text-green-500 text-2xl mb-2">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3 class="font-semibold">HTML View</h3>
                    <p class="text-sm text-gray-600 mt-1">User-friendly version</p>
                </a>

                <a href="<?= base_url('sitemap.xml') ?>" download class="bg-gray-100 hover:bg-gray-200 p-4 rounded-lg text-center transition-colors">
                    <div class="text-purple-500 text-2xl mb-2">
                        <i class="fas fa-download"></i>
                    </div>
                    <h3 class="font-semibold">Download XML</h3>
                    <p class="text-sm text-gray-600 mt-1">Save to your computer</p>
                </a>
            </div>

            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <h3 class="font-semibold text-blue-800 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i> About Sitemaps
                </h3>
                <p class="text-blue-700 text-sm mt-2">
                    A sitemap helps search engines like Google and Bing to better index your website. 
                    It lists all important pages and provides metadata about each page. 
                    Submit your XML sitemap to search engine webmaster tools for better visibility.
                </p>
            </div>
        </div>

        <div class="text-center mt-8 text-gray-600 text-sm">
            <p>© <?= date('Y') ?> KEWASNET. All rights reserved.</p>
        </div>
    </div>

    <script>
        // Layout switching functionality
        function toggleLayout(layout) {
            const container = document.getElementById('sitemap-container');
            const masonryBtn = document.getElementById('masonry-btn');
            const gridBtn = document.getElementById('grid-btn');
            
            if (layout === 'masonry') {
                container.className = 'sitemap-masonry';
                masonryBtn.className = 'bg-white bg-opacity-20 hover:bg-opacity-30 px-3 py-1 rounded transition-colors';
                gridBtn.className = 'bg-white bg-opacity-10 hover:bg-opacity-20 px-3 py-1 rounded transition-colors';
                
                // Store preference
                localStorage.setItem('sitemap-layout', 'masonry');
            } else {
                container.className = 'sitemap-grid';
                gridBtn.className = 'bg-white bg-opacity-20 hover:bg-opacity-30 px-3 py-1 rounded transition-colors';
                masonryBtn.className = 'bg-white bg-opacity-10 hover:bg-opacity-20 px-3 py-1 rounded transition-colors';
                
                // Store preference
                localStorage.setItem('sitemap-layout', 'grid');
            }
        }
        
        // Load saved layout preference
        document.addEventListener('DOMContentLoaded', function() {
            const savedLayout = localStorage.getItem('sitemap-layout') || 'masonry';
            toggleLayout(savedLayout);
        });
        
        // Add smooth scrolling to internal links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Add visual feedback for external links
        document.querySelectorAll('a[href^="http"]:not([href*="' + window.location.hostname + '"])').forEach(link => {
            link.setAttribute('target', '_blank');
            link.setAttribute('rel', 'noopener noreferrer');
            
            // Add external link icon
            const icon = document.createElement('i');
            icon.className = 'fas fa-external-link-alt text-xs ml-1 opacity-50';
            link.appendChild(icon);
        });
    </script>
</body>
</html>