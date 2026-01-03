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
    <main class="flex-1 overflow-y-auto p-6">
        <?= $this->include('backendV2/pages/courses/partials/header_section') ?>
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
                            <th>Order</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
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
                { "data": "order_index", "className": "text-center" },
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
