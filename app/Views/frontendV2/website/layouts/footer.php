<?php 
    use App\Models\SocialLink;

    $socialLink     = new SocialLink();
    $socialLinks    = $socialLink->getSocialLinks();

    $uri = service('uri');
    $segments       = $uri->getSegments();

    // Safely get social links with fallback to empty string if null or key doesn't exist
    $facebook  = (!empty($socialLinks) && isset($socialLinks['facebook'])) ? $socialLinks['facebook'] : '';
    $twitter   = (!empty($socialLinks) && isset($socialLinks['twitter'])) ? $socialLinks['twitter'] : '';
    $instagram = (!empty($socialLinks) && isset($socialLinks['instagram'])) ? $socialLinks['instagram'] : '';
    $linkedin  = (!empty($socialLinks) && isset($socialLinks['linkedin'])) ? $socialLinks['linkedin'] : '';
    $youtube   = (!empty($socialLinks) && isset($socialLinks['youtube'])) ? $socialLinks['youtube'] : '';
?>

<!-- Footer -->
<footer class="bg-slate-800 text-white">
    <div class="container mx-auto px-4 sm:px-6 py-12 sm:py-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 sm:gap-10 lg:gap-8">
            <!-- Brand -->
            <div class="sm:col-span-2 lg:col-span-1">
                <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-secondaryShades-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold text-lg sm:text-xl">K</span>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold">KEWASNET</h3>
                        <p class="text-xs sm:text-sm text-slate-400">Knowledge Network</p>
                    </div>
                </div>
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed mb-4 sm:mb-6">
                    Leading Kenya's WASH sector through knowledge sharing, capacity building, 
                    and strategic advocacy for sustainable water and sanitation solutions.
                </p>
                <?php if (!empty($facebook) || !empty($twitter) || !empty($linkedin) || !empty($youtube) || !empty($instagram)): ?>
                    <div class="flex space-x-3 sm:space-x-4">
                        <?php if (!empty($facebook)): ?>
                            <a href="<?= esc($facebook) ?>" target="_blank" class="w-8 h-8 sm:w-10 sm:h-10 bg-slate-700 hover:bg-primary rounded-lg flex items-center justify-center text-slate-300 hover:text-white transition-colors flex-shrink-0">
                                <i data-lucide="facebook" class="icon-sm sm:icon"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($twitter)): ?>
                            <a href="<?= esc($twitter) ?>" target="_blank" class="w-8 h-8 sm:w-10 sm:h-10 bg-slate-700 hover:bg-primary rounded-lg flex items-center justify-center text-slate-300 hover:text-white transition-colors flex-shrink-0">
                                <i data-lucide="twitter" class="icon-sm sm:icon"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($linkedin)): ?>
                            <a href="<?= esc($linkedin) ?>" target="_blank" class="w-8 h-8 sm:w-10 sm:h-10 bg-slate-700 hover:bg-primary rounded-lg flex items-center justify-center text-slate-300 hover:text-white transition-colors flex-shrink-0">
                                <i data-lucide="linkedin" class="icon-sm sm:icon"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($youtube)): ?>
                            <a href="<?= esc($youtube) ?>" target="_blank" class="w-8 h-8 sm:w-10 sm:h-10 bg-slate-700 hover:bg-primary rounded-lg flex items-center justify-center text-slate-300 hover:text-white transition-colors flex-shrink-0">
                                <i data-lucide="youtube" class="icon-sm sm:icon"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($instagram)): ?>
                            <a href="<?= esc($instagram) ?>" target="_blank" class="w-8 h-8 sm:w-10 sm:h-10 bg-slate-700 hover:bg-primary rounded-lg flex items-center justify-center text-slate-300 hover:text-white transition-colors flex-shrink-0">
                                <i data-lucide="instagram" class="icon-sm sm:icon"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Links -->
            <div class="sm:col-span-1">
                <h4 class="text-base sm:text-lg font-semibold mb-4 sm:mb-6">Quick Links</h4>
                <ul class="space-y-2 sm:space-y-3">
                    <li><a href="<?= base_url('about') ?>" class="text-slate-300 hover:text-secondaryShades-400 transition-colors text-xs sm:text-sm block py-1">About KEWASNET</a></li>
                    <li><a href="<?= base_url('programs') ?>" class="text-slate-300 hover:text-secondaryShades-400 transition-colors text-xs sm:text-sm block py-1">Our Programs</a></li>
                    <li><a href="<?= base_url('ksp') ?>" class="text-slate-300 hover:text-secondaryShades-400 transition-colors text-xs sm:text-sm block py-1">Knowledge Platform</a></li>
                    <li><a href="<?= base_url('contact-us') ?>" class="text-slate-300 hover:text-secondaryShades-400 transition-colors text-xs sm:text-sm block py-1">Contact Us</a></li>
                </ul>
            </div>

            <!-- Resources -->
            <div class="sm:col-span-1">
                <h4 class="text-base sm:text-lg font-semibold mb-4 sm:mb-6">Resources</h4>
                <ul class="space-y-2 sm:space-y-3">
                    <li><a href="<?= base_url('/policy-briefs') ?>" class="text-slate-300 hover:text-secondaryShades-400 transition-colors text-xs sm:text-sm block py-1">Policy Briefs</a></li>
                    <li><a href="<?= base_url('/best-practices') ?>" class="text-slate-300 hover:text-secondaryShades-400 transition-colors text-xs sm:text-sm block py-1">Best Practices</a></li>
                    <li><a href="<?= base_url('/faq') ?>" class="text-slate-300 hover:text-secondaryShades-400 transition-colors text-xs sm:text-sm block py-1">FAQs</a></li>
                    <li><a href="<?= base_url('/help-center') ?>" class="text-slate-300 hover:text-secondaryShades-400 transition-colors text-xs sm:text-sm block py-1">Help Center</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="sm:col-span-2 lg:col-span-1">
                <h4 class="text-base sm:text-lg font-semibold mb-4 sm:mb-6">Contact Us</h4>
                <div class="space-y-3 sm:space-y-4">
                    <div class="flex items-start space-x-3">
                        <i data-lucide="map-pin" class="icon-sm sm:icon-lg text-secondaryShades-400 mt-0.5 flex-shrink-0"></i>
                        <div class="text-slate-300 text-xs sm:text-sm leading-relaxed min-w-0 flex-1">
                            <p class="break-words">Mango Court, 4th Floor, Suite A403, Wood Avenue</p>
                            <p>Nairobi, Kenya</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i data-lucide="phone" class="icon-sm sm:icon-lg text-secondaryShades-400 flex-shrink-0"></i>
                        <a href="tel:+254705530499" class="text-slate-300 text-xs sm:text-sm hover:text-secondaryShades-400 transition-colors">+254 (705) 530-499</a>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i data-lucide="mail" class="icon-sm sm:icon-lg text-secondaryShades-400 flex-shrink-0"></i>
                        <a href="mailto:info@kewasnet.co.ke" class="text-slate-300 text-xs sm:text-sm hover:text-secondaryShades-400 transition-colors break-all">info@kewasnet.co.ke</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-700 mt-8 sm:mt-12 pt-6 sm:pt-8">
            <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:justify-between sm:items-center">
                <p class="text-slate-400 text-xs sm:text-sm text-center sm:text-left">
                    © 2010 - <?= date("Y") ?> KEWASNET. All rights reserved.
                </p>
                <div class="flex flex-wrap justify-center sm:justify-end gap-3 sm:gap-4 sm:space-x-0">
                    <a href="<?= base_url('/privacy-and-policies') ?>" class="text-slate-400 hover:text-secondaryShades-400 transition-colors text-xs sm:text-sm whitespace-nowrap">Privacy Policy</a>
                    <span class="text-slate-600 hidden sm:inline">|</span>
                    <a href="<?= base_url('/terms-of-service') ?>" class="text-slate-400 hover:text-secondaryShades-400 transition-colors text-xs sm:text-sm whitespace-nowrap">Terms of Service</a>
                    <span class="text-slate-600 hidden sm:inline">|</span>
                    <a href="<?= base_url('/cookies') ?>" class="text-slate-400 hover:text-secondaryShades-400 transition-colors text-xs sm:text-sm whitespace-nowrap">Cookies Policy</a>
                    <span class="text-slate-600 hidden sm:inline">|</span>
                    <a href="<?= base_url('/sitemap') ?>" class="text-slate-400 hover:text-secondaryShades-400 transition-colors text-xs sm:text-sm whitespace-nowrap">Sitemap</a>
                </div>
            </div>
        </div>
    </div>
</footer>