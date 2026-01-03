<?php 
    use App\Helpers\UrlHelper;
    use Carbon\Carbon;

    $currentUrl = new UrlHelper();

    // dd($recentDiscussions);

    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('frontendV2/ksp/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <div class="bg-light">
        <!-- Hero Section -->
        <section class="relative py-20 bg-gradient-to-r from-primary to-secondary">
            <div class="container mx-auto px-4 text-center text-white">
                <div class="max-w-4xl mx-auto">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6">Welcome to Our Networking Corner!</h1>
                    <p class="text-xl md:text-2xl mb-8 leading-relaxed">
                        Discover a vibrant space where the KEWASNET community comes together to engage in meaningful discussions and share valuable insights.
                    </p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="<?= base_url('ksp/networking-corner/forums') ?>" class="bg-white px-8 py-3 rounded-[50px] text-primary font-medium text-lg flex items-center">
                            Explore Forums
                            <i data-lucide="arrow-right" class="ml-2 w-5 h-5"></i>
                        </a>
                        <a href="<?= base_url('ksp/signup') ?>" class="border-2 border-white px-8 py-3 rounded-[50px] text-white font-medium text-lg flex items-center">
                            Join Community
                            <i data-lucide="users" class="ml-2 w-5 h-5"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Breadcrumb -->
        <div class="bg-white border-b borderColor">
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
                                <span class="ml-1 text-sm font-medium text-primary md:ml-2">Networking Corner</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-12">
            <!-- What to Expect Section -->
            <section class="mb-16">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-dark mb-4">What to Expect in Our Community</h2>
                    <p class="text-lg text-slate-600 max-w-3xl mx-auto">
                        KEWASNET Networking Corner is a space for you to connect with other users, share your knowledge, and learn from others.
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Discussion Forums -->
                    <div class="network-card bg-white rounded-lg overflow-hidden">
                        <div class="p-6">
                            <div class="bg-secondaryShades-100 p-3 rounded-full w-12 h-12 flex items-center justify-center mb-4">
                                <i data-lucide="message-square" class="w-6 h-6 text-secondary"></i>
                            </div>
                            <h3 class="text-xl font-bold text-dark mb-3">Discussion Forums</h3>
                            <p class="text-slate-600 mb-4">
                                Explore topics like water conservation, sanitation practices, and sustainable solutions with like-minded individuals.
                            </p>
                            <!-- <a href="#" class="text-secondary font-medium flex items-center">
                                Browse Forums
                                <i data-lucide="arrow-right" class="ml-2 w-4 h-4"></i>
                            </a> -->
                        </div>
                    </div>
                    
                    <!-- Knowledge Sharing -->
                    <div class="network-card bg-white rounded-lg overflow-hidden">
                        <div class="p-6">
                            <div class="bg-secondaryShades-100 p-3 rounded-full w-12 h-12 flex items-center justify-center mb-4">
                                <i data-lucide="book-open" class="w-6 h-6 text-secondary"></i>
                            </div>
                            <h3 class="text-xl font-bold text-dark mb-3">Knowledge Sharing</h3>
                            <p class="text-slate-600 mb-4">
                                Share best practices, research findings, and real-world experiences with our expert community.
                            </p>
                            <!-- <a href="#" class="text-secondary font-medium flex items-center">
                                Share Knowledge
                                <i data-lucide="arrow-right" class="ml-2 w-4 h-4"></i>
                            </a> -->
                        </div>
                    </div>
                    
                    <!-- Event Announcements -->
                    <div class="network-card bg-white rounded-lg overflow-hidden">
                        <div class="p-6">
                            <div class="bg-secondaryShades-100 p-3 rounded-full w-12 h-12 flex items-center justify-center mb-4">
                                <i data-lucide="calendar" class="w-6 h-6 text-secondary"></i>
                            </div>
                            <h3 class="text-xl font-bold text-dark mb-3">Event Announcements</h3>
                            <p class="text-slate-600 mb-4">
                                Stay informed about upcoming workshops, webinars, and events in the water management sector.
                            </p>
                            <!-- <a href="#" class="text-secondary font-medium flex items-center">
                                View Events
                                <i data-lucide="arrow-right" class="ml-2 w-4 h-4"></i>
                            </a> -->
                        </div>
                    </div>
                    
                    <!-- Networking Opportunities -->
                    <div class="network-card bg-white rounded-lg overflow-hidden">
                        <div class="p-6">
                            <div class="bg-secondaryShades-100 p-3 rounded-full w-12 h-12 flex items-center justify-center mb-4">
                                <i data-lucide="network" class="w-6 h-6 text-secondary"></i>
                            </div>
                            <h3 class="text-xl font-bold text-dark mb-3">Networking Opportunities</h3>
                            <p class="text-slate-600 mb-4">
                                Connect with professionals and enthusiasts committed to improving water access and quality.
                            </p>
                            <!-- <a href="#" class="text-secondary font-medium flex items-center">
                                Connect Now
                                <i data-lucide="arrow-right" class="ml-2 w-4 h-4"></i>
                            </a> -->
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Forum Categories Section -->
            <?php if (!empty($recentForums)): ?>
            <section id="forums" class="mb-16">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                    <div class="text-center md:text-left mb-4 md:mb-0">
                        <h2 class="text-3xl font-bold text-dark mb-4">Discussion Forums</h2>
                        <p class="text-lg text-slate-600 max-w-3xl">
                            Join conversations in our specialized forums covering all aspects of water and sanitation.
                        </p>
                    </div>
                    <a href="<?= base_url('ksp/networking-corner/forums') ?>" class="text-primary font-medium flex items-center hover:text-secondary transition-colors">
                        View all forums
                        <i data-lucide="arrow-right" class="ml-2 w-5 h-5"></i>
                    </a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($recentForums as $forum): ?>
                        <div class="forum-category rounded-lg overflow-hidden border borderColor">
                            <div class="p-6">
                                <div class="flex items-center mb-4">
                                    <div class="bg-secondaryShades-100 p-3 rounded-full mr-4">
                                        <i data-lucide="<?= esc($forum->icon ?? 'message-circle') ?>" class="w-6 h-6 text-secondary"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-secondaryShades-100"><?= esc($forum->name ?? '') ?></h3>
                                </div>
                                <p class="text-slate-300 mb-4">
                                    <?= esc($forum->description ?? '') ?>
                                </p>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-slate-400"><?= esc(Carbon::parse($forum->created_at ?? '')->diffForHumans()) ?></span>
                                    <!-- onclick="handleOpenForum('<?= esc($forum->id) ?>', event)" -->
                                    <a href="<?= base_url('ksp/networking-corner-forum-discussion/'.$forum->slug) ?>" class="text-sm font-medium text-secondary flex items-center">
                                        View Forum
                                        <i data-lucide="arrow-right" class="w-6 h-6 text-secondary"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
            
            <!-- Active Discussions Section -->
            <section class="mb-16">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                    <h2 class="text-3xl font-bold text-dark">Active Discussions</h2>
                    <a href="<?= base_url('ksp/networking-corner-discussions') ?>" class="text-primary font-medium flex items-center hover:text-secondary transition-colors">
                        View all discussions
                        <i data-lucide="arrow-right" class="ml-2 w-5 h-5"></i>
                    </a>
                </div>

                <?php if (!empty($recentDiscussions)): ?>
                    <div class="bg-white rounded-lg shadow-sm border borderColor overflow-hidden">
                        <!-- Discussion 1 -->
                        <?php foreach ($recentDiscussions as $discussion): ?>
                            <a href="<?= session()->get('id') ?? session()->get('user_id') ? base_url('ksp/discussion/' . $discussion->slug . '/view') : base_url('ksp/login') ?>" class="discussion-card block border-b borderColor last:border-b-0 p-6 hover:bg-slate-50">
                                <div class="flex items-start space-x-4">
                                    <?php if (!empty($discussion->user_avatar)): ?>
                                        <img src="<?= esc($discussion->user_avatar) ?>" alt="User" class="w-10 h-10 rounded-full">
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-full mr-4 bg-secondary flex items-center justify-center text-white">
                                            <i data-lucide="user-circle" class="w-5 h-5"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-1">
                                        <h3 class="font-bold text-lg mb-1 hover:text-secondary"><?= esc($discussion->title ?? '') ?></h3>
                                        <p class="text-slate-600 mb-2 line-clamp-2"><?= $discussion->content ?? '' ?></p>
                                        <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500">
                                            <span><?= esc($discussion->forum_name ?? '') ?></span>
                                            <span>Posted by <?= esc($discussion->posted_by ?? '') ?></span>
                                            <span><?= esc(Carbon::parse($discussion->created_at ?? '')->diffForHumans()) ?></span>
                                            <span class="flex items-center">
                                                <i data-lucide="message-square" class="w-4 h-4 mr-1"></i>
                                                <?= esc($discussion->reply_count ?? 0) ?> replies
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
            
            <!-- Get Started Section -->
            <section class="mb-16">
                <div class="bg-gradient-to-r from-primary to-secondary rounded-xl overflow-hidden">
                    <div class="flex flex-col lg:flex-row">
                        <div class="lg:w-1/2 p-8 md:p-12 text-white">
                            <h2 class="text-3xl font-bold mb-4">Ready to Join the Conversation?</h2>
                            <p class="text-lg mb-6">
                                Become part of Kenya's largest network of water and sanitation professionals. Share your knowledge, ask questions, and connect with peers.
                            </p>
                            <div class="flex flex-wrap gap-4">
                                <a href="<?= base_url('ksp/signup') ?>" class="bg-white px-8 py-3 rounded-[50px] text-primary font-medium text-lg flex items-center">
                                    Sign Up Now
                                    <i data-lucide="user-plus" class="ml-2 w-5 h-5"></i>
                                </a>
                                <a href="#" class="border-2 border-white px-8 py-3 rounded-[50px] text-white font-medium text-lg flex items-center">
                                    Learn More
                                    <i data-lucide="info" class="ml-2 w-5 h-5"></i>
                                </a>
                            </div>
                        </div>
                        <div class="lg:w-1/2 bg-white/20 backdrop-blur-sm p-8 md:p-12 flex items-center justify-center">
                            <div class="max-w-md">
                                <h3 class="text-xl font-bold text-white mb-4">How to Get Started</h3>
                                <ul class="space-y-4">
                                    <li class="flex items-start space-x-3">
                                        <div class="bg-white/20 rounded-full p-2">
                                            <i data-lucide="user" class="w-5 h-5 text-white"></i>
                                        </div>
                                        <span class="text-white">Create your free account</span>
                                    </li>
                                    <li class="flex items-start space-x-3">
                                        <div class="bg-white/20 rounded-full p-2">
                                            <i data-lucide="edit" class="w-5 h-5 text-white"></i>
                                        </div>
                                        <span class="text-white">Complete your profile</span>
                                    </li>
                                    <li class="flex items-start space-x-3">
                                        <div class="bg-white/20 rounded-full p-2">
                                            <i data-lucide="message-square" class="w-5 h-5 text-white"></i>
                                        </div>
                                        <span class="text-white">Introduce yourself in our welcome forum</span>
                                    </li>
                                    <li class="flex items-start space-x-3">
                                        <div class="bg-white/20 rounded-full p-2">
                                            <i data-lucide="search" class="w-5 h-5 text-white"></i>
                                        </div>
                                        <span class="text-white">Explore topics that interest you</span>
                                    </li>
                                    <li class="flex items-start space-x-3">
                                        <div class="bg-white/20 rounded-full p-2">
                                            <i data-lucide="send" class="w-5 h-5 text-white"></i>
                                        </div>
                                        <span class="text-white">Start participating in discussions</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Community Guidelines Section -->
            <section class="bg-white rounded-lg shadow-sm border borderColor p-8">
                <h2 class="text-3xl font-bold text-dark mb-6 text-center">Community Guidelines</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                    <div class="text-center">
                        <div class="bg-secondaryShades-100 p-4 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="heart" class="w-8 h-8 text-secondary"></i>
                        </div>
                        <h3 class="font-bold mb-2">Be Respectful</h3>
                        <p class="text-slate-600 text-sm">Treat all members with courtesy and respect.</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-secondaryShades-100 p-4 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="briefcase" class="w-8 h-8 text-secondary"></i>
                        </div>
                        <h3 class="font-bold mb-2">Be Professional</h3>
                        <p class="text-slate-600 text-sm">Keep discussions relevant and professional.</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-secondaryShades-100 p-4 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="help-circle" class="w-8 h-8 text-secondary"></i>
                        </div>
                        <h3 class="font-bold mb-2">Be Helpful</h3>
                        <p class="text-slate-600 text-sm">Share knowledge and support others.</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-secondaryShades-100 p-4 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="bell" class="w-8 h-8 text-secondary"></i>
                        </div>
                        <h3 class="font-bold mb-2">Be Informed</h3>
                        <p class="text-slate-600 text-sm">Stay updated on community news.</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-secondaryShades-100 p-4 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="smile" class="w-8 h-8 text-secondary"></i>
                        </div>
                        <h3 class="font-bold mb-2">Be Yourself</h3>
                        <p class="text-slate-600 text-sm">Share your unique perspective.</p>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Join Forum Modal -->
    <div id="joinForumModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-11/12 md:w-1/2 lg:w-1/3 max-w-md mx-auto p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-dark">Join Forum</h3>
                <button onclick="closeJoinForumModal()" class="text-slate-500 hover:text-slate-700">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="mb-6">
                <p class="text-slate-600 mb-4">Would you like to join this forum to participate in discussions?</p>
                <div class="bg-primaryShades-50 p-4 rounded-lg">
                    <h4 id="forumModalTitle" class="font-medium text-primary mb-2"></h4>
                    <p id="forumModalDescription" class="text-sm text-slate-600"></p>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button onclick="closeJoinForumModal()" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50">
                    Cancel
                </button>
                <button id="joinForumBtn" onclick="joinForum()" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">
                    Join Forum
                </button>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Store the current forum ID
    let currentForumId = null;
    let currentForumTitle = '';
    let currentForumDescription = '';

    // Handle opening forum
    function handleOpenForum(forumId, event) {
        event.preventDefault();
        
        // Find the forum data
        <?php if (!empty($recentForums)): ?>
            <?php foreach ($recentForums as $forum): ?>
                if ('<?= $forum->id ?>' === forumId) {
                    currentForumId = forumId;
                    currentForumTitle = "<?= esc($forum->name ?? '') ?>";
                    currentForumDescription = "<?= esc($forum->description ?? '') ?>";
                    showJoinForumModal();
                    return;
                }
            <?php endforeach; ?>
        <?php endif; ?>
        
        // If forum not found, show error
        alert('Forum not found!');
    }

    // Show join forum modal
    function showJoinForumModal() {
        document.getElementById('forumModalTitle').textContent = currentForumTitle;
        document.getElementById('forumModalDescription').textContent = currentForumDescription;
        document.getElementById('joinForumModal').classList.remove('hidden');
    }

    // Close join forum modal
    function closeJoinForumModal() {
        document.getElementById('joinForumModal').classList.add('hidden');
        currentForumId = null;
    }

    // Join forum function
    function joinForum() {
        if (!currentForumId) {
            alert('No forum selected!');
            return;
        }
        
        const joinBtn = document.getElementById('joinForumBtn');
        const originalText = joinBtn.innerHTML;
        
        // Show loading state
        joinBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin mr-2"></i> Joining...';
        joinBtn.disabled = true;
        
        // Send request to server
        fetch('<?= base_url('ksp/join-forum') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                forum_id: currentForumId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success - close modal and redirect or show success message
                closeJoinForumModal();
                alert('Successfully joined the forum!');
                // Optionally redirect to the forum page
                // window.location.href = '<?= base_url('ksp/forum/') ?>' + currentForumId;
            } else {
                // Error
                alert(data.message || 'Failed to join forum. Please try again.');
                joinBtn.innerHTML = originalText;
                joinBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            joinBtn.innerHTML = originalText;
            joinBtn.disabled = false;
        });
    }

    // Initialize GSAP animations
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof gsap !== 'undefined') {
            gsap.registerPlugin(ScrollTrigger);
            
            // Animate network cards
            gsap.utils.toArray(".network-card").forEach((card, i) => {
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
            
            // Animate forum categories
            gsap.utils.toArray(".forum-category").forEach((category, i) => {
                gsap.from(category, {
                    scrollTrigger: {
                        trigger: category,
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
            
            // Animate discussion cards
            gsap.utils.toArray(".discussion-card").forEach((card, i) => {
                gsap.from(card, {
                    scrollTrigger: {
                        trigger: card,
                        start: "top 80%",
                        toggleActions: "play none none none"
                    },
                    y: 30,
                    opacity: 0,
                    duration: 0.4,
                    delay: i * 0.05,
                    ease: "power2.out"
                });
            });
        }
    });
</script>
<?= $this->endSection() ?>