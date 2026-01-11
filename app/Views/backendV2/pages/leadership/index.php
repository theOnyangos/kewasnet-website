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
                        <th>Profile</th>
                        <th>Description</th>
                        <th>Socials</th>
                        <th>Status</th>
                        <th>Member Since</th>
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
    let leadershipTable;

    // Helper function to format dates
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        } catch (e) {
            return 'Invalid date';
        }
    }

    $(document).ready(function() {
        leadershipTable = $('#leadershipTable').DataTable({
            "processing": true,
            "serverSide": true,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search members...",
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
                "url": "<?= base_url('auth/leadership/get') ?>",
                "type": "POST",
                "dataSrc": function(json) {
                    return json.data;
                },
                "error": function(xhr) {
                    console.error("DataTables error:", xhr.responseText);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load leadership data',
                            confirmButtonColor: '#3b82f6'
                        });
                    }
                }
            },
            "columns": [
                {
                    "data": null,
                    "width": "20%",
                    "render": function(data, type, row) {
                        const imgHtml = row.image 
                            ? `<img src="<?= base_url() ?>/${row.image}" class="w-12 h-12 rounded-full object-cover border-2 border-gray-200" alt="${row.name || 'Profile'}" onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'48\' height=\'48\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23999\' stroke-width=\'2\'%3E%3Cpath d=\'M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2\'%3E%3C/path%3E%3Ccircle cx=\'12\' cy=\'7\' r=\'4\'%3E%3C/circle%3E%3C/svg%3E'; this.classList.add('opacity-50');">`
                            : `<div class="w-12 h-12 rounded-full bg-gray-100 border-2 border-gray-200 flex items-center justify-center">
                                   <i data-lucide="user" class="w-6 h-6 text-gray-400"></i>
                               </div>`;
                        
                        return `
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    ${imgHtml}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">${row.name || 'N/A'}</div>
                                    ${row.experience ? `<div class="text-xs text-gray-500 mt-0.5">Experience: ${row.experience}</div>` : ''}
                                    ${row.position ? `<div class="text-xs text-gray-500 mt-0.5">Position: ${row.position}</div>` : ''}
                                </div>
                            </div>
                        `;
                    },
                    "orderable": false
                },
                {
                    "data": "description",
                    "width": "25%",
                    "render": function(data) {
                        if (!data) {
                            return '<span class="text-xs text-gray-400 italic">No description</span>';
                        }
                        const maxLength = 100;
                        const truncated = data.length > maxLength ? data.substring(0, maxLength) + '...' : data;
                        return `<div class="text-sm text-gray-600" title="${data.replace(/"/g, '&quot;')}">${truncated}</div>`;
                    }
                },
                {
                    "data": "social_media",
                    "width": "15%",
                    "className": "text-center",
                    "orderable": false,
                    "render": function(data) {
                        if (!data) {
                            return '<span class="text-xs text-gray-400">No socials</span>';
                        }
                        
                        try {
                            const socials = typeof data === 'string' ? JSON.parse(data) : data;
                            if (!Array.isArray(socials) || socials.length === 0) {
                                return '<span class="text-xs text-gray-400">No socials</span>';
                            }
                            
                            const iconMap = {
                                'LinkedIn': 'linkedin',
                                'github': 'github',
                                'Facebook': 'facebook',
                                'Instagram': 'instagram',
                                'YouTube': 'youtube',
                                'WhatsApp': 'message-circle',
                                'Website': 'globe',
                                'X': 'x',
                                'Other': 'link'
                            };
                            
                            let socialsHtml = '<div class="flex items-center justify-center gap-2 flex-wrap">';
                            socials.forEach(function(social) {
                                const platform = social.platform || '';
                                const url = social.url || '';
                                const icon = iconMap[platform] || 'link';
                                
                                if (url) {
                                    socialsHtml += `<a href="${url}" target="_blank" rel="noopener noreferrer" 
                                        class="text-gray-600 hover:text-primary transition-colors" 
                                        title="${platform}: ${url}">
                                        <i data-lucide="${icon}" class="w-4 h-4"></i>
                                    </a>`;
                                }
                            });
                            socialsHtml += '</div>';
                            
                            return socialsHtml;
                        } catch (e) {
                            console.error('Error parsing social media:', e);
                            return '<span class="text-xs text-gray-400">Invalid data</span>';
                        }
                    }
                },
                {
                    "data": "status",
                    "width": "10%",
                    "className": "text-center",
                    "render": function(data) {
                        const badgeClass = data === 'active' 
                            ? 'bg-green-100 text-green-800' 
                            : 'bg-red-100 text-red-800';
                        const badgeText = data === 'active' ? 'Active' : 'Inactive';
                        return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${badgeClass}">${badgeText}</span>`;
                    }
                },
                {
                    "data": "created_at",
                    "width": "12%",
                    "className": "text-center",
                    "render": function(data) {
                        const formattedDate = formatDate(data);
                        return `
                            <div class="flex items-center justify-center">
                                <i data-lucide="calendar" class="w-4 h-4 mr-1 text-gray-400"></i>
                                <span class="text-sm text-gray-600">${formattedDate}</span>
                            </div>
                        `;
                    }
                },
                {
                    "data": null,
                    "width": "10%",
                    "className": "text-center",
                    "orderable": false,
                    "render": function(data, type, row) {
                        return `
                            <div class="flex items-center justify-center space-x-2">
                                <a href="<?= base_url('auth/leadership/edit') ?>/${row.id}" 
                                   class="text-slate-600 hover:text-primary transition-colors p-1.5 rounded hover:bg-primary/10" 
                                   title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                <button onclick="deleteMember('${row.id}')" 
                                        class="text-slate-600 hover:text-red-600 transition-colors p-1.5 rounded hover:bg-red-50" 
                                        title="Delete">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            "order": [[4, "desc"]], // Order by created_at descending
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            "responsive": true,
            "initComplete": function() {
                lucide.createIcons();
            },
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
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Deleting...',
                        text: 'Please wait while we delete the member',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `<?= base_url('auth/leadership/delete') ?>/${id}`,
                        type: 'DELETE',
                        dataType: 'json',
                        success: function(data) {
                            if (data.success || data.status === 'success') {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: data.message || 'Member deleted successfully',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                leadershipTable.ajax.reload();
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: data.message || 'Failed to delete member',
                                    icon: 'error',
                                    confirmButtonColor: '#3b82f6'
                                });
                            }
                        },
                        error: function(xhr) {
                            const errorMessage = xhr.responseJSON?.message || 'Failed to delete member';
                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonColor: '#3b82f6'
                            });
                        }
                    });
                }
            });
        }
    }
</script>
<?= $this->endSection() ?>