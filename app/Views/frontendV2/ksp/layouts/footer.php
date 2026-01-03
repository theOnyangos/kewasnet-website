<footer class="bg-dark text-white py-8">
    <div class="container mx-auto px-4">
        <div class="border-t border-slate-700 flex flex-col md:flex-row justify-between items-center">
            <p class="text-slate-400 mb-4 md:mb-0"> © 2010 - <?= date("Y") ?> WASH Knowledge Hub. All rights reserved.</p>
            <div class="flex space-x-6">
                <a href="<?= base_url('/privacy-and-policies') ?>" class="text-slate-400 hover:text-secondary transition-colors">Privacy Policy</a>
                <a href="<?= base_url('/terms-of-service') ?>" class="text-slate-400 hover:text-secondary transition-colors">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<!-- Admin Popup -->
<?= $this->include('frontendV2/ksp/layouts/constants/admin-popup') ?>