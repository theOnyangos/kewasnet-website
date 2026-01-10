<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('frontendV2/ksp/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
<!-- Hero Section -->
    <section class="school-header py-20 text-white">
        <div class="container mx-auto px-4 text-center">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold mb-6">Welcome to the KEWASNET Learning Hub</h1>
                <p class="text-xl md:text-2xl mb-8 leading-relaxed">
                    Expand your knowledge with our comprehensive courses on water, sanitation, and environmental management.
                </p>
                
                <!-- Search Box -->
                <div class="max-w-2xl mx-auto mt-10">
                    <div class="search-box bg-white rounded-full p-2 flex items-center">
                        <input type="text" placeholder="Search courses, topics, or keywords..." 
                               class="flex-1 px-6 py-3 border-0 focus:ring-0 focus:outline-none rounded-full">
                        <button class="gradient-btn p-3 rounded-full text-white">
                            <i data-lucide="search" class="w-5 h-5"></i>
                        </button>
                    </div>
                    <p class="text-sm text-slate-300 mt-3">Try: "water conservation" or "climate adaptation"</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Breadcrumb -->
    <div class="sticky top-[110px] z-40 bg-white border-b borderColor shadow-sm">
        <div class="container mx-auto px-4 py-3">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="/" class="inline-flex items-center text-sm font-medium text-slate-600 hover:text-primary">
                            <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                            Home
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                            <span class="ml-1 text-sm font-medium text-primary md:ml-2">Learning Hub</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Course Statistics Section -->
    <section class="bg-gradient-to-r from-primary to-secondary py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-white">
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold mb-2"><?= $stats['total_courses'] ?? '89' ?></div>
                    <div class="text-white/90 text-sm md:text-base">Total Courses</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold mb-2"><?= $stats['total_students'] ?? '2,500+' ?></div>
                    <div class="text-white/90 text-sm md:text-base">Active Learners</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold mb-2"><?= $stats['total_instructors'] ?? '35' ?></div>
                    <div class="text-white/90 text-sm md:text-base">Expert Instructors</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold mb-2"><?= $stats['certificates_issued'] ?? '1,200+' ?></div>
                    <div class="text-white/90 text-sm md:text-base">Certificates Issued</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-12">
        <!-- Diagonal Grid Pattern -->
        <div class="events-page-pattern-bg"></div>

        <!-- Pillars Section -->
        <section class="mb-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-dark mb-4">Explore Our Learning Pillars</h2>
                <p class="text-lg text-slate-600 max-w-3xl mx-auto">
                    Our courses are organized around five key thematic areas that form the foundation of our knowledge platform.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <!-- WASH -->
                <a href="#" class="pillar-category bg-white rounded-lg overflow-hidden shadow-sm border borderColor">
                    <div class="p-6 text-center">
                        <div class="bg-secondaryShades-100 p-4 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="droplets" class="w-10 h-10 text-secondary"></i>
                        </div>
                        <h3 class="text-xl font-bold text-dark mb-3">WASH</h3>
                        <p class="text-slate-600 mb-4">
                            Water, Sanitation and Hygiene courses covering best practices and innovative solutions.
                        </p>
                        <span class="inline-block bg-secondaryShades-100 text-secondary text-sm px-3 py-1 rounded-full">
                            24 Courses
                        </span>
                    </div>
                </a>
                
                <!-- Climate Change -->
                <a href="#" class="pillar-category bg-white rounded-lg overflow-hidden shadow-sm border borderColor">
                    <div class="p-6 text-center">
                        <div class="bg-secondaryShades-100 p-4 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="cloud-sun" class="w-10 h-10 text-secondary"></i>
                        </div>
                        <h3 class="text-xl font-bold text-dark mb-3">Climate Change</h3>
                        <p class="text-slate-600 mb-4">
                            Understanding climate impacts and adaptation strategies for water systems.
                        </p>
                        <span class="inline-block bg-secondaryShades-100 text-secondary text-sm px-3 py-1 rounded-full">
                            18 Courses
                        </span>
                    </div>
                </a>
                
                <!-- Governance -->
                <a href="#" class="pillar-category bg-white rounded-lg overflow-hidden shadow-sm border borderColor">
                    <div class="p-6 text-center">
                        <div class="bg-secondaryShades-100 p-4 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="scale" class="w-10 h-10 text-secondary"></i>
                        </div>
                        <h3 class="text-xl font-bold text-dark mb-3">Governance</h3>
                        <p class="text-slate-600 mb-4">
                            Policy, regulation and institutional frameworks for effective water management.
                        </p>
                        <span class="inline-block bg-secondaryShades-100 text-secondary text-sm px-3 py-1 rounded-full">
                            15 Courses
                        </span>
                    </div>
                </a>
                
                <!-- NEXUS -->
                <a href="#" class="pillar-category bg-white rounded-lg overflow-hidden shadow-sm border borderColor">
                    <div class="p-6 text-center">
                        <div class="bg-secondaryShades-100 p-4 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="link" class="w-10 h-10 text-secondary"></i>
                        </div>
                        <h3 class="text-xl font-bold text-dark mb-3">NEXUS</h3>
                        <p class="text-slate-600 mb-4">
                            Integrated approaches to water, energy and food security.
                        </p>
                        <span class="inline-block bg-secondaryShades-100 text-secondary text-sm px-3 py-1 rounded-full">
                            12 Courses
                        </span>
                    </div>
                </a>
                
                <!-- IWRM -->
                <a href="#" class="pillar-category bg-white rounded-lg overflow-hidden shadow-sm border borderColor">
                    <div class="p-6 text-center">
                        <div class="bg-secondaryShades-100 p-4 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="globe" class="w-10 h-10 text-secondary"></i>
                        </div>
                        <h3 class="text-xl font-bold text-dark mb-3">IWRM</h3>
                        <p class="text-slate-600 mb-4">
                            Integrated Water Resources Management principles and applications.
                        </p>
                        <span class="inline-block bg-secondaryShades-100 text-secondary text-sm px-3 py-1 rounded-full">
                            20 Courses
                        </span>
                    </div>
                </a>
            </div>
        </section>
        
        <!-- Featured Courses -->
        <section class="mb-16">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <h2 class="text-3xl font-bold text-dark">Featured Courses</h2>
                <div class="flex space-x-4">
                    <a href="<?= base_url('ksp/learning-hub/courses') ?>" class="text-primary font-medium flex items-center hover:text-primary-dark transition-colors">
                        View all courses
                        <i data-lucide="arrow-right" class="ml-2 w-5 h-5"></i>
                    </a>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Course 1 -->
                <div class="course-card bg-white rounded-lg overflow-hidden">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1606768666853-403c90a981ad?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" 
                             alt="Course Image" class="w-full h-48 object-cover">
                        <span class="absolute top-4 right-4 bg-primary text-white text-xs px-2 py-1 rounded-full">
                            WASH
                        </span>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center text-sm text-slate-500 mb-2">
                            <i data-lucide="clock" class="w-4 h-4 mr-1"></i>
                            <span>8 Hours</span>
                            <span class="mx-2">•</span>
                            <i data-lucide="book-open" class="w-4 h-4 mr-1"></i>
                            <span>12 Lessons</span>
                        </div>
                        <h3 class="text-xl font-bold text-dark mb-3">Community-Led Total Sanitation</h3>
                        <p class="text-slate-600 mb-4">
                            Learn participatory approaches to improve sanitation behaviors and eliminate open defecation in rural communities.
                        </p>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Instructor" class="w-8 h-8 rounded-full mr-2">
                                <span class="text-sm text-slate-600">Dr. Amina Mohamed</span>
                            </div>
                            <a href="#" class="text-primary font-medium flex items-center text-sm">
                                Enroll Now
                                <i data-lucide="arrow-right" class="ml-1 w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Course 2 -->
                <div class="course-card bg-white rounded-lg overflow-hidden">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1613771404721-1f92d799e49f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" 
                             alt="Course Image" class="w-full h-48 object-cover">
                        <span class="absolute top-4 right-4 bg-secondary text-white text-xs px-2 py-1 rounded-full">
                            Climate Change
                        </span>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center text-sm text-slate-500 mb-2">
                            <i data-lucide="clock" class="w-4 h-4 mr-1"></i>
                            <span>12 Hours</span>
                            <span class="mx-2">•</span>
                            <i data-lucide="book-open" class="w-4 h-4 mr-1"></i>
                            <span>18 Lessons</span>
                        </div>
                        <h3 class="text-xl font-bold text-dark mb-3">Climate-Resilient Water Systems</h3>
                        <p class="text-slate-600 mb-4">
                            Design and implement water systems that can withstand climate variability and extreme weather events.
                        </p>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <img src="https://randomuser.me/api/portraits/men/45.jpg" alt="Instructor" class="w-8 h-8 rounded-full mr-2">
                                <span class="text-sm text-slate-600">Prof. James Kariuki</span>
                            </div>
                            <a href="#" class="text-primary font-medium flex items-center text-sm">
                                Enroll Now
                                <i data-lucide="arrow-right" class="ml-1 w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Course 3 -->
                <div class="course-card bg-white rounded-lg overflow-hidden">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1450101499163-c8848c66ca85?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" 
                             alt="Course Image" class="w-full h-48 object-cover">
                        <span class="absolute top-4 right-4 bg-primaryShades-800 text-white text-xs px-2 py-1 rounded-full">
                            IWRM
                        </span>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center text-sm text-slate-500 mb-2">
                            <i data-lucide="clock" class="w-4 h-4 mr-1"></i>
                            <span>10 Hours</span>
                            <span class="mx-2">•</span>
                            <i data-lucide="book-open" class="w-4 h-4 mr-1"></i>
                            <span>15 Lessons</span>
                        </div>
                        <h3 class="text-xl font-bold text-dark mb-3">River Basin Management</h3>
                        <p class="text-slate-600 mb-4">
                            Comprehensive approaches to managing water resources at the river basin scale for sustainable development.
                        </p>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <img src="https://randomuser.me/api/portraits/women/56.jpg" alt="Instructor" class="w-8 h-8 rounded-full mr-2">
                                <span class="text-sm text-slate-600">Dr. Wanjiru Mwangi</span>
                            </div>
                            <a href="#" class="text-primary font-medium flex items-center text-sm">
                                Enroll Now
                                <i data-lucide="arrow-right" class="ml-1 w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Learning Experience Section -->
        <section class="mb-16 bg-white rounded-xl overflow-hidden border borderColor">
            <div class="flex flex-col lg:flex-row">
                <div class="lg:w-1/2 p-8 md:p-12">
                    <h2 class="text-3xl font-bold text-dark mb-6">Our Learning Experience</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="bg-secondaryShades-100 p-3 rounded-full flex-shrink-0">
                                <i data-lucide="monitor" class="w-6 h-6 text-secondary"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-dark mb-2">Interactive Modules</h3>
                                <p class="text-slate-600">
                                    Engaging multimedia content with videos, quizzes, and interactive exercises to enhance learning.
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="bg-secondaryShades-100 p-3 rounded-full flex-shrink-0">
                                <i data-lucide="award" class="w-6 h-6 text-secondary"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-dark mb-2">Certification</h3>
                                <p class="text-slate-600">
                                    Earn recognized certificates upon course completion to showcase your expertise.
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="bg-secondaryShades-100 p-3 rounded-full flex-shrink-0">
                                <i data-lucide="users" class="w-6 h-6 text-secondary"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-dark mb-2">Expert Instructors</h3>
                                <p class="text-slate-600">
                                    Learn from leading practitioners and academics in the water and sanitation sector.
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="bg-secondaryShades-100 p-3 rounded-full flex-shrink-0">
                                <i data-lucide="calendar" class="w-6 h-6 text-secondary"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-dark mb-2">Self-Paced Learning</h3>
                                <p class="text-slate-600">
                                    Study at your own convenience with 24/7 access to all course materials.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="lg:w-1/2 bg-slate-50 p-8 md:p-12 flex items-center justify-center">
                    <div class="notebook-paper max-w-md w-full rounded-lg border borderColor">
                        <h3 class="font-bold text-lg mb-4">Today's Learning Objectives</h3>
                        <ul class="space-y-2">
                            <li class="flex items-start ">
                                <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5"></i>
                                <span>Understand the principles of integrated water resources management</span>
                            </li>
                            <li class="flex items-start">
                                <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5"></i>
                                <span>Explore climate change adaptation strategies for water utilities</span>
                            </li>
                            <li class="flex items-start">
                                <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5"></i>
                                <span>Learn participatory approaches to community water management</span>
                            </li>
                            <li class="flex items-start">
                                <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5"></i>
                                <span>Analyze case studies of successful WASH programs</span>
                            </li>
                            <li class="flex items-start">
                                <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5"></i>
                                <span>Develop skills for water policy analysis and advocacy</span>
                            </li>
                        </ul>
                        
                        <div class="mt-8 pt-4 border-t borderColor">
                            <h3 class="font-bold text-lg mb-4">Upcoming Assignments</h3>
                            <div class="flex items-center justify-between py-2">
                                <span>Case Study Analysis</span>
                                <span class="text-sm text-slate-500">Due Friday</span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span>Quiz: Water Governance</span>
                                <span class="text-sm text-slate-500">Due Next Monday</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Testimonials -->
        <section class="mb-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-dark mb-4">What Our Learners Say</h2>
                <p class="text-lg text-slate-600 max-w-3xl mx-auto">
                    Hear from professionals who have enhanced their skills through our courses.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg border borderColor">
                    <div class="flex items-center mb-4">
                        <img src="https://randomuser.me/api/portraits/women/45.jpg" alt="Learner" class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <h4 class="font-bold">Sarah Johnson</h4>
                            <p class="text-sm text-slate-600">WASH Coordinator</p>
                        </div>
                    </div>
                    <p class="text-slate-600 mb-4">
                        "The Community-Led Total Sanitation course transformed our approach to hygiene promotion. We've seen a 40% reduction in open defecation in our target communities."
                    </p>
                    <div class="flex text-yellow-400">
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg border borderColor">
                    <div class="flex items-center mb-4">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Learner" class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <h4 class="font-bold">David Ochieng</h4>
                            <p class="text-sm text-slate-600">Water Engineer</p>
                        </div>
                    </div>
                    <p class="text-slate-600 mb-4">
                        "The IWRM courses provided practical tools I've applied in developing our county's water master plan. The certification helped me secure a promotion."
                    </p>
                    <div class="flex text-yellow-400">
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg border borderColor">
                    <div class="flex items-center mb-4">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Learner" class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <h4 class="font-bold">Grace Mwende</h4>
                            <p class="text-sm text-slate-600">Climate Officer</p>
                        </div>
                    </div>
                    <p class="text-slate-600 mb-4">
                        "The climate change modules helped our team develop more resilient water projects. The self-paced format allowed me to balance learning with work commitments."
                    </p>
                    <div class="flex text-yellow-400">
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Call to Action -->
    <section class="bg-gradient-to-r from-primary to-secondary overflow-hidden p-8 md:p-12 text-white">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl font-bold mb-6">Start Learning Today</h2>
            <p class="text-xl mb-8 leading-relaxed">
                Join thousands of water and sanitation professionals enhancing their skills through our comprehensive courses.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="<?= base_url('ksp/learning-hub') ?>" class="bg-white px-8 py-3 rounded-[50px] text-primary font-medium text-lg flex items-center hover:bg-slate-50 transition-colors">
                    Browse Courses
                    <i data-lucide="arrow-right" class="ml-2 w-5 h-5"></i>
                </a>
                <a href="<?= base_url('ksp/signup') ?>" class="border-2 border-white px-8 py-3 rounded-[50px] text-white font-medium text-lg flex items-center hover:bg-white hover:text-primary transition-colors">
                    Create Account
                    <i data-lucide="user-plus" class="ml-2 w-5 h-5"></i>
                </a>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Initialize GSAP animations
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof gsap !== 'undefined') {
            gsap.registerPlugin(ScrollTrigger);
            
            // Animate pillar categories
            gsap.utils.toArray(".pillar-category").forEach((card, i) => {
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
            
            // Animate course cards
            gsap.utils.toArray(".course-card").forEach((card, i) => {
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
            
            // Animate testimonials
            gsap.utils.toArray(".bg-white.p-6.rounded-lg").forEach((item, i) => {
                gsap.from(item, {
                    scrollTrigger: {
                        trigger: item,
                        start: "top 80%",
                        toggleActions: "play none none none"
                    },
                    y: 30,
                    opacity: 0,
                    duration: 0.4,
                    delay: i * 0.1,
                    ease: "power2.out"
                });
            });
        }
    });
</script>
<?= $this->endSection() ?>