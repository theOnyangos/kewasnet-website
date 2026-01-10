<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();

    // dd($stats);
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('frontendV2/ksp/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <section class="ksp-hero-section">
        <!-- Slides -->
        <div class="ksp-slides-container">
            <!-- Slide 1 -->
            <div class="ksp-slide">
                <video autoplay muted loop playsinline class="ksp-video-bg">
                    <source src="<?= base_url('assets/new/water-drops.mp4') ?>" type="video/mp4">
                </video>
                <div class="container mx-auto px-4 h-full flex items-center relative z-10">
                    <div class="max-w-3xl text-white">
                        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 leading-tight">Transforming Kenya's WASH Sector</h1>
                        <p class="text-lg md:text-xl mb-6 text-white/90">Access the largest collection of water, sanitation and hygiene knowledge in East Africa</p>

                        <!-- Description -->
                        <p class="text-base md:text-lg mb-6 text-slate-200">Discover a wealth of resources, including research papers, case studies, and best practices to enhance your WASH initiatives.</p>

                        <!-- <button class="gradient-btn px-8 py-3 rounded-[50px] text-white font-medium text-lg flex items-center">
                            <span>Explore Resources</span>
                            <i data-lucide="arrow-right" class="ml-2 w-5 h-5 z-10"></i>
                        </button> -->
                    </div>
                </div>
            </div>
            
            <!-- Slide 2 -->
            <div class="ksp-slide">
                <video autoplay muted loop playsinline class="ksp-video-bg">
                    <source src="<?= base_url('assets/new/moving-water.mp4') ?>" type="video/mp4">
                </video>
                <div class="container mx-auto px-4 h-full flex items-center relative z-10">
                    <div class="max-w-3xl text-white">
                        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 leading-tight">Building Climate Resilient Communities</h1>
                        <p class="text-lg md:text-xl mb-6 text-white/90">Learn from innovative solutions addressing water challenges in changing climates</p>

                        <!-- Description -->
                        <p class="text-base md:text-lg mb-6 text-slate-200">Explore case studies and best practices for building resilience in water and sanitation systems.</p>

                        <!-- <button class="gradient-btn px-8 py-3 rounded-[50px] text-white font-medium text-lg flex items-center">
                            <span>Discover Solutions</span>
                            <i data-lucide="arrow-right" class="ml-2 w-5 h-5 z-10"></i>
                        </button> -->
                    </div>
                </div>
            </div>
            
            <!-- Slide 3 -->
            <div class="ksp-slide">
                <video autoplay muted loop playsinline class="ksp-video-bg">
                    <source src="<?= base_url('assets/new/climate.mp4') ?>" type="video/mp4">
                </video>
                <div class="container mx-auto px-4 h-full flex items-center relative z-10">
                    <div class="max-w-3xl text-white">
                        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 leading-tight">Connecting WASH Professionals</h1>
                        <p class="text-lg md:text-xl mb-6 text-white/90">Join Kenya's largest network of water and sanitation experts</p>

                        <!-- Description -->
                        <p class="text-base md:text-lg mb-6 text-slate-200">Share knowledge, collaborate on projects, and drive innovation in the WASH sector.</p>

                        <!-- <button class="gradient-btn px-8 py-3 rounded-[50px] text-white font-medium text-lg flex items-center">
                            <span>Join Network</span>
                            <i data-lucide="arrow-right" class="ml-2 w-5 h-5 z-10"></i>
                        </button> -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Arrows -->
        <button class="ksp-nav-arrow ksp-arrow-left">
            <i data-lucide="chevron-left" class="ksp-arrow-icon"></i>
        </button>
        <button class="ksp-nav-arrow ksp-arrow-right">
            <i data-lucide="chevron-right" class="ksp-arrow-icon"></i>
        </button>
        
        <!-- Slider controls -->
        <div class="ksp-slider-controls">
            <button class="ksp-slider-dot" data-index="0"></button>
            <button class="ksp-slider-dot" data-index="1"></button>
            <button class="ksp-slider-dot" data-index="2"></button>
        </div>
    </section>

    <!-- Knowledge Platform Section -->
    <section class="ksp-platform-section">
        <!-- Dotted background overlay -->
        <div class="ksp-dotted-bg"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="ksp-section-header">
                <h2 class="ksp-section-title">
                    Knowledge Sharing Platform
                </h2>
                <p class="ksp-section-description">
                    Kenya's leading knowledge-sharing service on Water, Sanitation and Hygiene. 
                    Connecting professionals, sharing insights, and driving sector transformation.
                </p>
            </div>

            <div class="ksp-platform-grid">
                <!-- Pillars -->
                <div class="ksp-platform-card">
                    <div class="ksp-card-image-container">
                        <div class="ksp-card-image" style="background-image: url('https://images.unsplash.com/photo-1500673922987-e212871fec22?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80')"></div>
                        <div class="ksp-card-image-overlay"></div>
                        <div class="ksp-card-stats">
                            <i data-lucide="book-open" class="ksp-card-icon"></i>
                            <div class="ksp-card-stat-text"><?= number_format($stats['total_resources'] ?? 0) ?>+ Documents</div>
                        </div>
                    </div>
                    
                    <div class="ksp-card-content">
                        <h3 class="ksp-card-title">
                            Pillars
                        </h3>
                        <p class="ksp-card-text">
                            Access comprehensive resources on WASH, Governance, Climate Change, Nexus and IWRM. Find policy briefs, learning reports, and strategy documents. Explore <?= number_format($stats['total_resources'] ?? 0) ?>+ published articles with <?= number_format($stats['total_downloads'] ?? 0) ?>+ total downloads.
                        </p>
                        <div class="mt-4 flex items-center gap-4 text-sm text-slate-600">
                            <div class="flex items-center gap-1">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                <span><?= number_format($stats['total_resources'] ?? 0) ?> Articles</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i data-lucide="download" class="w-4 h-4"></i>
                                <span><?= number_format($stats['total_downloads'] ?? 0) ?> Downloads</span>
                            </div>
                        </div>
                        <button type="button" onClick="location.href='<?= base_url('ksp/pillars') ?>'" class="ksp-card-btn mt-4">
                            <span>Explore Pillars</span>
                            <i data-lucide="arrow-right" class="ksp-btn-icon z-10"></i>
                        </button>
                    </div>
                </div>

                <!-- Learning Hub -->
                <div class="ksp-platform-card">
                    <div class="ksp-card-image-container">
                        <div class="ksp-card-image" style="background-image: url('https://images.unsplash.com/photo-1518495973542-4542c06a5843?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80')"></div>
                        <div class="ksp-card-image-overlay"></div>
                        <div class="ksp-card-stats">
                            <i data-lucide="graduation-cap" class="ksp-card-icon"></i>
                            <div class="ksp-card-stat-text"><?= number_format($stats['total_learning_hub'] ?? 0) ?>+ Courses</div>
                        </div>
                    </div>
                    
                    <div class="ksp-card-content">
                        <h3 class="ksp-card-title">
                            Learning Hub
                        </h3>
                        <p class="ksp-card-text">
                            Discover training materials, best practices, and innovative solutions from across Kenya's WASH sector. Learn from real field experiences through our comprehensive course library with <?= number_format($stats['total_learning_hub'] ?? 0) ?>+ published courses covering various skill levels and topics.
                        </p>
                        <div class="mt-4 flex items-center gap-4 text-sm text-slate-600">
                            <div class="flex items-center gap-1">
                                <i data-lucide="book" class="w-4 h-4"></i>
                                <span><?= number_format($stats['total_learning_hub'] ?? 0) ?> Courses</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i data-lucide="play-circle" class="w-4 h-4"></i>
                                <span>Interactive Learning</span>
                            </div>
                        </div>
                        <button type="button" onClick="location.href='<?= base_url('ksp/learning-hub') ?>'" class="ksp-card-btn mt-4">
                            <span>Explore Learning Hub</span>
                            <i data-lucide="arrow-right" class="ksp-btn-icon z-10"></i>
                        </button>
                    </div>
                </div>

                <!-- Networking Corner -->
                <div class="ksp-platform-card">
                    <div class="ksp-card-image-container">
                        <div class="ksp-card-image" style="background-image: url('https://images.unsplash.com/photo-1465146344425-f00d5f5c8f07?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80')"></div>
                        <div class="ksp-card-image-overlay"></div>
                        <div class="ksp-card-stats">
                            <i data-lucide="users" class="ksp-card-icon"></i>
                            <div class="ksp-card-stat-text"><?= number_format($stats['total_members'] ?? 0) ?>+ Members</div>
                        </div>
                    </div>
                    
                    <div class="ksp-card-content">
                        <h3 class="ksp-card-title">
                            Networking Corner
                        </h3>
                        <p class="ksp-card-text">
                            Connect with WASH professionals, share experiences, and collaborate on sector challenges. Build meaningful partnerships. Join <?= number_format($stats['total_members'] ?? 0) ?>+ members across <?= number_format($stats['total_forums'] ?? 0) ?>+ active forums with <?= number_format($stats['total_discussions'] ?? 0) ?>+ ongoing discussions.
                        </p>
                        <div class="mt-4 flex items-center gap-4 text-sm text-slate-600">
                            <div class="flex items-center gap-1">
                                <i data-lucide="users" class="w-4 h-4"></i>
                                <span><?= number_format($stats['total_members'] ?? 0) ?> Members</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i data-lucide="message-square" class="w-4 h-4"></i>
                                <span><?= number_format($stats['total_discussions'] ?? 0) ?> Discussions</span>
                            </div>
                        </div>
                        <button type="button" onClick="location.href='<?= base_url('ksp/networking-corner') ?>'" class="ksp-card-btn mt-4">
                            <span>Explore Networking Corner</span>
                            <i data-lucide="arrow-right" class="ksp-btn-icon z-10"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    document.addEventListener('DOMContentLoaded', () => {
        // Select elements
        const slides = document.querySelectorAll('.ksp-slide');
        const dots = document.querySelectorAll('.ksp-slider-dot');
        const arrows = document.querySelectorAll('.ksp-nav-arrow');
        const slidesContainer = document.querySelector('.ksp-slides-container');
        const videos = document.querySelectorAll('.ksp-slide .ksp-video-bg');
        
        let currentIndex = 0;
        const slideCount = slides.length;
        const transitionSpeed = 1.0;
        
        // Initialize videos
        videos.forEach((video, index) => {
            if (index !== 0) video.pause();
            else video.play();
            
            // Enhance video contrast
            video.style.filter = 'contrast(1.1) brightness(0.85)';
        });

        // Set initial position
        gsap.set(slidesContainer, { x: 0 });
        updateControls();
        
        // Navigation functions
        arrows.forEach(arrow => {
            arrow.addEventListener('click', function() {
                const direction = this.classList.contains('ksp-arrow-left') ? -1 : 1;
                navigate(direction);
            });
        });
        
        dots.forEach(dot => {
            dot.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                goToSlide(index);
            });
        });
        
        function navigate(direction) {
            let newIndex = currentIndex + direction;
            if (newIndex < 0) newIndex = slideCount - 1;
            else if (newIndex >= slideCount) newIndex = 0;
            goToSlide(newIndex);
        }
        
        function goToSlide(index) {
            if (index === currentIndex) return;
            
            videos[currentIndex].pause();
            
            gsap.to(slidesContainer, {
                x: `-${index * 100}%`,
                duration: transitionSpeed,
                ease: "power2.inOut",
                onComplete: () => {
                    videos[index].play();
                    currentIndex = index;
                    updateControls();
                }
            });
            
            animateContent(index);
        }
        
        function animateContent(index) {
            const slide = slides[index];
            const elements = [
                slide.querySelector('.ksp-headline'),
                slide.querySelector('.ksp-subhead'),
                slide.querySelector('.ksp-description'),
                slide.querySelector('.ksp-primary-btn')
            ].filter(el => el); // Filter out null elements
            
            gsap.set(elements, { opacity: 0, y: 20 });
            gsap.to(elements, {
                opacity: 1,
                y: 0,
                duration: 0.6,
                stagger: 0.1,
                ease: "power2.out"
            });
        }
        
        function updateControls() {
            dots.forEach((dot, index) => {
                dot.style.backgroundColor = index === currentIndex ? 'white' : 'rgba(255, 255, 255, 0.5)';
            });
        }
        
        // Initialize
        animateContent(0);
        
        // Scroll animations
        gsap.registerPlugin(ScrollTrigger);
        
        gsap.utils.toArray('.ksp-platform-card').forEach((card, i) => {
            gsap.from(card, {
                scrollTrigger: {
                    trigger: card,
                    start: "top 80%",
                    toggleActions: "play none none none"
                },
                y: 50,
                opacity: 0,
                duration: 0.6,
                delay: i * 0.1,
                ease: "power2.out"
            });
        });
        
        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') navigate(-1);
            if (e.key === 'ArrowRight') navigate(1);
        });
    });
</script>
<?= $this->endSection() ?>