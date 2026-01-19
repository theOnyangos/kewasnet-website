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
            'pageTitle' => 'Course Sections',
            'pageDescription' => 'Manage course sections and organize content',
            'breadcrumbs' => [
                ['label' => 'Courses', 'url' => base_url('auth/courses')],
                ['label' => 'Sections']
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
                    <h1 class="text-2xl font-bold text-slate-800">Course Sections</h1>
                    <p class="mt-1 text-sm text-slate-500">Manage course sections and organize content</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="sectionsTable" class="data-table stripe hover" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th>Course</th>
                            <th>Section Title</th>
                            <th>Lectures</th>
                            <th>Status</th>
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

        $('#sectionsTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('auth/courses/get-sections') ?>",
                "type": "POST"
            },
            "columns": [
                { "data": "course_title" },
                { "data": "title" },
                { "data": "lectures_count", "className": "text-center" },
                {
                    "data": "status",
                    "render": function(data) {
                        return data == 1 ? '<span class="badge bg-green-100 text-green-800">Active</span>' : '<span class="badge bg-red-100 text-red-800">Inactive</span>';
                    }
                },
                { "data": "created_at" },
                {
                    "data": "id",
                    "orderable": false,
                    "render": function(data, type, row) {
                        return `<button onclick="editSection(${data}, ${row.course_id})" class="text-blue-600 hover:text-blue-800"><i data-lucide="edit" class="w-4 h-4"></i></button>`;
                    }
                }
            ],
            "drawCallback": function() {
                lucide.createIcons();
            }
        });
    });

    function editSection(sectionId, courseId) {
        // Redirect to the course edit page with section ID in URL hash
        window.location.href = "<?= base_url('auth/courses/edit') ?>/" + courseId + "#section-" + sectionId;
    }
</script>
<?= $this->endSection() ?>
