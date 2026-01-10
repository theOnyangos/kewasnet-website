<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('frontendV2/website/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  SEO Meta Tags  -->
<?= $this->section('seo_title'); ?>
<?= !empty($blogPost->meta_title) ? esc($blogPost->meta_title) : esc($blogPost->title) ?>
<?= $this->endSection(); ?>

<?= $this->section('description'); ?>
<?= !empty($blogPost->meta_description) ? esc($blogPost->meta_description) : esc($blogPost->excerpt) ?>
<?= $this->endSection(); ?>

<?= $this->section('image_url'); ?>
<?= !empty($blogPost->featured_image) ? $blogPost->featured_image : base_url('hero.png') ?>
<?= $this->endSection(); ?>

<?php if (!empty($blogPost->meta_keywords)): ?>
<?= $this->section('keywords'); ?>
<?= esc($blogPost->meta_keywords) ?>
<?= $this->endSection(); ?>
<?php endif; ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <!-- Blog Content -->
    <article class="py-16 bg-white">
        <div class="container mx-auto px-4 max-w-4xl">
            <!-- Breadcrumbs -->
            <nav class="mb-8" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-1 sm:gap-2 text-xs sm:text-sm">
                    <li>
                        <a href="<?= base_url() ?>" class="text-slate-600 hover:text-primary transition-colors whitespace-nowrap">Home</a>
                    </li>
                    <li class="flex-shrink-0">
                        <i data-lucide="chevron-right" class="icon-xs text-slate-400"></i>
                    </li>
                    <li>
                        <a href="<?= base_url('news') ?>" class="text-slate-600 hover:text-primary transition-colors whitespace-nowrap">News</a>
                    </li>
                    <li class="flex-shrink-0">
                        <i data-lucide="chevron-right" class="icon-xs text-slate-400"></i>
                    </li>
                    <li class="text-primary min-w-0 flex-1 sm:flex-initial" aria-current="page">
                        <span class="truncate block" title="<?= esc($blogPost->title) ?>"><?= esc($blogPost->title) ?></span>
                    </li>
                </ol>
            </nav>

            <!-- Article Header -->
            <header class="mb-8 sm:mb-12">
                <!-- Category and Meta Info -->
                <div class="flex flex-wrap items-center gap-2 sm:gap-0 text-slate-500 text-xs sm:text-sm mb-4 sm:mb-6">
                    <?php if (!empty($blogPost->category_name)): ?>
                        <span class="bg-secondary text-white px-2 sm:px-3 py-1 rounded-full text-xs font-medium">
                            <?= esc($blogPost->category_name) ?>
                        </span>
                    <?php endif; ?>
                    <span class="hidden sm:inline"><?= date('F j, Y', strtotime($blogPost->published_at)) ?></span>
                    <span class="hidden sm:inline mx-2">•</span>
                    <span class="text-xs sm:text-sm"><?= date('M j, Y', strtotime($blogPost->published_at)) ?></span>
                    <span class="mx-2">•</span>
                    <span class="text-xs sm:text-sm"><?= $blogPost->reading_time ?> min read</span>
                    <span class="mx-2">•</span>
                    <div class="flex items-center text-xs sm:text-sm">
                        <i data-lucide="eye" class="icon-xs mr-1"></i>
                        <span><?= number_format($blogPost->views ?? 0) ?> views</span>
                    </div>
                </div>

                <!-- Article Title -->
                <div class="mb-6 sm:mb-8">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-slate-800 leading-tight">
                        <?= esc($blogPost->title) ?>
                    </h1>
                </div>

                <!-- Author Information -->
                <div class="flex items-center">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full overflow-hidden mr-3 sm:mr-4 bg-gradient-to-br from-primary to-secondary flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-semibold text-base sm:text-lg">
                            <?= strtoupper(substr($authorName, 0, 1)) ?>
                        </span>
                    </div>
                    <div class="text-left min-w-0 flex-1">
                        <p class="font-medium text-slate-800 text-base sm:text-lg truncate">By <?= esc($authorName) ?></p>
                        <p class="text-xs sm:text-sm text-slate-500">Published on <?= date('F j, Y', strtotime($blogPost->published_at)) ?></p>
                    </div>
                </div>
            </header>

            <!-- Featured Image -->
            <?php if (!empty($blogPost->featured_image)): ?>
                <figure class="mb-10 rounded-xl overflow-hidden shadow-lg">
                    <img src="<?= $blogPost->featured_image ?>" 
                         alt="<?= esc($blogPost->title) ?>" 
                         class="w-full h-auto"
                         onerror="this.src='<?= base_url('hero.png') ?>'">
                    <?php if (!empty($blogPost->excerpt)): ?>
                        <figcaption class="text-sm text-slate-500 mt-2 px-4 py-2">
                            <?= esc($blogPost->excerpt) ?>
                        </figcaption>
                    <?php endif; ?>
                </figure>
            <?php endif; ?>

            <!-- Article Content -->
            <div class="prose prose-sm sm:prose-base lg:prose-lg max-w-none">
                <?php if (!empty($blogPost->excerpt)): ?>
                    <p class="lead text-lg sm:text-xl text-slate-700 font-medium mb-6 sm:mb-8">
                        <?= esc($blogPost->excerpt) ?>
                    </p>
                <?php endif; ?>

                <!-- Main Content -->
                <div class="blog-content text-slate-700 leading-relaxed text-sm sm:text-base">
                    <?= $blogPost->content ?>
                </div>
            </div>

            <!-- Additional Images Gallery -->
            <?php if (!empty($additionalImages) && count($additionalImages) > 0): ?>
                <div class="my-8 sm:my-12">
                    <h3 class="text-lg sm:text-xl font-bold text-slate-800 mb-4 sm:mb-6">Image Gallery</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                        <?php foreach ($additionalImages as $image): ?>
                            <div class="group relative overflow-hidden rounded-lg shadow-md hover:shadow-xl transition-all duration-300 cursor-pointer">
                                <img src="<?= $image['file_path'] ?>"
                                     alt="<?= esc($image['original_name'] ?? 'Blog image') ?>"
                                     class="w-full h-48 sm:h-64 object-cover group-hover:scale-110 transition-transform duration-300"
                                     onerror="this.src='<?= base_url('hero.png') ?>'">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end">
                                    <p class="text-white text-xs sm:text-sm p-3 sm:p-4 truncate w-full">
                                        <?= esc($image['original_name'] ?? 'Image') ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Article Footer -->
            <footer class="mt-12 sm:mt-16 pt-6 sm:pt-8 border-t border-slate-200">
                <!-- Tags -->
                <div class="mb-6 sm:mb-8">
                    <h3 class="text-xs sm:text-sm font-semibold text-slate-600 uppercase tracking-wider mb-3 sm:mb-4">Tags</h3>
                    <div class="flex flex-wrap gap-2">
                        <a href="#" class="bg-slate-200 hover:bg-secondary hover:text-white text-slate-700 px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm transition-colors">#WASH</a>
                        <a href="#" class="bg-slate-200 hover:bg-secondary hover:text-white text-slate-700 px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm transition-colors">#Policy</a>
                        <a href="#" class="bg-slate-200 hover:bg-secondary hover:text-white text-slate-700 px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm transition-colors">#Kenya</a>
                        <a href="#" class="bg-slate-200 hover:bg-secondary hover:text-white text-slate-700 px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm transition-colors">#Water</a>
                        <a href="#" class="bg-slate-200 hover:bg-secondary hover:text-white text-slate-700 px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm transition-colors">#Sanitation</a>
                        <a href="#" class="bg-slate-200 hover:bg-secondary hover:text-white text-slate-700 px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm transition-colors">#SDG6</a>
                    </div>
                </div>
                
                <!-- Share Buttons -->
                <div class="mb-8 sm:mb-12">
                    <h3 class="text-sm font-semibold text-slate-600 uppercase tracking-wider mb-3 sm:mb-4">Share this article</h3>
                    <div class="flex flex-wrap gap-3 sm:gap-4">
                        <a href="#" data-platform="facebook" class="share-btn w-10 h-10 sm:w-10 sm:h-10 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition-colors flex-shrink-0" title="Share on Facebook">
                            <i class="fab fa-facebook-f text-sm"></i>
                        </a>
                        <a href="#" data-platform="twitter" class="share-btn w-10 h-10 sm:w-10 sm:h-10 rounded-full bg-blue-400 text-white flex items-center justify-center hover:bg-blue-500 transition-colors flex-shrink-0" title="Share on Twitter">
                            <i class="fab fa-twitter text-sm"></i>
                        </a>
                        <a href="#" data-platform="linkedin" class="share-btn w-10 h-10 sm:w-10 sm:h-10 rounded-full bg-blue-700 text-white flex items-center justify-center hover:bg-blue-800 transition-colors flex-shrink-0" title="Share on LinkedIn">
                            <i class="fab fa-linkedin-in text-sm"></i>
                        </a>
                        <a href="https://wa.me/?text=<?= urlencode(esc($blogPost->title) . ' - ' . current_url()) ?>" target="_blank" class="w-10 h-10 sm:w-10 sm:h-10 rounded-full bg-green-500 text-white flex items-center justify-center hover:bg-green-600 transition-colors flex-shrink-0" title="Share on WhatsApp">
                            <i class="fab fa-whatsapp text-sm"></i>
                        </a>
                        <a href="#" data-platform="copy" class="share-btn w-10 h-10 sm:w-10 sm:h-10 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center hover:bg-slate-300 transition-colors flex-shrink-0" title="Copy link">
                            <i data-lucide="link" class="icon-sm z-10"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Related Articles -->
                <?php if (!empty($relatedPosts)): ?>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-slate-800 mb-4 sm:mb-6">Related Articles</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <?php foreach ($relatedPosts as $relatedPost): ?>
                                <article class="group hover:shadow-lg transition-all duration-300 border border-slate-200 rounded-xl overflow-hidden">
                                    <div class="relative h-36 sm:h-40 overflow-hidden">
                                        <?php if (!empty($relatedPost->featured_image)): ?>
                                            <img src="<?= $relatedPost->featured_image ?>" 
                                                 alt="<?= esc($relatedPost->title) ?>" 
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                 onerror="this.src='<?= base_url('hero.png') ?>'">
                                        <?php else: ?>
                                            <div class="w-full h-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center">
                                                <i data-lucide="image" class="icon-lg sm:icon-xl text-primary/50"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="p-3 sm:p-4">
                                        <?php if (!empty($relatedPost->category_name)): ?>
                                            <span class="bg-secondary text-white px-2 py-1 rounded-full text-xs font-medium mb-2 inline-block">
                                                <?= esc($relatedPost->category_name) ?>
                                            </span>
                                        <?php endif; ?>
                                        <h4 class="text-base sm:text-lg font-bold text-slate-800 group-hover:text-secondary transition-colors mb-2 line-clamp-2">
                                            <?= esc($relatedPost->title) ?>
                                        </h4>
                                        <?php if (!empty($relatedPost->excerpt)): ?>
                                            <p class="text-xs sm:text-sm text-slate-600 mb-3 line-clamp-2">
                                                <?= esc($relatedPost->excerpt) ?>
                                            </p>
                                        <?php endif; ?>
                                        <a href="<?= base_url('news-details/' . esc($relatedPost->slug)) ?>" 
                                           class="text-xs sm:text-sm font-medium text-secondary hover:text-secondaryShades-700 transition-colors flex items-center">
                                            Read more
                                            <i data-lucide="arrow-right" class="icon-xs ml-1"></i>
                                        </a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </footer>
        </div>
    </article>

    <!-- Newsletter Signup -->
    <?= $this->include('frontendV2/website/pages/constants/newsletter-subscription') ?>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<style>
    /* Blog content styling */
    .blog-content {
        line-height: 1.8;
        font-size: 1rem;
    }

    .blog-content h1, .blog-content h2, .blog-content h3, .blog-content h4, .blog-content h5, .blog-content h6 {
        color: #1f2937;
        font-weight: 700;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }

    .blog-content h1 { font-size: 1.875rem; }
    .blog-content h2 { font-size: 1.5rem; }
    .blog-content h3 { font-size: 1.25rem; }
    .blog-content h4 { font-size: 1.125rem; }

    @media (min-width: 640px) {
        .blog-content h1 { font-size: 2.25rem; }
        .blog-content h2 { font-size: 1.875rem; }
        .blog-content h3 { font-size: 1.5rem; }
        .blog-content h4 { font-size: 1.25rem; }
        .blog-content {
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
    }

    .blog-content p {
        margin-bottom: 1rem;
        color: #374151;
    }

    @media (min-width: 640px) {
        .blog-content p {
            margin-bottom: 1.5rem;
        }
    }

    .blog-content ul, .blog-content ol {
        margin-bottom: 1rem;
        padding-left: 1.25rem;
    }

    @media (min-width: 640px) {
        .blog-content ul, .blog-content ol {
            margin-bottom: 1.5rem;
            padding-left: 1.5rem;
        }
    }

    .blog-content li {
        margin-bottom: 0.5rem;
        color: #374151;
    }

    .blog-content blockquote {
        border-left: 4px solid #3b82f6;
        padding: 0.75rem 0.75rem 0.75rem 1rem;
        margin: 1.5rem 0;
        font-style: italic;
        background: #f8fafc;
        border-radius: 0.5rem;
    }

    @media (min-width: 640px) {
        .blog-content blockquote {
            padding: 1rem 1rem 1rem 2rem;
            margin: 2rem 0;
        }
    }

    .blog-content img {
        max-width: 100%;
        height: auto;
        margin: 1.5rem 0;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    @media (min-width: 640px) {
        .blog-content img {
            margin: 2rem 0;
        }
    }

    /* Wrap tables in a scrollable container on mobile */
    .blog-content {
        overflow-x: visible;
    }

    @media (max-width: 639px) {
        .blog-content {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }

    .blog-content table {
        width: 100%;
        max-width: 100%;
        border-collapse: collapse;
        margin: 1.5rem 0;
        font-size: 0.875rem;
        display: table;
    }

    @media (min-width: 640px) {
        .blog-content table {
            margin: 2rem 0;
            font-size: 1rem;
        }
    }

    @media (max-width: 639px) {
        .blog-content table {
            min-width: 500px;
            font-size: 0.8125rem;
        }
    }

    .blog-content th, .blog-content td {
        border: 1px solid #d1d5db;
        padding: 0.5rem;
        text-align: left;
    }

    @media (min-width: 640px) {
        .blog-content th, .blog-content td {
            padding: 0.75rem;
        }
    }

    .blog-content th {
        background-color: #f3f4f6;
        font-weight: 600;
    }

    .blog-content code {
        background-color: #f3f4f6;
        padding: 0.125rem 0.375rem;
        border-radius: 0.25rem;
        font-family: 'Courier New', monospace;
        font-size: 0.8125rem;
    }

    @media (min-width: 640px) {
        .blog-content code {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    }

    .blog-content pre {
        background-color: #1f2937;
        color: #f9fafb;
        padding: 0.75rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin: 1.5rem 0;
        font-size: 0.8125rem;
    }

    @media (min-width: 640px) {
        .blog-content pre {
            padding: 1rem;
            margin: 1.5rem 0;
            font-size: 0.875rem;
        }
    }

    .blog-content pre code {
        background: none;
        padding: 0;
        color: inherit;
    }

    /* Social sharing buttons */
    .share-buttons {
        position: sticky;
        top: 50%;
        transform: translateY(-50%);
        left: -60px;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        z-index: 10;
    }

    @media (max-width: 1024px) {
        .share-buttons {
            display: none;
        }
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add social sharing functionality
    const shareButtons = document.querySelectorAll('.share-btn');
    const currentUrl = window.location.href;
    const currentTitle = document.title;
    
    shareButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const platform = this.dataset.platform;
            let shareUrl = '';
            
            switch(platform) {
                case 'twitter':
                    shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(currentTitle)}&url=${encodeURIComponent(currentUrl)}`;
                    break;
                case 'facebook':
                    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(currentUrl)}`;
                    break;
                case 'linkedin':
                    shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(currentUrl)}`;
                    break;
                case 'copy':
                    navigator.clipboard.writeText(currentUrl).then(function() {
                        // Show copied feedback
                        const originalText = button.innerHTML;
                        button.innerHTML = '<i data-lucide="check" class="icon-sm"></i>';
                        showNotification("success", "Link copied to clipboard");
                        setTimeout(() => {
                            button.innerHTML = originalText;
                        }, 2000);
                    });
                    return;
            }
            
            if (shareUrl) {
                window.open(shareUrl, '_blank', 'width=600,height=400');
            }
        });
    });
    
    // Add reading progress indicator
    const progressBar = document.createElement('div');
    progressBar.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: linear-gradient(90deg, #3b82f6, #06b6d4);
        z-index: 9999;
        transition: width 0.3s ease;
    `;
    document.body.appendChild(progressBar);
    
    window.addEventListener('scroll', function() {
        const article = document.querySelector('article');
        if (article) {
            const articleTop = article.offsetTop;
            const articleHeight = article.offsetHeight;
            const scrollTop = window.pageYOffset;
            const windowHeight = window.innerHeight;
            
            const scrolled = Math.max(0, scrollTop - articleTop);
            const maxScroll = articleHeight - windowHeight;
            const progress = Math.min(100, (scrolled / maxScroll) * 100);
            
            progressBar.style.width = progress + '%';
        }
    });
    
    // Add smooth scrolling for anchor links
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
    
    // Initialize Lucide icons for dynamically added content
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Custom Notifications Function
    function showNotification(type, message) {
        // Create notification element
        const notification = document.createElement("div");
        notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full bg-white border-l-4 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
            type === "success" ? "border-green-500" : "border-red-500"
        }`;

        notification.innerHTML = `
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i data-lucide="${
                        type === "success" ? "check-circle" : "x-circle"
                        }" class="w-5 h-5 ${
            type === "success" ? "text-green-500" : "text-red-500"
        }"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900">${message}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <button type="button" class="inline-flex text-gray-400 hover:text-gray-600" onclick="this.closest('.fixed').remove()">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            `;

        document.body.appendChild(notification);
        lucide.createIcons();

        // Animate in
        setTimeout(() => {
            notification.classList.remove("translate-x-full");
        }, 100);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add("translate-x-full");
            setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
            }, 300);
        }, 5000);
    }
});
</script>
<?= $this->endSection() ?>