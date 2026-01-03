<?php 
    use App\Models\SocialLink;

    $socialLink     = new SocialLink();
    $socialLinks    = $socialLink->getSocialLinks();

    $uri = service('uri');
    $segments       = $uri->getSegments();

    $facebook       = $socialLinks['facebook'];
    $twitter        = $socialLinks['twitter'];
    $instagram      = $socialLinks['instagram'];
    $linkedin       = $socialLinks['linkedin'];
    $youtube        = $socialLinks['youtube'];
?>

<!-- Footer -->
<footer class="bg-slate-800 text-white">
    <div class="container mx-auto px-4 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Brand -->
            <div class="lg:col-span-1">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-12 h-12 bg-secondaryShades-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-xl">K</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">KEWASNET</h3>
                        <p class="text-sm text-slate-400">Knowledge Network</p>
                    </div>
                </div>
                <p class="text-slate-300 text-sm leading-relaxed mb-6">
                    Leading Kenya's WASH sector through knowledge sharing, capacity building, 
                    and strategic advocacy for sustainable water and sanitation solutions.
                </p>
                <div class="flex space-x-4">
                    <a href="<?= $facebook ?>" class="text-slate-400 hover:text-secondaryShades-400 transition-colors">
                        <i data-lucide="facebook" class="icon-lg"></i>
                    </a>
                    <a href="<?= $twitter ?>" class="text-slate-400 hover:text-secondaryShades-400 transition-colors">
                        <i data-lucide="twitter" class="icon-lg"></i>
                    </a>
                    <a href="<?= $linkedin ?>" class="text-slate-400 hover:text-secondaryShades-400 transition-colors">
                        <i data-lucide="linkedin" class="icon-lg"></i>
                    </a>
                    <a href="<?= $youtube ?>" class="text-slate-400 hover:text-secondaryShades-400 transition-colors">
                        <i data-lucide="youtube" class="icon-lg"></i>
                    </a>
                    <a href="<?= $instagram ?>" class="text-slate-400 hover:text-secondaryShades-400 transition-colors">
                        <i data-lucide="instagram" class="icon-lg"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-lg font-semibold mb-6">Quick Links</h4>
                <ul class="space-y-3">
                    <li><a href="<?= base_url('about') ?>" class="text-slate-300 hover:text-secondaryShades-400 transition-colors text-sm">About KEWASNET</a></li>
                    <li><a href="<?= base_url('programs') ?>" class="text-slate-300 hover:text-secondaryShades-400 transition-colors text-sm">Our Programs</a></li>
                    <li><a href="<?= base_url('ksp') ?>" class="text-slate-300 hover:text-secondaryShades-400 transition-colors text-sm">Knowledge Platform</a></li>
                    <!-- <li><a href="<?= base_url('membership') ?>" class="text-slate-300 hover:text-secondaryShades-400 transition-colors text-sm">Membership</a></li>
                    <li><a href="<?= base_url('partners') ?>" class="text-slate-300 hover:text-secondaryShades-400 transition-colors text-sm">Partners</a></li> -->
                </ul>
            </div>

            <!-- Resources -->
            <div>
                <h4 class="text-lg font-semibold mb-6">Resources</h4>
                <ul class="space-y-3">
                    <li><a href="<?= base_url('/policy-briefs') ?>" class="text-slate-300 hover:text-secondaryShades-400 transition-colors text-sm">Policy Briefs</a></li>
                    <!-- <li><a href="<?= base_url('/research-reports') ?>" class="text-slate-300 hover:text-secondaryShades-400 transition-colors text-sm">Research Reports</a></li>
                    <li><a href="<?= base_url('/training-materials') ?>" class="text-slate-300 hover:text-secondaryShades-400 transition-colors text-sm">Training Materials</a></li> -->
                    <li><a href="<?= base_url('/best-practices') ?>" class="text-slate-300 hover:text-secondaryShades-400 transition-colors text-sm">Best Practices</a></li>
                    <li><a href="<?= base_url('/faq') ?>" class="text-slate-300 hover:text-secondaryShades-400 transition-colors text-sm">FAQs</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h4 class="text-lg font-semibold mb-6">Contact Us</h4>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <i data-lucide="map-pin" class="icon-lg text-secondaryShades-400 mt-0.5 flex-shrink-0"></i>
                        <div class="text-slate-300 text-sm">
                            <p>Mango Court , 4th Floor , Suite A403. WoodAvenue.</p>
                            <p>Nairobi, Kenya</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i data-lucide="phone" class="icon-lg text-secondaryShades-400 flex-shrink-0"></i>
                        <span class="text-slate-300 text-sm">+254 (705)- 530-499</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i data-lucide="mail" class="icon-lg text-secondaryShades-400 flex-shrink-0"></i>
                        <span class="text-slate-300 text-sm">info@kewasnet.co.ke</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-700 mt-12 pt-8">
            <div class="flex flex-col md:flex-row justify-between sm:items-center">
                <p class="text-slate-400 text-sm">
                    © 2010 - <?= date("Y") ?> KEWASNET. All rights reserved.
                </p>
                <div class="flex flex-col space-y-6 sm:space-y-0 sm:flex-row sm:space-x-6 mt-4 md:mt-0">
                    <a href="<?= base_url('/privacy-and-policies') ?>" class="text-slate-400 hover:text-secondaryShades-400 transition-colors text-sm">Privacy Policy</a>
                    <a href="<?= base_url('/terms-of-service') ?>" class="text-slate-400 hover:text-secondaryShades-400 transition-colors text-sm">Terms of Service</a>
                    <a href="<?= base_url('/cookies') ?>" class="text-slate-400 hover:text-secondaryShades-400 transition-colors text-sm">Cookies Policy</a>
                    <a href="<?= base_url('/sitemap') ?>" class="text-slate-400 hover:text-secondaryShades-400 transition-colors text-sm">Sitemap</a>
                </div>
            </div>
        </div>
    </div>
</footer>