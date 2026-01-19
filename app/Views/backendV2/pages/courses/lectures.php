<?php
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    session()->set(['redirect_url' => $currentUrl::currentUrl()]);
?>

<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto">
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Course Lectures',
            'pageDescription' => 'Manage course lectures and video content',
            'breadcrumbs' => [
                ['label' => 'Courses', 'url' => base_url('auth/courses')],
                ['label' => 'Lectures']
            ],
            'bannerActions' => '<div class="flex items-center gap-3">
                <a href="' . site_url('auth/courses/create') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                    Create Course
                </a>
                <button type="button" onclick="window.open(\'' . site_url('ksp/learning-hub') . '\', \'_blank\')" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                    <i data-lucide="external-link" class="w-4 h-4 mr-2"></i>
                    View Frontend
                </button>
            </div>'
        ]) ?>

        <div class="px-6 pb-6">
        <?= $this->include('backendV2/pages/courses/partials/quick_stats_section') ?>
        <?= $this->include('backendV2/pages/courses/partials/navigation_section') ?>

        <div class="bg-white rounded-b-xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Course Lectures</h1>
                    <p class="mt-1 text-sm text-slate-500">Manage course lectures and video content</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="lecturesTable" class="data-table stripe hover" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th>Section</th>
                            <th>Lecture Title</th>
                            <th>Duration</th>
                            <th>Preview</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        </div>
    </main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        lucide.createIcons();

        $('#lecturesTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('auth/courses/get-lectures') ?>",
                "type": "POST"
            },
            "columns": [
                { "data": "section_title" },
                { "data": "title" },
                {
                    "data": "duration",
                    "render": function(data) {
                        return data ? `${data} min` : 'N/A';
                    }
                },
                {
                    "data": "is_preview",
                    "className": "text-center",
                    "render": function(data) {
                        return data == 1 ? '<i data-lucide="eye" class="w-4 h-4 text-green-600"></i>' : '-';
                    }
                },
                { "data": "created_at" },
                {
                    "data": "id",
                    "orderable": false,
                    "render": function(data) {
                        return `<button class="text-blue-600"><i data-lucide="edit" class="w-4 h-4"></i></button>`;
                    }
                }
            ],
            "drawCallback": function() {
                lucide.createIcons();
            }
        });
    });
</script>
<?= $this->endSection() ?>
