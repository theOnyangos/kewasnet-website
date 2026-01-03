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
    <!-- Privacy Policy Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 max-w-4xl">
            <h1 class="text-4xl font-bold text-primary mb-6">Privacy Policy</h1>
            <p class="text-gray-600 mb-8">Last Updated: August 18, 2025 at 10:30 AM EAT</p>

            <div class="space-y-8">
                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">Introduction</h2>
                    <p class="text-gray-700 mb-4">At the Kenya Water and Sanitation Civil Society Network (KEWASNET), we are dedicated to protecting your privacy and ensuring the security of your personal information. This Privacy Policy outlines our commitment to safeguarding your data as you engage with our website, services, and water, sanitation, and hygiene (WASH) initiatives. By using our platform, you agree to the data practices described herein, and we encourage you to read this policy carefully to understand how we handle your information.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">What Does This Privacy Notice Cover?</h2>
                    <p class="text-gray-700 mb-4">This Privacy Notice applies to all personal data we collect through our website (kewasnet.co.ke), event registrations, newsletter subscriptions, surveys, and any other interactions with KEWASNET. It details how we collect, process, store, and protect your information, as well as your rights under the Kenyan Data Protection Act, 2019. This policy does not cover data collected by third-party sites linked from our platform, which are subject to their own privacy policies.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">What is Personal Data?</h2>
                    <p class="text-gray-700 mb-4">Personal data refers to any information that can be used to identify you, either directly or indirectly. This includes, but is not limited to, your name, email address, phone number, financial details, and online identifiers such as Anonymized IP addresses. At KEWASNET, we handle this data responsibly to support our WASH advocacy and community engagement efforts while adhering to legal standards.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">How Do We Collect Personal Data?</h2>
                    <p class="text-gray-700 mb-4">We collect personal data through various methods to deliver our services and improve your experience. These methods include direct interactions (e.g., forms), automated technologies (e.g., Google Analytics), and third-party sources where applicable. The specific data collected and the methods used are detailed in the table below.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">Data Collected and How We Collect It</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-primary text-white">
                                    <th class="py-2 px-4 border border-gray-300">Data Collected</th>
                                    <th class="py-2 px-4 border border-gray-300">How We Collect the Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="hover:bg-gray-100">
                                    <td class="py-2 px-4 border border-gray-300">1. Personal Information</td>
                                    <td class="py-2 px-4 border border-gray-300">Collected when you provide your name, gender, or date of birth through registration forms, event sign-ups, or profile creation on our website.</td>
                                </tr>
                                <tr class="hover:bg-gray-100">
                                    <td class="py-2 px-4 border border-gray-300">2. Contact Information</td>
                                    <td class="py-2 px-4 border border-gray-300">Gathered when you submit your email address, phone number, or postal address via newsletter subscriptions, contact forms, or event registrations.</td>
                                </tr>
                                <tr class="hover:bg-gray-100">
                                    <td class="py-2 px-4 border border-gray-300">3. Identification Details & Documentation</td>
                                    <td class="py-2 px-4 border border-gray-300">Collected if you provide identification documents (e.g., ID numbers or passports) for verification during specific programs or partnerships, with your consent.</td>
                                </tr>
                                <tr class="hover:bg-gray-100">
                                    <td class="py-2 px-4 border border-gray-300">4. Financial Information</td>
                                    <td class="py-2 px-4 border border-gray-300">Obtained when you make donations or purchase resources, processed securely by third-party payment providers (e.g., credit card details are not stored by us).</td>
                                </tr>
                                <tr class="hover:bg-gray-100">
                                    <td class="py-2 px-4 border border-gray-300">5. Tracking Information</td>
                                    <td class="py-2 px-4 border border-gray-300">Collected via Google Analytics, which tracks usage data (e.g., pages visited, time spent) and device information (e.g.,Anonymized IP address, browser type) to analyze website performance.<br>Click to check out our Google Analytics Policy > <a href="GoogleAnalytics.html" target="_blank" style="color: #27aae0;" >Google Analytics Policy</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">How Do We Use Your Personal Data?</h2>
                    <p class="text-gray-700 mb-4">We use your personal data for the following purposes to support our mission and enhance your experience:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                        <li><strong>Service Delivery:</strong> To provide access to our knowledge-sharing platforms, event registrations, and WASH-related resources.</li>
                        <li><strong>Communication:</strong> To send you newsletters, updates, or promotional materials about our initiatives (with your consent, which you can withdraw at any time).</li>
                        <li><strong>Improvement:</strong> To analyze usage patterns and improve our website, services, and advocacy efforts based on your feedback and Google Analytics insights.</li>
                        <li><strong>Legal Compliance:</strong> To comply with legal obligations, resolve disputes, and enforce our policies.</li>
                        <li><strong>Personalization:</strong> To tailor content and recommendations relevant to your interests in water and sanitation issues.</li>
                    </ul>
                    <p class="text-gray-700 mt-4">We rely on your consent, contractual necessity, and legitimate interests (where applicable) as legal bases for processing your data, in line with the Kenyan Data Protection Act, 2019.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">Your Data Protection Rights</h2>
                    <p class="text-gray-700 mb-4">Under the Kenyan Data Protection Act, 2019, and other applicable regulations, you have the following rights:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                        <li><strong>Access:</strong> Request a copy of the personal data we hold about you.</li>
                        <li><strong>Correction:</strong> Request correction of inaccurate or incomplete data.</li>
                        <li><strong>Deletion:</strong> Request deletion of your data where it is no longer necessary or where you withdraw consent.</li>
                        <li><strong>Restriction:</strong> Limit how we use your data under certain circumstances.</li>
                        <li><strong>Objection:</strong> Object to processing based on legitimate interests or for direct marketing purposes.</li>
                        <li><strong>Data Portability:</strong> Request your data in a structured, machine-readable format for transfer to another organization.</li>
                    </ul>
                    <p class="text-gray-700 mt-4">To exercise these rights, contact us at <a href="mailto:info@kewasnet.co.ke" class="text-secondary hover:underline">info@kewasnet.co.ke</a>. We may require verification of your identity before processing your request.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">Data Security</h2>
                    <p class="text-gray-700 mb-4">We prioritize the security of your personal information and employ a range of measures to protect it, including:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                        <li>Encryption of sensitive data during transmission and storage.</li>
                        <li>Regular security assessments and updates to our systems.</li>
                        <li>Access controls to limit data access to authorized personnel only.</li>
                    </ul>
                    <p class="text-gray-700 mt-4">While we strive to use commercially acceptable means to protect your data, no method of transmission over the internet or electronic storage is 100% secure, and we cannot guarantee absolute security.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">International Data Transfers</h2>
                    <p class="text-gray-700 mb-4">Your information may be transferred to and processed in countries outside Kenya (e.g., for cloud storage or service providers). We ensure that such transfers comply with the Kenyan Data Protection Act, 2019, and include appropriate safeguards, such as standard contractual clauses.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">Retention of Data</h2>
                    <p class="text-gray-700 mb-4">We retain your personal information only for as long as necessary to fulfill the purposes outlined in this policy, including legal, accounting, or reporting requirements. When data is no longer needed, it is securely deleted or anonymized.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">Changes to This Privacy Policy</h2>
                    <p class="text-gray-700 mb-4">We may update this Privacy Policy to reflect changes in our practices or legal requirements. Any updates will be posted on this page with a revised "Last Updated" date. We encourage you to review this policy periodically. Significant changes will be communicated via email or a prominent notice on our website.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-primary mb-4">Contact Us</h2>
                    <p class="text-gray-700 mb-4">If you have any questions, concerns, or requests regarding this Privacy Policy or your data, please contact us:</p>
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