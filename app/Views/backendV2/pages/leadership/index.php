<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<main class="flex-1 overflow-y-auto">
    <?= view('backendV2/partials/page_banner', [
        'pageTitle' => 'Manage Leadership Team',
        'pageDescription' => 'Manage leadership team members displayed on the about page',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => base_url('auth/dashboard')],
            ['label' => 'Leadership Team']
        ]
    ]) ?>

    <div class="px-6 pb-6">
        <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Leadership Team</h1>
                    <p class="mt-1 text-sm text-slate-500">Manage team members displayed on the about page</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="<?= base_url('auth/leadership/create') ?>" class="gradient-btn flex items-center px-4 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
                        <i data-lucide="plus" class="w-4 h-4 mr-2 z-10"></i>
                        <span>Add Team Member</span>
                    </a>
                </div>
            </div>

            <table id="leadershipTable" class="data-table stripe hover" style="width:100%">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Order</th>
                        <th>Status</th>
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
        $('#leadershipTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('auth/leadership/get') ?>",
                "type": "POST",
                "dataSrc": function(json) {
                    return json.data;
                }
            },
            "columns": [
                {
                    "data": "image",
                    "render": function(data) {
                        if (data) {
                            return `<img src="<?= base_url() ?>/${data}" alt="Profile" class="w-12 h-12 rounded-full object-cover">`;
                        }
                        return '<span class="text-xs text-gray-400">No image</span>';
                    },
                    "orderable": false
                },
                { "data": "name" },
                { "data": "position" },
                { "data": "order_index" },
                {
                    "data": "status",
                    "render": function(data) {
                        const badgeClass = data === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                        return `<span class="px-2 py-1 rounded-full text-xs font-medium ${badgeClass}">${data}</span>`;
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return `
                            <div class="flex space-x-2">
                                <a href="<?= base_url('auth/leadership/edit') ?>/${row.id}" class="p-1 text-slate-500 hover:text-primary" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                <button onclick="deleteMember('${row.id}')" class="p-1 text-slate-500 hover:text-red-500" title="Delete">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        `;
                    },
                    "orderable": false
                }
            ],
            "drawCallback": function() {
                lucide.createIcons();
            }
        });
    });

    function deleteMember(id) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will delete the leadership team member!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `<?= base_url('auth/leadership/delete') ?>/${id}`,
                        type: 'DELETE',
                        dataType: 'json',
                        success: function(data) {
                            if (data.success) {
                                Swal.fire('Deleted!', data.message || 'Member deleted successfully', 'success');
                                $('#leadershipTable').DataTable().ajax.reload();
                            } else {
                                Swal.fire('Error!', data.message || 'Failed to delete member', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Failed to delete member', 'error');
                        }
                    });
                }
            });
        }
    }
</script>
<?= $this->endSection() ?>