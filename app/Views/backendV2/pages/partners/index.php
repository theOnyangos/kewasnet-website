<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('backendV2/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto">
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Partner Management',
            'pageDescription' => 'Manage organizational partnerships and collaborations',
            'breadcrumbs' => [
                ['label' => 'Partners']
            ]
        ]) ?>

        <div class="px-6 pb-6">
        <!-- Partners Table -->
        <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
            <!-- Title and Description -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Partner Management</h1>
                    <p class="mt-1 text-sm text-slate-500">View and manage all registered partners in the system</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="<?= base_url('auth/partners/create') ?>" class="gradient-btn flex items-center px-4 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
                        <i data-lucide="plus" class="w-4 h-4 mr-2 z-10"></i>
                        <span>Add New Partner</span>
                    </a>
                </div>
            </div>

            <!-- Partners Table -->
            <table id="partnerTable" class="data-table stripe hover" style="width:100%">
                <thead>
                    <tr>
                        <th class="">Partner Name</th>
                        <th class="">Partner Logo</th>
                        <th class="">Website URL</th>
                        <th class="">Created At</th>
                        <th class="">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        </div>
    </main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    let partnerDataTable;

    $(document).ready(function() {
        // Initialize DataTable
        partnerDataTable = $('#partnerTable').DataTable({
            "processing": true,
            "serverSide": true,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search partners...",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "Showing 0 to 0 of 0 entries",
                "infoFiltered": "(filtered from _MAX_ total entries)",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            },
            "ajax": {
                "url": "<?= base_url('auth/partners/get') ?>",
                "type": "POST",
                "dataSrc": function(json) {
                    return json.data;
                },
                "error": function(xhr) {
                    showToast('Failed to load partners data', 'error');
                    console.error("DataTables error:", xhr.responseText);
                }
            },
            "columns": [
                { 
                    "data": "partner_name",
                    "render": function(data) {
                        return $('<span>').addClass('text-sm font-medium text-dark').text(data)[0].outerHTML;
                    }
                },
                {
                    "data": "partner_logo",
                    "render": function(data, type, row) {
                        if (data) {
                            const imgUrl = '<?= base_url() ?>/' + data;
                            return `
                                <div class="flex items-center justify-center">
                                    <img src="${imgUrl}"
                                         class="w-12 h-12 object-contain rounded border border-gray-200"
                                         alt="${row.partner_name || 'Partner Logo'}"
                                         onerror="this.onerror=null; this.src='<?= base_url('assets/images/placeholder.png') ?>'; this.classList.add('opacity-50');">
                                </div>
                            `;
                        }
                        return '<span class="text-xs text-gray-400 italic">No logo</span>';
                    },
                    "orderable": false
                },
                { 
                    "data": "partner_url", 
                    "render": function(data) {
                        if (data) {
                            return $('<a>').attr('href', data).addClass('text-sm font-medium text-primaryShades-400 hover:underline').attr('target', '_blank').text(data)[0].outerHTML;
                        }
                        return 'N/A';
                    }
                },
                { 
                    "data": "created_at",
                    "render": function(data) {
                        return $('<span>').addClass('text-sm font-medium text-gray-500').text(data)[0].outerHTML;
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return `
                            <div class="flex space-x-2">
                                <button onclick="editPartner('${row.id}')" class="p-1 text-slate-500 hover:text-primary" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                <button onclick="deletePartner('${row.id}')" class="p-1 text-slate-500 hover:text-red-500" title="Delete">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        `;
                    },
                    "orderable": false
                }
            ],
            "dom": '<"flex flex-col md:flex-row md:justify-between md:items-center gap-4"<"flex flex-col"l><"flex flex-col"f>><"my-2"B>rt<"flex flex-col md:flex-row md:justify-between md:items-center mt-2 gap-4"ip>',
            "buttons": [
                {
                    extend: 'copy',
                    className: 'bg-gradient-to-r from-primary to-secondary text-white px-4 py-2 rounded'
                },
                {
                    extend: 'csv',
                    className: 'bg-gradient-to-r from-primary to-secondary text-white px-4 py-2 rounded'
                },
                {
                    extend: 'excel',
                    className: 'bg-gradient-to-r from-primary to-secondary text-white px-4 py-2 rounded'
                },
                {
                    extend: 'pdf',
                    className: 'bg-gradient-to-r from-primary to-secondary text-white px-4 py-2 rounded',
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    customize: function(doc) {
                        doc.content[1].margin = [ 100, 50, 100, 50 ]; // Adjust margins
                        doc.content[1].alignment = 'justify'; // Justify text
                        doc.content[1].fontSize = 12; // Set font size
                    },
                }
            ],
            "responsive": true,
            "initComplete": function() {
                lucide.createIcons();
            },
            "drawCallback": function() {
                lucide.createIcons();
            }
        });

        // Initialize Lucide icons
        lucide.createIcons();
    });

    function editPartner(id) {
        window.location.href = `<?= base_url('auth/partners/edit') ?>/${id}`;
    }

    function deletePartner(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait while we delete the partner',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `<?= base_url('auth/partners/delete') ?>/${id}`,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function(data) {
                        if (data.status === 'success') {
                            Swal.fire({
                                title: 'Deleted!',
                                text: data.message || 'Partner has been deleted successfully.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            partnerDataTable.ajax.reload();
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Failed to delete partner',
                                icon: 'error',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    },
                    error: function(xhr) {
                        const errorMessage = xhr.responseJSON?.message || 'Failed to delete partner';
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                });
            }
        });
    }

    function showToast(message, type = 'success') {
        $('.custom-toast').remove();

        const icon = type === 'success' ? 'check-circle' :
                    type === 'error' ? 'alert-circle' : 'info';

        const toast = $(`
            <div class="custom-toast fixed top-4 right-4 px-4 py-2 rounded-md shadow-lg text-white z-50 ${
                type === 'success' ? 'bg-green-500' :
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            }">
                <div class="flex items-center">
                    <i data-lucide="${icon}" class="w-5 h-5 mr-2"></i>
                    ${message}
                </div>
            </div>
        `).appendTo('body');

        lucide.createIcons();

        toast.hide().css({opacity: 0, x: 100}).show().animate({opacity: 1, x: 0}, 300);

        setTimeout(function() {
            toast.animate({opacity: 0, y: -20}, 300, function() {
                $(this).remove();
            });
        }, 3000);
    }
</script>
<?= $this->endSection() ?>