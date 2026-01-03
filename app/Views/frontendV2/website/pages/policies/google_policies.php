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
<!-- Google Analytics Policy Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 max-w-4xl">
            <h1 class="text-4xl font-bold text-primary mb-6">Google Analytics Policy</h1>
            <p class="text-gray-600 mb-8">Last Updated: August 18, 2025 at 11:20 AM EAT</p>
            <p class="text-gray-700 mb-6">At the Kenya Water and Sanitation Civil Society Network (KEWASNET), we use Google Analytics to understand how visitors interact with our website, which supports our mission to promote water, sanitation, and hygiene (WASH) initiatives. This policy explains what data is collected, how it is used, your options for managing it, and our commitment to your privacy, in compliance with the Kenyan Data Protection Act, 2019. By using our website, you consent to the practices outlined here.</p>

            <div class="space-y-8">
                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">1. What is Google Analytics?</h2>
                    <p class="text-gray-700 mb-4">Google Analytics is a web analytics service provided by Google, Inc. that tracks and reports website traffic. We use it to analyze user behavior, improve our WASH-focused content, and optimize the user experience on kewasnet.co.ke.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">2. Information Collected by Google Analytics</h2>
                    <p class="text-gray-700 mb-4">Google Analytics collects the following data when you visit our website:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                        <li><strong>Usage Data:</strong> Information about your interactions, including pages visited, time spent, referral sources, and navigation paths.</li>
                        <li><strong>Device Information:</strong> Data such as Anonymized IP address, browser type, operating system, and device resolution.</li>
                        <li><strong>Location Data:</strong> Approximate location derived from Anonymized IP addresses, used to understand geographic trends in WASH engagement.</li>
                    </ul>
                    <p class="text-gray-700 mt-4">This data is collected automatically and is not linked to personally identifiable information unless you provide it (e.g., through forms), and we do not combine it with other data sources without your consent.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">3. How We Use Google Analytics Data</h2>
                    <p class="text-gray-700 mb-4">We use the data collected by Google Analytics for the following purposes:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                        <li><strong>Website Improvement:</strong> To analyze traffic patterns and enhance the performance, layout, and content of our WASH resources.</li>
                        <li><strong>User Experience:</strong> To optimize site navigation and ensure accessibility for users interested in water and sanitation issues.</li>
                        <li><strong>Reporting:</strong> To generate anonymized reports for internal use and to share aggregated insights with partners, without identifying individuals.</li>
                    </ul>
                    <p class="text-gray-700 mt-4">The data helps us align our advocacy and educational efforts with the needs of our community, in line with our legitimate interests under data protection laws.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">4. Data Sharing with Google</h2>
                    <p class="text-gray-700 mb-4">The data collected by Google Analytics is transmitted to and stored by Google on servers in the United States and other countries. We have configured Google Analytics to:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                        <li>Anonymize IP addresses to prevent direct identification of users.</li>
                        <li>Limit data retention to 26 months, after which it is automatically deleted or anonymized by Google.</li>
                        <li>Adhere to the Google Analytics Data Processing Amendment, ensuring compliance with the EU-U.S. and Swiss-U.S. Privacy Shield frameworks (where applicable).</li>
                    </ul>
                    <p class="text-gray-700 mt-4">Google may use this data for its own purposes, such as improving its services, but we do not allow it to use our data for advertising without your consent.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">5. Your Choices and Control</h2>
                    <p class="text-gray-700 mb-4">You have several options to manage how Google Analytics tracks your data:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                        <li><strong>Browser Settings:</strong> Disable tracking by adjusting your browser’s settings to block JavaScript or use Do Not Track (DNT) signals, though this may affect website functionality.</li>
                        <li><strong>Google Analytics Opt-Out:</strong> Install the Google Analytics Opt-out Browser Add-on (available at <a href="https://tools.google.com/dlpage/gaoptout" target="_blank" rel="noopener noreferrer" class="text-secondary hover:underline">tools.google.com/dlpage/gaoptout</a>) to prevent data collection.</li>
                        <li><strong>Cookie Consent:</strong> If we implement a consent management platform, you can opt out of non-essential tracking during your visit.</li>
                    </ul>
                    <p class="text-gray-700 mt-4">Exercising these options will not affect your ability to access our website’s core content.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">6. Legal Basis and Compliance</h2>
                    <p class="text-gray-700 mb-4">Our use of Google Analytics is based on our legitimate interest in improving our website and services, as permitted under the Kenyan Data Protection Act, 2019. We ensure compliance by:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                        <li>Implementing IP anonymization to minimize identifiability.</li>
                        <li>Adhering to Google’s data processing terms and local data protection regulations.</li>
                    </ul>
                    <p class="text-gray-700 mt-4">If you are in the European Economic Area (EEA), we rely on legitimate interests or your consent (where required) and comply with the General Data Protection Regulation (GDPR).</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">7. Changes to This Policy</h2>
                    <p class="text-gray-700 mb-4">We may update this Google Analytics Policy to reflect changes in our practices or legal requirements. Any updates will be posted on this page with a revised "Last Updated" date. Significant changes will be communicated via email or a notice on our website. We encourage you to review this policy periodically.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">8. Contact Us</h2>
                    <p class="text-gray-700 mb-4">If you have questions or concerns about this policy or how we use Google Analytics, please contact us:</p>
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