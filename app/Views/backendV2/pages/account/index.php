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
        'pageTitle' => 'Manage Admin Accounts',
        'pageDescription' => 'View and manage all admin accounts in the system',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => base_url('auth/dashboard')],
            ['label' => 'Admin Accounts']
        ]
    ]) ?>

    <div class="px-6 pb-6">
        <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Admin Accounts</h1>
                    <p class="mt-1 text-sm text-slate-500">Manage admin user accounts, roles, and permissions</p>
                </div>
            </div>

            <table id="accountTable" class="data-table stripe hover" style="width:100%">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Account Status</th>
                        <th>Last Login</th>
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
        $('#accountTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('auth/account/get') ?>",
                "type": "POST",
                "dataSrc": function(json) {
                    return json.data;
                }
            },
            "columns": [
                { "data": "name" },
                { "data": "email" },
                { "data": "phone" },
                { "data": "role" },
                { 
                    "data": "status",
                    "render": function(data) {
                        const badgeClass = data === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                        return `<span class="px-2 py-1 rounded-full text-xs font-medium ${badgeClass}">${data}</span>`;
                    }
                },
                { 
                    "data": "account_status",
                    "render": function(data) {
                        const badgeClass = data === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                        return `<span class="px-2 py-1 rounded-full text-xs font-medium ${badgeClass}">${data}</span>`;
                    }
                },
                { "data": "last_login" },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return `
                            <div class="flex space-x-2">
                                <a href="<?= base_url('auth/account/view') ?>/${row.id}" class="p-1 text-slate-500 hover:text-primary" title="View">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="<?= base_url('auth/account/edit') ?>/${row.id}" class="p-1 text-slate-500 hover:text-primary" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                <button onclick="deleteAccount('${row.id}')" class="p-1 text-slate-500 hover:text-red-500" title="Delete">
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

    function deleteAccount(id) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will delete the admin account!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `<?= base_url('auth/account/delete') ?>/${id}`,
                        type: 'DELETE',
                        dataType: 'json',
                        success: function(data) {
                            if (data.status === 'success') {
                                Swal.fire('Deleted!', data.message || 'Account deleted successfully', 'success');
                                $('#accountTable').DataTable().ajax.reload();
                            } else {
                                Swal.fire('Error!', data.message || 'Failed to delete account', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Failed to delete account', 'error');
                        }
                    });
                }
            });
        }
    }
</script>
<?= $this->endSection() ?>