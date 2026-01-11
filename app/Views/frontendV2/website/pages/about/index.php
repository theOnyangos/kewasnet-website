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

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-primary to-secondary text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl font-bold mb-6">About KEWASNET</h1>
            <p class="text-xl max-w-3xl mx-auto">
                Kenya's premier knowledge-sharing network driving excellence in Water, Sanitation, and Hygiene
            </p>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-10 items-center">
                <div>
                    <h2 class="text-4xl font-bold mb-6 text-primaryShades-800">Our Mission</h2>
                    <p class="text-lg text-dark mb-8">
                        To facilitate knowledge sharing, capacity building, and collaborative action among WASH sector stakeholders in Kenya, driving sustainable solutions for improved water, sanitation, and hygiene services for all.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 bg-primary rounded-full flex items-center justify-center mt-1">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <p class="text-dark">Facilitate knowledge exchange and learning</p>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 bg-primary rounded-full flex items-center justify-center mt-1">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <p class="text-dark">Build capacity across the WASH sector</p>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 bg-primary rounded-full flex items-center justify-center mt-1">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <p class="text-dark">Promote sustainable WASH solutions</p>
                        </div>
                    </div>
                </div>
                <div class="bg-secondaryShades-100 rounded-lg p-8">
                    <h3 class="text-3xl font-bold mb-6 text-primaryShades-800">Our Vision</h3>
                    <p class="text-lg text-dark">
                        A Kenya where all people have sustainable access to safe water, adequate sanitation, and practice good hygiene, supported by an informed and collaborative WASH sector.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Values -->
    <section class="py-20 bg-light">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-6 text-primaryShades-800">Our Core Values</h2>
                <p class="text-xl text-dark max-w-3xl mx-auto">
                    The principles that guide our work and define our commitment to the WASH sector
                </p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-20 h-20 bg-primary rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-primaryShades-800">Collaboration</h3>
                    <p class="text-dark">
                        We believe in the power of partnership and collective action to achieve sustainable WASH outcomes.
                    </p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 bg-secondary rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-primaryShades-800">Excellence</h3>
                    <p class="text-dark">
                        We strive for the highest standards in all our work, continuously improving our services and impact.
                    </p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 bg-dark rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-primaryShades-800">Innovation</h3>
                    <p class="text-dark">
                        We embrace new ideas, technologies, and approaches to address evolving WASH challenges.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Story -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-4xl font-bold mb-8 text-center text-primaryShades-800">Our Story</h2>
                <div class="space-y-6 text-lg text-dark">
                    <p>
                        KEWASNET was established in response to the growing need for coordinated knowledge sharing and collaboration in Kenya's Water, Sanitation, and Hygiene (WASH) sector. Recognizing that sustainable solutions require collective wisdom and shared learning, our founders envisioned a platform that would bring together diverse stakeholders to drive sector-wide improvements.
                    </p>
                    <p>
                        Since our inception, we have grown from a small network of passionate professionals to Kenya's leading knowledge-sharing platform for the WASH sector. Our journey has been marked by significant milestones, including the development of our comprehensive Knowledge Sharing Platform, the facilitation of numerous sector dialogues, and the production of influential policy briefs that have shaped national WASH strategies.
                    </p>
                    <p>
                        Today, KEWASNET serves as a critical bridge between research and practice, policy and implementation, bringing together government agencies, development partners, civil society organizations, private sector players, and academic institutions. Our work continues to evolve, always guided by our commitment to creating a Kenya where everyone has access to safe water, adequate sanitation, and practices good hygiene.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-20 bg-light">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-6 text-primaryShades-800">Leadership Team</h2>
                <p class="text-xl text-dark max-w-3xl mx-auto">
                    Meet the dedicated professionals driving KEWASNET's mission forward
                </p>
            </div>
            <!-- Team Grid Container -->
            <div class="grid md:grid-cols-3 gap-8">
                <?php if (!empty($leadershipMembers)): ?>
                    <?php foreach ($leadershipMembers as $index => $member): ?>
                        <div class="team-member bg-white rounded-lg p-8 text-center shadow-lg transform transition-all duration-300 hover:scale-105 hover:shadow-xl" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                            <div class="relative mb-6">
                                <div class="w-32 h-32 mx-auto mb-4 relative overflow-hidden rounded-full border-4 border-primary/20">
                                    <?php if (!empty($member['image'])): ?>
                                        <img src="<?= esc($member['image']) ?>" alt="<?= esc($member['name']) ?>" class="w-full h-full object-cover" 
                                             onerror="this.parentElement.innerHTML='<div class=\\'w-full h-full bg-lightGray flex items-center justify-center\\'>
                                                 <svg class=\\'w-16 h-16 text-primary/50\\' fill=\\'currentColor\\' viewBox=\\'0 0 20 20\\'>
                                                     <path fill-rule=\\'evenodd\\' d=\\'M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z\\' clip-rule=\\'evenodd\\' />
                                                 </svg>
                                             </div>'">
                                    <?php else: ?>
                                        <div class="w-full h-full bg-lightGray flex items-center justify-center">
                                            <svg class="w-16 h-16 text-primary/50" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($member['experience'])): ?>
                                    <div class="absolute top-2 right-2">
                                        <span class="inline-block bg-primary text-white text-xs px-2 py-1 rounded-full">
                                            <?= esc($member['experience']) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <h3 class="text-xl font-bold mb-2 text-primaryShades-800"><?= esc($member['name']) ?></h3>
                            <p class="text-secondary font-semibold mb-4"><?= esc($member['position'] ?? '') ?></p>
                            <?php if (!empty($member['description'])): ?>
                                <p class="text-dark text-sm mb-6 leading-relaxed"><?= esc($member['description']) ?></p>
                            <?php endif; ?>
                            
                            <!-- Social Links -->
                            <?php if (!empty($member['social_media']) && is_array($member['social_media'])): ?>
                                <div class="flex justify-center space-x-4 flex-wrap">
                                    <?php foreach ($member['social_media'] as $social): ?>
                                        <?php 
                                        $platform = strtolower($social['platform'] ?? '');
                                        $url = $social['url'] ?? '';
                                        
                                        // Determine icon name and colors based on platform
                                        $iconName = 'link';
                                        $bgClass = 'bg-primary hover:bg-primary/90';
                                        
                                        switch ($platform) {
                                            case 'linkedin':
                                                $iconName = 'linkedin';
                                                $bgClass = 'bg-blue-600 hover:bg-blue-700';
                                                break;
                                            case 'github':
                                                $iconName = 'github';
                                                $bgClass = 'bg-gray-800 hover:bg-gray-900';
                                                break;
                                            case 'twitter':
                                            case 'x':
                                                $iconName = 'twitter';
                                                $bgClass = 'bg-black hover:bg-gray-800';
                                                break;
                                            case 'facebook':
                                                $iconName = 'facebook';
                                                $bgClass = 'bg-blue-700 hover:bg-blue-800';
                                                break;
                                            case 'instagram':
                                                $iconName = 'instagram';
                                                $bgClass = 'bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600';
                                                break;
                                            case 'youtube':
                                                $iconName = 'youtube';
                                                $bgClass = 'bg-red-600 hover:bg-red-700';
                                                break;
                                            case 'whatsapp':
                                                $iconName = 'message-circle';
                                                $bgClass = 'bg-green-500 hover:bg-green-600';
                                                break;
                                            case 'website':
                                                $iconName = 'globe';
                                                $bgClass = 'bg-gray-600 hover:bg-gray-700';
                                                break;
                                            default:
                                                $iconName = 'link';
                                                $bgClass = 'bg-primary hover:bg-primary/90';
                                        }
                                        
                                        if (!empty($url)):
                                        ?>
                                            <a href="<?= esc($url) ?>" target="_blank" rel="noopener noreferrer" 
                                               class="inline-flex items-center justify-center w-10 h-10 <?= $bgClass ?> text-white rounded-full transition-colors duration-300"
                                               title="<?= esc($social['platform'] ?? '') ?>">
                                                <i data-lucide="<?= $iconName ?>" class="w-5 h-5"></i>
                                            </a>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-8">
                        <p class="text-dark">No leadership team members available at this time.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Add hover effects for team members
    $(document).ready(function() {
        $('.team-member').hover(
            function() {
                $(this).addClass('shadow-2xl');
            },
            function() {
                $(this).removeClass('shadow-2xl');
            }
        );
    });
</script>
<?= $this->endSection() ?>
