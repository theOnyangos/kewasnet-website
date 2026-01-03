<?php $this->extend('backendV2/layouts/main'); ?>

<?php $this->section('title'); ?>
YouTube Links Settings
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
<main class="flex-1 overflow-y-auto p-6">
    <?= $this->include('backendV2/pages/settings/partials/settings_navigation'); ?>

    <!--            success: function(response) {
                if (response.status === 'success') {
                    const link = response.data;
                    $('#youtubeTitle').val(link.title);
                    $('#youtubeUrl').val(link.url || link.link); // Handle both field names
                    $('#modalTitle').text('Edit YouTube Link');
                    $('#addYouTubeLinkForm').data('edit-id', id);
                    openYouTubeModal();
                } else {
                    showNotification(response.message || 'Failed to load link data', 'error');
                }
            },ks Panel -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">YouTube Links</h2>
                <p class="mt-1 text-sm text-gray-500">Manage YouTube video links for your website</p>
            </div>
            <button onclick="openAddYouTubeModal()" class="gradient-btn text-white px-8 py-2 rounded-[50px] hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus w-4 h-4 mr-2 inline z-10"></i>
                <span>Add YouTube Link</span>
            </button>
        </div>
        
        <!-- YouTube Links Table -->
        <div class="overflow-x-auto">
            <table id="youtubeLinksTable" class="data-table stripe hover" style="width:100%">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col">Video Title</th>
                        <th scope="col">YouTube Link</th>
                        <th scope="col">Date Added</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Table content will be populated by DataTables -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit YouTube Link Modal -->
    <div id="youtubeModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border max-w-[500px] shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="flex items-center justify-between border-b border-gray-200 pb-5 mb-5">
                    <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Add YouTube Link</h3>
                    <button onclick="closeYouTubeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times w-5 h-5"></i>
                    </button>
                </div>
                
                <form id="addYouTubeLinkForm" class="text-left space-y-4">
                    <input type="hidden" id="youtubeId" name="id">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Video Title</label>
                        <input type="text" id="youtubeTitle" name="title" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter video title">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">YouTube URL</label>
                        <input type="url" id="youtubeUrl" name="link" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://youtube.com/watch?v=...">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                        <textarea id="youtubeDescription" name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Brief description of the video"></textarea>
                    </div>

                    <!-- CSRF Token -->
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                    <div class="flex items-center space-x-3 pt-4">
                        <button type="submit" class="flex-1 gradient-btn text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-save w-4 h-4 mr-2 inline !z-20"></i>
                            <span class="">Save</span>
                        </button>
                        <button type="button" onclick="closeYouTubeModal()" class="flex-1 bg-primaryShades-100 text-primary py-2 px-4 rounded-lg hover:bg-primaryShades-200 transition-colors">
                            <i class="fas fa-times w-4 h-4 mr-2 inline !z-20"></i>
                            <span class="">Cancel</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    // Helper function for date formatting
    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return date.toLocaleDateString('en-US', options);
    }
    // Modal functions
    function openAddYouTubeModal() {
        document.getElementById('modalTitle').textContent = 'Add YouTube Link';
        document.getElementById('addYouTubeLinkForm').reset();
        document.getElementById('youtubeId').value = '';
        document.getElementById('youtubeModal').classList.remove('hidden');
    }
    
    // Initialize YouTube Links DataTable
    $(document).ready(function() {
        youtubeTable = $('#youtubeLinksTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('auth/settings/youtube-get-links') ?>",
                "type": "POST",
            },
            "columns": [
                {
                    "data": "title",
                    "className": "text-left max-w-md",
                    "render": function(data, type, row) {
                        return `
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center text-white">
                                    <i class="fab fa-youtube w-5 h-5"></i>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">${data}</div>
                                    <div class="text-sm text-gray-500">YouTube Video</div>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    "data": "link",
                    "render": function(data, type, row) {
                        const displayUrl = data.length > 40 ? data.substring(0, 40) + '...' : data;
                        return `
                            <div class="flex items-center">
                                <i class="fas fa-link w-4 h-4 mr-2 text-blue-500"></i>
                                <a href="${data}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm" title="${data}">
                                    ${displayUrl}
                                </a>
                            </div>
                        `;
                    }
                },
                {
                    "data": "created_at",
                    "className": "text-left",
                    "render": function(data) {
                        const formattedDate = formatDate(data);
                        return `
                            <div class="flex items-center">
                                <i class="fas fa-calendar w-4 h-4 mr-2 text-yellow-500"></i>
                                <span class="text-sm">${formattedDate}</span>
                            </div>
                        `;
                    }
                },
                {
                    "data": "id",
                    "orderable": false,
                    "className": "text-right text-sm font-medium",
                    "render": function(data, type, row) {
                        return `
                            <div class="flex justify-end space-x-1">
                                <button onclick="editYouTubeLink('${row.id}')" 
                                        class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 hover:bg-blue-200 text-blue-600 hover:text-blue-700 rounded-md transition-colors duration-200"
                                        title="Edit">
                                    <i class="fas fa-edit w-4 h-4"></i>
                                </button>
                                <button onclick="deleteYouTubeLink('${row.id}')" 
                                        class="inline-flex items-center justify-center w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 hover:text-red-700 rounded-md transition-colors duration-200"
                                        title="Delete">
                                    <i class="fas fa-trash w-4 h-4"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            "responsive": true,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search YouTube links...",
            },
            "order": [[2, 'desc']], // Order by created_at descending
            "createdRow": function(row, data, dataIndex) {
                $(row).addClass('hover:bg-gray-50');
            }
        });

        // Add YouTube Link Form Handler
        $('#addYouTubeLinkForm').on('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            const editId = $('#youtubeId').val();
            
            // Show loading state
            submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...').prop('disabled', true);
            
            const formData = {
                title: $('#youtubeTitle').val(),
                link: $('#youtubeUrl').val(),
                description: $('#youtubeDescription').val(),
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            };
            
            if (editId) {
                formData.id = editId;
            }
            
            $.ajax({
                url: '<?= base_url('auth/settings/youtube-save') ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        showNotification('success', response.message);
                        closeYouTubeModal();
                        youtubeTable.ajax.reload();
                        $('#addYouTubeLinkForm')[0].reset();
                    } else {
                        showNotification('error', response.message || 'An error occurred');
                    }

                    if (response.errors) {
                        showFormErrors(response.errors);
                    }
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'An error occurred while saving';

                    const response = JSON.parse(xhr.responseText);

                    if (response.errors) {
                        showFormErrors(response.errors);
                        showNotification('error', 'Please correct the errors and try again');
                    } else {
                        showNotification('error', response.message || 'An error occurred while processing your request');
                    }
                },
                complete: function() {
                    submitBtn.html(originalText).prop('disabled', false);
                }
            });
        });
    });

    // Modal Functions
    function openYouTubeModal() {
        $('#youtubeModal').removeClass('hidden');
        $('#youtubeTitle').focus();
    }

    function closeYouTubeModal() {
        $('#youtubeModal').addClass('hidden');
        $('#addYouTubeLinkForm')[0].reset();
        $('#youtubeId').val(''); // Clear the edit ID
        // Clear any edit state
        $('#addYouTubeLinkForm').removeData('edit-id');
        $('#modalTitle').text('Add YouTube Link');
    }

    // Edit YouTube Link
    function editYouTubeLink(id) {
        $.ajax({
            url: `<?= base_url('auth/settings/youtube/get/') ?>${id}`,
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.status === 'success') {
                    const link = response.data;
                    $('#youtubeTitle').val(link.title);
                    $('#youtubeUrl').val(link.link || link.url); // Handle both field names
                    $('#youtubeId').val(id); // Set the ID for editing
                    $('#youtubeDescription').val(link.description || ''); // Set description if available
                    $('#modalTitle').text('Edit YouTube Link');
                    openYouTubeModal();
                } else {
                    showNotification(response.message || 'Failed to load link data', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Edit error:', xhr.responseText);
                showNotification('Failed to load link data', 'error');
            }
        });
    }

    // Delete YouTube Link
    function deleteYouTubeLink(id) {
        if (confirm('Are you sure you want to delete this YouTube link? This action cannot be undone.')) {
            $.ajax({
                url: `<?= base_url('auth/settings/youtube/delete/') ?>${id}`,
                type: 'DELETE',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        showNotification('success', response.message);
                        $('#youtubeLinksTable').DataTable().ajax.reload();
                    } else {
                        showNotification('error', response.message || 'Failed to delete link');
                    }
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'Failed to delete link';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showNotification('error', errorMessage);
                }
            });
        }
    }

    // Close modal when clicking outside
    $('#youtubeModal').on('click', function(e) {
        if (e.target === this) {
            closeYouTubeModal();
        }
    });

    // Close modal with ESC key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && !$('#youtubeModal').hasClass('hidden')) {
            closeYouTubeModal();
        }
    });

    // Clear field errors on input
    $('input, textarea, select').on('input change', function() {
        $(this).removeClass('is-invalid border-red-500');
        $(this).next('.invalid-feedback').remove();
    });
</script>
<?php $this->endSection(); ?>
