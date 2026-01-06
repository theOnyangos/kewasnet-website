<?php
    // Ensure activePillar is available and is an array
    if (!isset($activePillar) || empty($activePillar)) {
        return;
    }
    
    // Convert to array if object
    if (is_object($activePillar)) {
        $activePillar = (array) $activePillar;
    }
    
    $pillarSlug = $activePillar['slug'] ?? '';
    $pillarTitle = $activePillar['title'] ?? 'Pillar';
    $pillarDescription = $activePillar['description'] ?? '';
    $pillarContent = $activePillar['content'] ?? '';
    $pillarIcon = $activePillar['icon'] ?? 'columns';
    $pillarImage = $activePillar['image_path'] ?? null;
    $buttonText = $activePillar['button_text'] ?? 'Explore Resources';
    $buttonLink = $activePillar['button_link'] ?? base_url('ksp/pillar-articles/' . $pillarSlug);
    
    // Format image path
    if (!empty($pillarImage)) {
        if (strpos($pillarImage, 'http') === 0 || strpos($pillarImage, '//') === 0) {
            // Already a full URL
        } elseif (strpos($pillarImage, '/') === 0) {
            $pillarImage = base_url(ltrim($pillarImage, '/'));
        } else {
            $pillarImage = base_url($pillarImage);
        }
    }
?>

<!-- Dynamic Pillar Content -->
<section class="py-12 md:py-16" id="<?= esc($pillarSlug) ?>">
    <div class="mx-auto px-4 max-w-4xl">
        
        <!-- Document Header -->
        <header class="text-left mb-12">
            <?php if (!empty($pillarImage)): ?>
                <div class="mb-8">
                    <img src="<?= esc($pillarImage) ?>" alt="<?= esc($pillarTitle) ?>" class="w-full h-64 object-cover rounded-lg shadow-lg">
                </div>
            <?php endif; ?>
            
            <div class="flex items-center mb-6">
                <?php if (!empty($pillarIcon)): ?>
                    <div class="w-16 h-16 bg-gradient-to-br from-primary to-secondary rounded-lg flex items-center justify-center mr-4">
                        <i data-lucide="<?= esc($pillarIcon) ?>" class="w-8 h-8 text-white"></i>
                    </div>
                <?php endif; ?>
                <h1 class="text-4xl md:text-5xl font-bold text-primary">
                    <?= esc($pillarTitle) ?>
                </h1>
            </div>
            
            <?php if (!empty($pillarDescription)): ?>
                <p class="text-lg text-slate-700 leading-relaxed mb-8">
                    <?= esc($pillarDescription) ?>
                </p>
            <?php endif; ?>
            
            <?php if (!empty($buttonText) && !empty($buttonLink)): ?>
                <div class="flex justify-center md:justify-start">
                    <a href="<?= esc($buttonLink) ?>" class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-lg font-medium transition-all inline-flex items-center">
                        <i data-lucide="external-link" class="w-4 h-4 mr-2"></i>
                        <?= esc($buttonText) ?>
                    </a>
                </div>
            <?php endif; ?>
        </header>

        <!-- Document Content -->
        <?php if (!empty($pillarContent)): ?>
            <article class="prose prose-lg max-w-none">
                <div class="pillar-content">
                    <?= $pillarContent ?>
                </div>
            </article>
        <?php else: ?>
            <div class="bg-slate-50 rounded-lg p-8 text-center">
                <i data-lucide="file-text" class="w-16 h-16 text-slate-400 mx-auto mb-4"></i>
                <p class="text-slate-600 text-lg">Content for this pillar is coming soon.</p>
            </div>
        <?php endif; ?>
        
        <!-- Articles Link Section -->
        <div class="mt-12 pt-8 border-t border-slate-200">
            <div class="text-center">
                <a href="<?= base_url('ksp/pillar-articles/' . esc($pillarSlug)) ?>" class="inline-flex items-center text-primary hover:text-secondary font-medium transition-colors">
                    <i data-lucide="book-open" class="w-5 h-5 mr-2"></i>
                    View Articles & Resources
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<style>
    .pillar-content {
        color: #334155;
        line-height: 1.8;
    }
    
    .pillar-content h2 {
        font-size: 2rem;
        font-weight: 700;
        color: #1e40af;
        margin-top: 2rem;
        margin-bottom: 1rem;
    }
    
    .pillar-content h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1e293b;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }
    
    .pillar-content p {
        margin-bottom: 1rem;
        line-height: 1.8;
    }
    
    .pillar-content ul,
    .pillar-content ol {
        margin-bottom: 1rem;
        padding-left: 1.5rem;
    }
    
    .pillar-content li {
        margin-bottom: 0.5rem;
    }
    
    .pillar-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        margin: 1.5rem 0;
    }
    
    .pillar-content a {
        color: #2563eb;
        text-decoration: underline;
    }
    
    .pillar-content a:hover {
        color: #1d4ed8;
    }
</style>

<script>
    // Initialize Lucide icons after content loads
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>

