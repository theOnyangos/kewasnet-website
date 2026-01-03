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
<!-- Terms of Service Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 max-w-4xl">
            <h1 class="text-4xl font-bold text-primary mb-6">Terms of Service</h1>
            <p class="text-gray-600 mb-8">Last Updated: August 18, 2025 at 11:55 AM EAT</p>

            <div class="space-y-8">
                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">1. Introduction</h2>
                    <p class="text-gray-700 mb-4">Welcome to the Kenya Water and Sanitation Civil Society Network (KEWASNET) website. These Terms of Service ("Terms") govern your use of kewasnet.co.ke and related services, including the Knowledge Sharing Platform, designed to support water, sanitation, and hygiene (WASH) initiatives. By accessing or using our website, you agree to comply with these Terms. If you do not agree, please refrain from using our services. We may update these Terms periodically, and continued use constitutes acceptance of the revised terms.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">2. User Responsibility</h2>
                    <p class="text-gray-700 mb-4">As a user, you are responsible for:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                        <li>Providing accurate, current, and complete information when registering or interacting with our services.</li>
                        <li>Refraining from engaging in illegal activities, including but not limited to hacking, distributing malware, or violating intellectual property rights.</li>
                        <li>Respecting website rules, such as not posting offensive or harmful content, and adhering to community guidelines outlined on the platform.</li>
                    </ul>
                    <p class="text-gray-700 mt-4">Failure to comply may result in account suspension or termination.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">3. Acceptable Use Policy</h2>
                    <p class="text-gray-700 mb-4">You agree to use the website only for lawful purposes and in a manner that does not infringe on the rights of others. Prohibited activities include:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                        <li>Posting content that is defamatory, obscene, or promotes violence.</li>
                        <li>Attempting to interfere with the website’s security or performance (e.g., DDoS attacks).</li>
                        <li>Impersonating others or misrepresenting your affiliation with KEWASNET.</li>
                    </ul>
                    <p class="text-gray-700 mt-4">Violations may lead to immediate account termination.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">4. Intellectual Property Rights</h2>
                    <p class="text-gray-700 mb-4">KEWASNET owns all intellectual property rights to the website content, including design, text, logos, and images, unless otherwise stated. These are protected by Kenyan copyright and trademark laws. Users retain ownership of any content they upload (if applicable), such as forum posts or contributions, but grant KEWASNET a non-exclusive, royalty-free license to use, display, and distribute such content for platform purposes. Unauthorized use of our content is prohibited without prior written consent.</p>
                    <p class="text-gray-700 mt-4">We reserve the right to remove any user content that violates these Terms, and users may request removal by emailing info@kewasnet.co.ke with relevant details.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">5. Links to Third-Party Sites</h2>
                    <p class="text-gray-700 mb-4">Our website may contain links to third-party sites for your convenience. KEWASNET is not responsible for the  privacy policies, or practices of these sites. Your interactions with third-party sites are governed by their terms.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">6. Force Majeure Clause</h2>
                    <p class="text-gray-700 mb-4">KEWASNET shall not be liable for any delays or failures in performance due to unforeseen events beyond our control, including natural disasters, war, government actions, or internet outages. We will resume services as soon as practicable under such circumstances.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">7. Liability Limitation</h2>
                    <p class="text-gray-700 mb-4">KEWASNET is not liable for any indirect, incidental, or consequential damages arising from website use, including interruptions, data breaches, or loss of content, to the extent permitted by law. Our liability is limited to the amount you have paid us (if any) for services. This limitation does not apply to damages resulting from gross negligence or intentional misconduct by KEWASNET, and all claims must be brought within one year of the incident.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">8. Termination Clauses</h2>
                    <p class="text-gray-700 mb-4">KEWASNET reserves the right to terminate or suspend your account and access to the website under the following conditions:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                        <li>Violation of these Terms, including illegal behavior or misuse of the platform.</li>
                        <li>Prolonged inactivity (e.g., no login for 12 consecutive months).</li>
                        <li>Engagement in activities that harm the website’s integrity or community.</li>
                    </ul>
                    <p class="text-gray-700 mt-4">Termination may occur with or without notice, and you will lose access to uploaded content upon termination unless otherwise required by law.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">9. Dispute Resolution</h2>
                    <p class="text-gray-700 mb-4">In the event of a dispute, we encourage parties to first attempt resolution through mediation or arbitration before pursuing legal action. These proceedings will be conducted in Nairobi, Kenya, under the rules of the Kenya Commercial Arbitration and Mediation Centre. If unresolved, disputes will be subject to the exclusive jurisdiction of the courts of Kenya, and you agree to waive any objections to such jurisdiction.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">10. Cookie Policy</h2>
                    <p class="text-gray-700 mb-4">We use cookies to enhance your experience on kewasnet.co.ke, including for analytics and personalization. You can manage cookies through your browser settings or install the Google Analytics Opt-out Browser Add-on (available at <a href="https://tools.google.com/dlpage/gaoptout" target="_blank" rel="noopener noreferrer" class="text-secondary hover:underline">tools.google.com/dlpage/gaoptout</a>). Essential cookies cannot be disabled as they are necessary for website functionality.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">11. Applicable Laws</h2>
                    <p class="text-gray-700 mb-4">These Terms are governed by and construed in accordance with the laws of Kenya. Key legal frameworks applicable to kewasnet.co.ke include:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                        <li><strong>Kenyan Constitution (2010):</strong> Protects freedom of expression and access to information, influencing user rights and content moderation.</li>
                        <li><strong>Data Protection Act, 2019:</strong> Regulates the collection, processing, and storage of personal data, ensuring compliance with user privacy rights.</li>
                        <li><strong>Computer Misuse and Cybercrimes Act, 2018:</strong> Prohibits unauthorized access, data interference, and other cybercrimes, forming the basis for user responsibility clauses.</li>
                        <li><strong>Copyright Act, 2001:</strong> Protects KEWASNET’s intellectual property and governs user-uploaded content usage.</li>
                    </ul>
                    <p class="text-gray-700 mt-4">Where applicable, international standards such as the General Data Protection Regulation (GDPR) may influence data handling for users from the European Economic Area (EEA).</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">12. Changes to These Terms</h2>
                    <p class="text-gray-700 mb-4">We may revise these Terms to reflect changes in our services or legal requirements. Updates will be posted here with a revised "Last Updated" date, and significant changes will be communicated via email or a website notice. Continued use after updates signifies acceptance of the new Terms.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">13. Contact Us</h2>
                    <p class="text-gray-700 mb-4">For questions or concerns about these Terms, contact us at:</p>
                    <p class="text-gray-700"><strong>Email:</strong> <a href="mailto:info@kewasnet.co.ke" class="text-secondary hover:underline">info@kewasnet.co.ke</a></p>
                    <p class="text-gray-700"><strong>Address:</strong> KEWASNET Secretariat, P.O. Box 30725-00100, Nairobi, Kenya</p>
                    <p class="text-gray-700"><strong>Phone:</strong> <a href="tel:+254705530499" class="text-secondary hover:underline">+254 (705) -530-499</a></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Subscribe Section -->
    <section class="py-20 bg-gradient-to-r from-primary to-secondary text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-6">Stay Informed</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto">
                Subscribe to our newsletter for updates on IWRM-driven solutions.
            </p>
            <form class="max-w-xl mx-auto flex gap-4 subscribe-input" onsubmit="return validateForm()">
                <input type="email" id="email-input" placeholder="Enter your email" class="flex-1 px-4 py-3 text-primary rounded-lg outline-none border-none" required aria-label="Email address">
                <button type="submit" class="bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-primary/90 transition-colors flex items-center justify-center">
                    <span>Subscribe</span>
                    <i data-lucide="mail" class="ml-2 icon"></i>
                </button>
            </form>
        </div>
    </section>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    }
</script>
<?= $this->endSection() ?>