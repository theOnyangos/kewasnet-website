<?php
// app/Views/frontendV2/ksp/pages/learning-hub/learning/certificate.php
// Certificate page for completed courses

$user = $user ?? null;
$course = $course ?? null;
$certificateId = $certificateId ?? null;
$issuedAt = $issuedAt ?? date('Y-m-d');

if (!$user || !$course) {
    // Optionally redirect or show error
    echo '<div class="text-center text-red-600 mt-10">Invalid certificate request.</div>';
    return;
}
?>
<?= $this->extend('frontendV2/ksp/layouts/main') ?>
<?= $this->section('title'); ?>Certificate of Completion<?= $this->endSection(); ?>
<?= $this->section('content') ?>
<div class="flex justify-center items-center min-h-[80vh] bg-gradient-to-br from-blue-50 to-green-50 py-10">
    <div class="bg-white shadow-2xl rounded-2xl border-4 border-blue-200 max-w-2xl w-full p-10 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-primary to-green-400"></div>
        <div class="flex flex-col items-center">
            <img src="<?= base_url('public/assets/logo.png') ?>" alt="KEWASNET Logo" class="h-16 mb-4">
            <h1 class="text-3xl font-extrabold text-primary mb-2 tracking-wide">Certificate of Completion</h1>
            <p class="text-lg text-slate-700 mb-6">This is to certify that</p>
            <div class="text-2xl font-bold text-green-700 mb-2"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></div>
            <p class="text-slate-600 mb-4">has successfully completed the course</p>
            <div class="text-xl font-semibold text-blue-800 mb-2">"<?= esc($course['title']) ?>"</div>
            <p class="text-slate-500 mb-6">on <?= date('F j, Y', strtotime($issuedAt)) ?></p>
            <div class="w-full flex justify-between items-center mt-8">
                <div class="text-left">
                    <div class="text-xs text-slate-400">Certificate ID</div>
                    <div class="font-mono text-sm text-slate-700"><?= esc($certificateId) ?></div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-slate-400">Issued by</div>
                    <div class="font-bold text-slate-700">Kenya Water &amp; Sanitation Civil Society Network<br>(KEWASNET)</div>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 w-full h-2 bg-gradient-to-r from-primary to-green-400"></div>
    </div>
</div>
<?= $this->endSection() ?>
