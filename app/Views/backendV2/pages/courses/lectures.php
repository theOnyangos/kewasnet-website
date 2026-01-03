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
                            <th>Order</th>
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
                { "data": "order_index", "className": "text-center" },
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
