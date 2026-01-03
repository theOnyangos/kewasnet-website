<?php $this->extend('backendV2/layouts/main'); ?>

<?php $this->section('title'); ?>
Partners Settings
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
<main class="flex-1 overflow-y-auto p-6">
    <?= $this->include('backendV2/pages/settings/partials/settings_navigation'); ?>

    <!-- Partners Panel -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Partners</h2>
                <p class="mt-1 text-sm text-gray-500">Manage your organization partners and sponsors</p>
            </div>
            <button onclick="openAddPartnerModal()" class="gradient-btn text-white px-8 py-2 rounded-[50px] hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus w-4 h-4 mr-2 inline z-10"></i>
                <span>Add Partner</span>
            </button>
        </div>
        
        <!-- Partners Table -->
        <div class="overflow-x-auto">
            <table id="partnersTable" class="data-table stripe hover" style="width:100%">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col">Partner Name</th>
                        <th scope="col">Partnership Type</th>
                        <th scope="col">Website</th>
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

    <!-- Add/Edit Partner Modal -->
    <div id="partnerModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-[600px] shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Add Partner</h3>
                    <button onclick="closePartnerModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times w-5 h-5"></i>
                    </button>
                </div>
                
                <form id="addPartnerForm" class="text-left space-y-4">
                    <input type="hidden" id="partnerId" name="id">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Partner Name</label>
                        <input type="text" id="partnerName" name="partner_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter partner name">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Partnership Type</label>
                        <select id="partnerType" name="partnership_type" class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Partnership Type</option>
                            <option value="sponsor">Sponsor</option>
                            <option value="strategic_partner">Strategic Partner</option>
                            <option value="supporter">Supporter</option>
                            <option value="donor">Donor</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Website URL (Optional)</label>
                        <input type="url" id="partnerUrl" name="partner_url" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://partner-website.com">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Partner Logo</label>
                        <input type="file" id="partnerLogo" name="partner_logo" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Upload PNG, JPG, or SVG format. Max size: 2MB</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                        <textarea id="partnerDescription" name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Brief description about the partner"></textarea>
                    </div>
                    
                    <!-- Logo Preview -->
                    <div id="logoPreview" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Logo Preview</label>
                        <img id="previewImage" src="" alt="Logo Preview" class="w-20 h-20 object-contain border border-gray-300 rounded-lg">
                    </div>

                    <!-- CSRF Token -->
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    
                    <!-- Progress Bar (Hidden by default) -->
                    <div id="uploadProgress" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Progress</label>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div id="progressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span id="progressText">Preparing upload...</span>
                            <span id="progressPercent">0%</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 pt-4">
                        <button type="submit" id="submitBtn" class="flex-1 gradient-btn text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-save w-4 h-4 mr-2 inline z-20"></i>
                            <span class="">Save</span>
                        </button>
                        <button type="button" onclick="closePartnerModal()" id="cancelBtn" class="flex-1 bg-primaryShades-100 text-primary py-2 px-4 rounded-lg hover:bg-primaryShades-200 transition-colors">
                            <i class="fas fa-times w-4 h-4 mr-2 inline z-20"></i>
                            Cancel
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
    let partnersTable;

    // Modal functions
    function openAddPartnerModal() {
        document.getElementById('modalTitle').textContent = 'Add Partner';
        document.getElementById('addPartnerForm').reset();
        document.getElementById('partnerId').value = '';
        document.getElementById('logoPreview').classList.add('hidden');
        document.getElementById('uploadProgress').classList.add('hidden');
        document.getElementById('partnerModal').classList.remove('hidden');
        
        // Reset progress bar
        $('#progressBar').removeClass('bg-red-600').addClass('bg-blue-600').css('width', '0%');
        $('#progressText').text('Preparing upload...');
        $('#progressPercent').text('0%');
        
        // Reset button states
        $('#submitBtn').prop('disabled', false).html('<i class="fas fa-save w-4 h-4 mr-2 inline z-20"></i><span class="">Save</span>');
        $('#cancelBtn').prop('disabled', false);
    }
    
    // Initialize Partners DataTable
    $(document).ready(function() {
        partnersTable = $('#partnersTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('auth/settings/get-partners') ?>",
                "type": "POST",
            },
            "columns": [
                {
                    "data": "partner_name",
                    "className": "text-left max-w-md",
                    "render": function(data, type, row) {
                        // Switch case for showing partner type
                        let partnershipType = row.partnership_type || 'Partner';
                        switch (partnershipType) {
                            case 'sponsor':
                                partnershipType = 'Sponsor';
                                break;
                            case 'strategic_partner':
                                partnershipType = 'Strategic Partner';
                                break;
                            case 'supporter':
                                partnershipType = 'Supporter';
                                break;
                            case 'donor':
                                partnershipType = 'Donor';
                                break;
                        }

                        const logoHtml = row.partner_logo ? 
                            `<img src="${row.partner_logo}" alt="${data}" class="w-10 h-10 object-contain rounded-lg">` :
                            `<div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white">
                                <i class="fas fa-handshake w-5 h-5"></i>
                            </div>`;
                        
                        return `
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    ${logoHtml}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">${data}</div>
                                    <div class="text-sm text-gray-500">${partnershipType}</div>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    "data": "partnership_type",
                    "className": "text-left",
                    "render": function(data, type, row) {
                        const typeColors = {
                            'sponsor': 'bg-green-100 text-green-800',
                            'strategic_partner': 'bg-blue-100 text-blue-800',
                            'supporter': 'bg-yellow-100 text-yellow-800',
                            'donor': 'bg-purple-100 text-purple-800'
                        };
                        const colorClass = typeColors[data] || 'bg-gray-100 text-gray-800';
                        
                        // Switch case for showing partner type
                        let partnershipType = row.partnership_type || 'Partner';
                        switch (partnershipType) {
                            case 'sponsor':
                                partnershipType = 'sponsor';
                                break;
                            case 'strategic_partner':
                                partnershipType = 'strategic partner';
                                break;
                            case 'supporter':
                                partnershipType = 'supporter';
                                break;
                            case 'donor':
                                partnershipType = 'donor';
                                break;
                        }
                        
                        return `
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${colorClass}">
                                ${partnershipType}
                            </span>
                        `;
                    }
                },
                {
                    "data": "partner_url",
                    "render": function(data, type, row) {
                        if (!data) {
                            return '<span class="text-gray-400 text-sm">No website</span>';
                        }
                        const displayUrl = data.length > 30 ? data.substring(0, 30) + '...' : data;
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
                                <button onclick="editPartner('${row.id}')" 
                                        class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 hover:bg-blue-200 text-blue-600 hover:text-blue-700 rounded-md transition-colors duration-200"
                                        title="Edit">
                                    <i class="fas fa-edit w-4 h-4"></i>
                                </button>
                                <button onclick="deletePartner('${row.id}')" 
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
                "searchPlaceholder": "Search Partners...",
                "processing": "Loading partners...",
                "emptyTable": "No partners found",
                "info": "Showing _START_ to _END_ of _TOTAL_ partners",
                "infoEmpty": "Showing 0 to 0 of 0 partners",
                "infoFiltered": "(filtered from _MAX_ total partners)",
                "lengthMenu": "Show _MENU_ partners per page",
                "search": "Search partners:",
                "zeroRecords": "No matching partners found"
            },
            "dom": '<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4"<"flex items-center"l><"flex items-center"f>>rtip',
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            "order": [[3, "desc"]]
        });

        // Add Partner Form Handler
        $('#addPartnerForm').on('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = $('#submitBtn');
            const cancelBtn = $('#cancelBtn');
            const originalText = submitBtn.html();
            const editId = $('#partnerId').val();
            const progressContainer = $('#uploadProgress');
            const progressBar = $('#progressBar');
            const progressText = $('#progressText');
            const progressPercent = $('#progressPercent');
            
            // Check if file is selected for progress tracking
            const fileInput = $('#partnerLogo')[0];
            const hasFile = fileInput.files && fileInput.files.length > 0;
            
            // Show loading state
            submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...').prop('disabled', true);
            cancelBtn.prop('disabled', true);
            
            // Show progress bar if file is being uploaded
            if (hasFile) {
                progressContainer.removeClass('hidden');
                progressText.text('Preparing upload...');
                progressPercent.text('0%');
                progressBar.css('width', '0%');
            }
            
            const formData = new FormData(this);
            
            $.ajax({
                url: '<?= base_url('auth/settings/partner-save') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    
                    // Upload progress tracking
                    if (hasFile) {
                        xhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                const percentComplete = Math.round((e.loaded / e.total) * 100);
                                progressBar.css('width', percentComplete + '%');
                                progressPercent.text(percentComplete + '%');
                                
                                if (percentComplete < 100) {
                                    progressText.text('Uploading file...');
                                } else {
                                    progressText.text('Processing...');
                                }
                            }
                        });
                    }
                    
                    return xhr;
                },
                success: function(response) {
                    if (hasFile) {
                        progressBar.css('width', '100%');
                        progressPercent.text('100%');
                        progressText.text('Upload complete!');
                    }
                    
                    if (response.status === 'success') {
                        showNotification('success', response.message);
                        closePartnerModal();
                        partnersTable.ajax.reload();
                        $('#addPartnerForm')[0].reset();
                    } else {
                        showNotification('error', response.message || 'An error occurred');
                    }

                    if (response.errors) {
                        showFormErrors(response.errors);
                    }
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'An error occurred while saving';
                    
                    if (hasFile) {
                        progressText.text('Upload failed');
                        progressBar.removeClass('bg-blue-600').addClass('bg-red-600');
                    }

                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.errors) {
                            showFormErrors(response.errors);
                            showNotification('error', 'Please correct the errors and try again');
                        } else {
                            showNotification('error', response.message || 'An error occurred while processing your request');
                        }
                    } catch (e) {
                        showNotification('error', errorMessage);
                    }
                },
                complete: function() {
                    submitBtn.html(originalText).prop('disabled', false);
                    cancelBtn.prop('disabled', false);
                    
                    // Hide progress bar after a short delay
                    if (hasFile) {
                        setTimeout(function() {
                            progressContainer.addClass('hidden');
                            progressBar.removeClass('bg-red-600').addClass('bg-blue-600');
                            progressBar.css('width', '0%');
                        }, 1500);
                    }
                }
            });
        });

        // Logo preview functionality
        $('#partnerLogo').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImage').src = e.target.result;
                    document.getElementById('logoPreview').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // Modal Functions
    function openPartnerModal() {
        $('#partnerModal').removeClass('hidden');
        $('#partnerName').focus();
    }

    function closePartnerModal() {
        $('#partnerModal').addClass('hidden');
        $('#addPartnerForm')[0].reset();
        $('#partnerId').val(''); // Clear the edit ID
        $('#logoPreview').addClass('hidden');
        $('#modalTitle').text('Add Partner');
        
        // Reset progress bar
        $('#uploadProgress').addClass('hidden');
        $('#progressBar').removeClass('bg-red-600').addClass('bg-blue-600').css('width', '0%');
        $('#progressText').text('Preparing upload...');
        $('#progressPercent').text('0%');
        
        // Reset button states
        $('#submitBtn').prop('disabled', false).html('<i class="fas fa-save w-4 h-4 mr-2 inline z-20"></i><span class="">Save</span>');
        $('#cancelBtn').prop('disabled', false);
    }

    // Edit Partner
    function editPartner(id) {
        $.ajax({
            url: `<?= base_url('auth/settings/partner/get/') ?>${id}`,
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.status === 'success') {
                    const partner = response.data;
                    $('#partnerName').val(partner.partner_name);
                    $('#partnerType').val(partner.partnership_type || '');
                    $('#partnerUrl').val(partner.partner_url || '');
                    $('#partnerDescription').val(partner.description || '');
                    $('#partnerId').val(id); // Set the ID for editing
                    
                    // Show logo preview if exists
                    if (partner.partner_logo) {
                        $('#previewImage').attr('src', partner.partner_logo);
                        $('#logoPreview').removeClass('hidden');
                    }
                    
                    $('#modalTitle').text('Edit Partner');
                    openPartnerModal();
                } else {
                    showNotification('error', response.message || 'Failed to load partner data');
                }
            },
            error: function(xhr, status, error) {
                console.error('Edit error:', xhr.responseText);
                showNotification('error', 'Failed to load partner data');
            }
        });
    }

    // Delete Partner
    function deletePartner(id) {
        if (confirm('Are you sure you want to delete this partner? This action cannot be undone.')) {
            $.ajax({
                url: `<?= base_url('auth/settings/partner/delete/') ?>${id}`,
                type: 'DELETE',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        showNotification('success', response.message);
                        partnersTable.ajax.reload();
                    } else {
                        showNotification('error', response.message || 'Failed to delete partner');
                    }
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'Failed to delete partner';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.message || errorMessage;
                    } catch (e) {
                        // Use default error message
                    }
                    showNotification('error', errorMessage);
                }
            });
        }
    }

    // Close modal when clicking outside
    $(document).on('click', '#partnerModal', function(e) {
        if (e.target === this) {
            closePartnerModal();
        }
    });

    // Utility function to format dates
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

    // Show form errors function
    function showFormErrors(errors) {
        // Clear previous errors
        $('.error-message').remove();
        $('.border-red-500').removeClass('border-red-500');
        
        // Display new errors
        for (const field in errors) {
            const input = $(`[name="${field}"]`);
            if (input.length) {
                input.addClass('border-red-500');
                input.after(`<div class="error-message text-red-500 text-xs mt-1">${errors[field]}</div>`);
            }
        }
    }
</script>
<?php $this->endSection(); ?>
