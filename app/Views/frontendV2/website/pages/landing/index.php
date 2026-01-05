<?php 
    use Carbon\Carbon;
    use App\Helpers\UrlHelper;

    $currentUrl = new UrlHelper();

    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('frontendV2/website/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <!-- Hero Section -->
    <section class="hero-section">
        <!-- Water wave animation background -->
        <div class="wave-container">
            <div class="wave wave-animation"></div>
            <div class="wave wave-2 wave-animation"></div>
        </div>
        
        <!-- Video Background -->
        <div class="video-background">
            <video autoplay loop muted playsinline>
                <source src="<?= base_url('assets/new/water_falls.mp4') ?>" type="video/mp4">
                <!-- Fallback image if video doesn't load -->
                <div class="video-fallback" style="background-image: url('<?= base_url('assets/tap-water.jpg') ?>')"></div>
            </video>
        </div>
        
        <!-- Hero Content -->
        <div class="hero-content">
            <div class="hero-inner">
                <h1 class="hero-title">
                    Transforming Kenya's <br>
                    <span class="block text-secondary">WASH Sector</span>
                </h1>
                
                <p class="hero-subtitle">
                    Leading knowledge sharing, capacity building, and advocacy for sustainable 
                    Water, Sanitation, and Hygiene solutions across Kenya
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16 hero-buttons">
                    <button type="button" onclick="window.location.href='<?= base_url('ksp') ?>'" class="bg-secondary hover:bg-secondary/90 text-white px-8 py-2 text-lg rounded-[50px] transition-colors flex items-center justify-center hero-button-1 transform hover:scale-105">
                        Explore Knowledge Platform
                        <i data-lucide="arrow-right" class="ml-2 icon-lg"></i>
                    </button>
                    <button type="button" onclick="window.location.href='<?= base_url('about') ?>'" class="border-2 border-white text-white hover:bg-white hover:text-primary px-8 py-2 text-lg rounded-[50px] transition-colors hero-button-2 transform hover:scale-105 flex items-center justify-center">
                        Learn About Our Work
                        <i data-lucide="arrow-right" class="ml-2 icon-lg"></i>
                    </button>
                </div>
                
                <!-- Stats -->
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i data-lucide="users" class="text-secondary"></i>
                        </div>
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Network Members</div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon">
                            <i data-lucide="droplets" class="text-secondary"></i>
                        </div>
                        <div class="stat-number">47</div>
                        <div class="stat-label">Counties Reached</div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon">
                            <i data-lucide="lightbulb" class="text-secondary"></i>
                        </div>
                        <div class="stat-number">1000+</div>
                        <div class="stat-label">Knowledge Resources</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Knowledge Platform -->
    <section class="py-20 bg-slate-50 relative">
        <!-- Dotted background overlay -->
        <div class="radial-pattern-container">
            <div class="radial-pattern"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-slate-800 mb-6">
                    Knowledge Sharing Platform
                </h2>
                <p class="text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
                    Kenya's leading knowledge-sharing service on Water, Sanitation and Hygiene. 
                    Connecting professionals, sharing insights, and driving sector transformation.
                </p>
            </div>

            <div class="card-grid">
                <!-- Card 1 -->
                <div class="card">
                    <div class="card-image-container">
                        <img src="<?= base_url('assets/new/pillars.jpg') ?>" class="card-image" alt="Pillars">
                        <div class="card-overlay"></div>
                        <div class="card-badge">
                            <i data-lucide="book-open"></i>
                            <div><?= $stats['total_resources'] ?? 0 ?>+ Documents</div>
                        </div>
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">Pillars</h3>
                        <p class="card-description">
                            Access comprehensive resources on WASH, Governance, Climate Change, Nexus and IWRM. 
                            Find policy briefs, learning reports, and strategy documents.
                        </p>
                        <button type="button" onClick="location.href='<?= base_url('ksp/pillars') ?>'" class="card-button">
                            Explore Pillars
                            <i data-lucide="arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="card">
                    <div class="card-image-container">
                        <img src="<?= base_url('assets/new/learning.jpg') ?>" class="card-image" alt="Learning Hub">
                        <div class="card-overlay"></div>
                        <div class="card-badge">
                            <i data-lucide="users-2"></i>
                            <div><?= $stats['total_learning_hub'] ?? 0 ?>+ Resources</div>
                        </div>
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">Learning Hub</h3>
                        <p class="card-description">
                            Discover training materials, best practices, and innovative solutions from across 
                            Kenya's WASH sector. Learn from real field experiences.
                        </p>
                        <button type="button" onClick="location.href='<?= base_url('ksp/learning-hub') ?>'" class="card-button">
                            Explore Learning Hub
                            <i data-lucide="arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="card">
                    <div class="card-image-container">
                        <img src="<?= base_url('assets/new/Networking.JPG') ?>" class="card-image" alt="Networking">
                        <div class="card-overlay"></div>
                        <div class="card-badge">
                            <i data-lucide="network"></i>
                            <div><?= $stats['total_members'] ?? 0 ?>+ Members</div>
                        </div>
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">Networking Corner</h3>
                        <p class="card-description">
                            Connect with WASH professionals, share experiences, and collaborate on sector 
                            challenges. Build meaningful partnerships.
                        </p>
                        <button type="button" onClick="location.href='<?= base_url('ksp/networking-corner') ?>'" class="card-button">
                            Explore Networking Corner
                            <i data-lucide="arrow-right"></i>
                        </button>
                    </div>
                </div>
                </div>

            <div class="w-full flex justify-center">
                <!-- Alternative Button -->
                <button type="button" onClick="location.href='<?= base_url('ksp') ?>'" class="gradient-btn flex items-center text-white px-8 py-4 rounded-[50px] transition-all duration-300">
                    <span>Access Full Platform</span>
                    <i data-lucide="arrow-right" class="ml-2 icon-lg z-10"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- Impact Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-slate-800 mb-6">
                    Our Impact Across Kenya
                </h2>
                <p class="text-xl text-slate-600 max-w-3xl mx-auto">
                    Driving sustainable change through knowledge sharing, capacity building, 
                    and strategic partnerships in the WASH sector.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
                <div class="text-center border shadow-lg hover:shadow-xl transition-shadow bg-white rounded-lg p-8">
                    <div class="bg-secondaryShades-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="droplet" class="icon-xl text-secondaryShades-600"></i>
                    </div>
                    <div class="text-3xl font-bold text-slate-800 mb-2">2M+</div>
                    <div class="text-lg font-semibold text-secondaryShades-600 mb-2">People Reached</div>
                    <p class="text-slate-600 text-sm">Through our WASH programs and advocacy initiatives</p>
                </div>

                <div class="text-center border shadow-lg hover:shadow-xl transition-shadow bg-white rounded-lg p-8">
                    <div class="bg-secondaryShades-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="users" class="icon-xl text-secondaryShades-600"></i>
                    </div>
                    <div class="text-3xl font-bold text-slate-800 mb-2">500+</div>
                    <div class="text-lg font-semibold text-secondaryShades-600 mb-2">Partners</div>
                    <p class="text-slate-600 text-sm">Government agencies, NGOs, and private sector collaborators</p>
                </div>

                <div class="text-center border shadow-lg hover:shadow-xl transition-shadow bg-white rounded-lg p-8">
                    <div class="bg-secondaryShades-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="globe" class="icon-xl text-secondaryShades-600"></i>
                    </div>
                    <div class="text-3xl font-bold text-slate-800 mb-2">47</div>
                    <div class="text-lg font-semibold text-secondaryShades-600 mb-2">Counties</div>
                    <p class="text-slate-600 text-sm">Across Kenya where we've implemented programs</p>
                </div>

                <div class="text-center border shadow-lg hover:shadow-xl transition-shadow bg-white rounded-lg p-8">
                    <div class="bg-secondaryShades-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="trending-up" class="icon-xl text-secondaryShades-600"></i>
                    </div>
                    <div class="text-3xl font-bold text-slate-800 mb-2">85%</div>
                    <div class="text-lg font-semibold text-secondaryShades-600 mb-2">Success Rate</div>
                    <p class="text-slate-600 text-sm">In sustainable WASH solution implementation</p>
                </div>
            </div>

            <!-- Photo Grid -->
            <div class="photo-grid">
                <!-- Card 1 -->
                <div class="photo-card">
                    <div class="photo-card-bg" style="background-image: url('<?= base_url("assets/new/policy_advocacy.JPG") ?>')"></div>
                    <div class="photo-card-overlay"></div>
                    <div class="photo-card-content">
                        <h3 class="photo-card-title">Community Engagement</h3>
                        <p class="photo-card-description">Working directly with communities</p>
                    </div>
                </div>
                
                <!-- Card 2 -->
                <div class="photo-card">
                    <div class="photo-card-bg" style="background-image: url('<?= base_url("assets/new/community_engagement.JPG") ?>')"></div>
                    <div class="photo-card-overlay"></div>
                    <div class="photo-card-content">
                        <h3 class="photo-card-title">Infrastructure Development</h3>
                        <p class="photo-card-description">Building sustainable water systems</p>
                    </div>
                </div>
                
                <!-- Card 3 -->
                <div class="photo-card">
                    <div class="photo-card-bg" style="background-image: url('<?= base_url("assets/new/infrastructure_development.jpg") ?>')"></div>
                    <div class="photo-card-overlay"></div>
                    <div class="photo-card-content">
                        <h3 class="photo-card-title">Policy Advocacy</h3>
                        <p class="photo-card-description">Influencing sector governance</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- News Section -->
    <?php if(!empty($blogs)): ?>
        <section class="py-20 bg-slate-50 relative">
            <!-- Dotted background overlay -->
            <div class="radial-pattern-container">
                <div class="radial-pattern"></div>
            </div>

            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold text-slate-800 mb-6">
                        Latest News & Updates
                    </h2>
                    <p class="text-xl text-slate-600 max-w-3xl mx-auto">
                        Stay informed about our latest initiatives, partnerships, and developments 
                        in Kenya's WASH sector.
                    </p>
                </div>

                <div class="blog-grid">
                    <!-- Blog Item -->
                     <?php foreach ($blogs as $blog): ?>
                        <div class="blog-card">
                            <div class="blog-image-container">
                                <?php 
                                $imageUrl = !empty($blog->featured_image) ? $blog->featured_image : base_url('assets/images/default-blog.jpg');
                                ?>
                                <div class="blog-image" style="background-image: url('<?= $imageUrl ?>'); background-size: cover; background-position: center;"></div>
                                <div class="blog-image-overlay"></div>
                                <span class="blog-category"><?= esc($blog->category_name) ?></span>
                            </div>
                            <div class="blog-content">
                                <div class="blog-meta">
                                    <i data-lucide="calendar"></i>
                                    <?= date('F j, Y', strtotime($blog->published_at)) ?>
                                </div>
                                <h3 class="blog-title"><?= esc($blog->title) ?></h3>
                                <p class="blog-excerpt">
                                    <?php
                                    // Strip HTML tags and limit to 150 characters
                                    $plainText = strip_tags($blog->excerpt);
                                    if (strlen($plainText) > 150) {
                                        $plainText = substr($plainText, 0, 150);
                                        // Find last space to avoid cutting words
                                        $lastSpace = strrpos($plainText, ' ');
                                        if ($lastSpace !== false) {
                                            $plainText = substr($plainText, 0, $lastSpace);
                                        }
                                        $plainText .= '...';
                                    }
                                    echo esc($plainText);
                                    ?>
                                </p>
                                <button type="button" onclick="window.open('<?= base_url('news-details/' . $blog->slug) ?>', '_self')" class="blog-button">
                                    Read More
                                    <i data-lucide="arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="w-full flex justify-center">
                    <!-- View All News Button -->
                    <button type="button" onClick="location.href='<?= base_url('news') ?>'" class="gradient-btn flex items-center text-white px-8 py-4 rounded-[50px] transition-all duration-300">
                        <span>View All News</span>
                        <i data-lucide="arrow-right" class="ml-2 icon-lg"></i>
                    </button>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Events Section -->
    <?php if(!empty($events)): ?>
        <style>
            .events-pattern-bg {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 0;
                pointer-events: none;
                background-image: 
                    repeating-linear-gradient(45deg, rgba(0, 0, 0, 0.1) 0, rgba(0, 0, 0, 0.1) 1px, transparent 1px, transparent 20px),
                    repeating-linear-gradient(-45deg, rgba(0, 0, 0, 0.1) 0, rgba(0, 0, 0, 0.1) 1px, transparent 1px, transparent 20px);
                background-size: 40px 40px;
            }
        </style>
        <section class="py-20 relative text-gray-900" style="background-color: #fafafa; min-height: auto; position: relative;">
            <!-- Diagonal Grid Pattern -->
            <div class="events-pattern-bg"></div>

            <div class="container mx-auto px-4 relative z-10">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold text-slate-800 mb-6">
                        Upcoming Events
                    </h2>
                    <p class="text-xl text-slate-600 max-w-3xl mx-auto">
                        Join us for our upcoming events, workshops, and conferences in Kenya's WASH sector.
                    </p>
                </div>

                <div class="blog-grid">
                    <!-- Event Item -->
                     <?php foreach ($events as $event): ?>
                        <div class="blog-card">
                            <div class="blog-image-container">
                                <?php 
                                $imageUrl = !empty($event['image_url']) ? $event['image_url'] : base_url('assets/images/default-blog.jpg');
                                ?>
                                <div class="blog-image" style="background-image: url('<?= $imageUrl ?>'); background-size: cover; background-position: center;"></div>
                                <div class="blog-image-overlay"></div>
                                <span class="blog-category"><?= esc($event['event_type'] === 'paid' ? 'Paid Event' : 'Free Event') ?></span>
                            </div>
                            <div class="blog-content">
                                <div class="blog-meta">
                                    <i data-lucide="calendar"></i>
                                    <?= date('F j, Y', strtotime($event['start_date'])) ?>
                                    <?php if (!empty($event['start_time'])): ?>
                                        <span class="mx-2">•</span>
                                        <i data-lucide="clock"></i>
                                        <?= date('g:i A', strtotime($event['start_time'])) ?>
                                    <?php endif; ?>
                                </div>
                                <h3 class="blog-title"><?= esc($event['title']) ?></h3>
                                <p class="blog-excerpt">
                                    <?php
                                    // Strip HTML tags and limit to 150 characters
                                    $plainText = strip_tags($event['description'] ?? '');
                                    if (strlen($plainText) > 150) {
                                        $plainText = substr($plainText, 0, 150);
                                        // Find last space to avoid cutting words
                                        $lastSpace = strrpos($plainText, ' ');
                                        if ($lastSpace !== false) {
                                            $plainText = substr($plainText, 0, $lastSpace);
                                        }
                                        $plainText .= '...';
                                    }
                                    echo esc($plainText ?: 'Join us for this exciting event.');
                                    ?>
                                </p>
                                <button type="button" onclick="window.open('<?= base_url('events/' . $event['slug']) ?>', '_self')" class="blog-button">
                                    View Details
                                    <i data-lucide="arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="w-full flex justify-center">
                    <!-- View All Events Button -->
                    <button type="button" onClick="location.href='<?= base_url('events') ?>'" class="gradient-btn flex items-center text-white px-8 py-4 rounded-[50px] transition-all duration-300">
                        <span>View All Events</span>
                        <i data-lucide="arrow-right" class="ml-2 icon-lg"></i>
                    </button>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- YouTube Video Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-slate-800 mb-6">
                    Watch Our Latest Videos
                </h2>
                <p class="text-xl text-slate-600 max-w-3xl mx-auto">
                    Explore our YouTube channel for insights, interviews, and updates on Kenya's WASH sector.
                </p>
            </div>

            <!-- Video Embed -->
            <div class="max-w-4xl mx-auto mb-12 rounded-xl overflow-hidden shadow-xl">
                <div class="aspect-w-16 aspect-h-9">
                    <iframe 
                        class="w-full h-[300px] md:h-[500px]" 
                        src="https://www.youtube.com/embed/cCc6zlr63K8" 
                        title="YouTube video player" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                </div>
            </div>

            <!-- YouTube Button -->
            <div class="text-center">
                <a 
                    href="https://www.youtube.com/@kewasnet4300" 
                    target="_blank" 
                    rel="noopener noreferrer"
                    class="youtube-btn inline-flex items-center text-white px-8 py-3 rounded-[50px] transition-all duration-300"
                >
                    <i class="fab fa-youtube mr-3 text-xl z-10"></i>
                    <span class="z-10">Visit Our YouTube Channel</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Partners Section -->
    <?php if($partners): ?>
        <section class="relative py-20 bg-slate-50" id="partners-section">
            <!-- Dotted background overlay -->
            <div class="radial-pattern-container">
                <div class="radial-pattern"></div>
            </div>

            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold text-slate-800 mb-6">
                        Our Members
                    </h2>
                    <p class="text-xl text-slate-600 max-w-3xl mx-auto">
                        Collaborating with leading organizations to transform Kenya's WASH sector
                    </p>
                </div>

                <!-- Partners Grid -->
                <div class="partners-grid grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8 max-w-5xl mx-auto z-10 relative">
                    <!-- Partner 1 -->
                        <?php foreach($partners as $partner): ?>
                        <button type="button" onclick="window.open('<?= $partner['partner_url'] ?>', '_blank')" class="partner-card bg-white p-6 rounded-xl shadow-md flex items-center justify-center h-32 opacity-0">
                            <img src="<?= $partner['partner_logo'] ?>" alt="<?= $partner['partner_name'] ?>" class="max-h-16 object-contain">
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Newsletter Signup -->
    <?= $this->include('frontendV2/website/pages/constants/newsletter-subscription') ?>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    // GSAP Animations
    document.addEventListener('DOMContentLoaded', () => {
        // Hero section animations
        gsap.from('.hero-title', {
            duration: 1,
            y: 50,
            opacity: 0,
            ease: 'power3.out',
            stagger: 0.2
        });

        gsap.from('.hero-subtitle', {
            duration: 1,
            y: 30,
            opacity: 0,
            delay: 0.3,
            ease: 'power3.out'
        });

        gsap.from('.hero-button-1', {
            duration: 0.8,
            y: 20,
            opacity: 0,
            delay: 0.6,
            ease: 'back.out'
        });

        gsap.from('.hero-button-2', {
            duration: 0.8,
            y: 20,
            opacity: 0,
            delay: 0.7,
            ease: 'back.out'
        });

        gsap.from('.stat-item', {
            duration: 1,
            y: 30,
            opacity: 0,
            delay: 0.9,
            ease: 'power3.out',
            stagger: 0.2
        });

        // Register ScrollTrigger plugin
        gsap.registerPlugin(ScrollTrigger);
            
        // Partner cards animation
        gsap.utils.toArray('.partner-card').forEach((card, index) => {
            gsap.fromTo(card, 
                { 
                    opacity: 0,
                    y: 50,
                    scale: 0.8
                },
                { 
                    opacity: 1,
                    y: 0,
                    scale: 1,
                    duration: 0.6,
                    ease: "back.out(1.2)",
                    scrollTrigger: {
                        trigger: "#partners-section",
                        start: "top 70%",
                        toggleActions: "play none none none"
                    },
                    delay: index * 0.1
                }
            );
            
            // Hover effect
            card.addEventListener('mouseenter', () => {
                gsap.to(card, {
                    scale: 1.05,
                    boxShadow: "0 10px 25px -5px rgba(0,0,0,0.1)",
                    duration: 0.3
                });
            });
            
            card.addEventListener('mouseleave', () => {
                gsap.to(card, {
                    scale: 1,
                    boxShadow: "0 4px 6px -1px rgba(0,0,0,0.1)",
                    duration: 0.3
                });
            });
        });
        
        // Continuous subtle pulse animation
        gsap.to(".partner-card", {
            y: -5,
            duration: 2,
            repeat: -1,
            yoyo: true,
            ease: "sine.inOut",
            stagger: {
                each: 0.2,
                from: "random"
            }
        });
    });
</script>
<?= $this->endSection() ?>