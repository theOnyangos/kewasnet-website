<?php $this->extend('backendV2/layouts/main'); ?>

<?php $this->section('title'); ?>
FAQs Settings
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
    <main class="flex-1 overflow-y-auto p-6">
        <?= $this->include('backendV2/pages/settings/partials/settings_navigation'); ?>

        <!-- FAQs Panel -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Frequently Asked Questions</h2>
                    <p class="mt-1 text-sm text-gray-500">Manage FAQ content for your website</p>
                </div>
                <button onclick="openAddFaqModal()" class="gradient-btn text-white px-8 py-2 rounded-[50px] hover:bg-blue-700 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4 mr-2 inline z-10"></i>
                    <span>Add FAQ</span>
                </button>
            </div>
            
            <!-- FAQ Categories -->
            <div class="mb-6">
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-gray-700">Filter by Category:</span>
                    <select id="categoryFilter" onchange="filterByCategory()" class="select2 border border-gray-300 rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        <option value="general">General</option>
                        <option value="programs">Programs</option>
                        <option value="membership">Membership</option>
                        <option value="events">Events</option>
                        <option value="donations">Donations</option>
                        <option value="technical">Technical</option>
                    </select>
                </div>
            </div>
            
            <!-- FAQs Table -->
            <div class="overflow-x-auto">
                <table id="faqsTable" class="data-table stripe min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Question</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Table content will be populated by DataTables -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add/Edit FAQ Modal -->
        <div id="faqModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border max-w-2xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900" id="faqModalTitle">Add FAQ</h3>
                        <button onclick="closeFaqModal()" class="text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                    
                    <form id="faqForm" class="space-y-4">
                        <input type="hidden" id="faqId" name="id">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <select id="faqCategory" name="category" required class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Category</option>
                                    <option value="general">General</option>
                                    <option value="programs">Programs</option>
                                    <option value="membership">Membership</option>
                                    <option value="events">Events</option>
                                    <option value="donations">Donations</option>
                                    <option value="technical">Technical</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select id="faqStatus" name="status" class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="active">Active</option>
                                    <option value="draft">Draft</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Question</label>
                            <input type="text" id="faqQuestion" name="question" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter the frequently asked question">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Answer</label>
                            <textarea id="faqAnswer" name="answer" required rows="6" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter the detailed answer to the question"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                                <input type="number" id="faqOrder" name="display_order" min="0" value="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tags (Optional)</label>
                                <input type="text" id="faqTags" name="tags" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="water, community, development">
                                <p class="text-xs text-gray-500 mt-1">Comma separated tags</p>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" id="faqFeatured" name="is_featured" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Featured FAQ (appears prominently)</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" id="faqSearchable" name="searchable" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Include in search results</span>
                            </label>
                        </div>

                        <!-- CSRF Token -->
                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                        
                        <!-- Progress Bar (Hidden by default) -->
                        <div id="uploadProgress" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Save Progress</label>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div id="progressBar" class="bg-secondary h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span id="progressText">Preparing...</span>
                                <span id="progressPercent">0%</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-3 pt-4">
                            <button type="submit" id="submitBtn" class="flex-1 gradient-btn text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-save w-4 h-4 mr-2 inline z-20"></i>
                                <span class="">Save FAQ</span>
                            </button>
                            <button type="button" onclick="closeFaqModal()" id="cancelBtn" class="flex-1 bg-primaryShades-100 text-primary py-2 px-4 rounded-lg hover:bg-primaryShades-200 transition-colors">
                                <i class="fas fa-times w-4 h-4 mr-2 inline z-20"></i>
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- FAQ Preview Modal -->
        <div id="faqPreviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border max-w-2xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">FAQ Preview</h3>
                        <button onclick="closeFaqPreviewModal()" class="text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span id="previewCategory" class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded"></span>
                                <span id="previewStatus" class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded"></span>
                            </div>
                            <h4 id="previewQuestion" class="text-lg font-medium text-gray-900 mb-3"></h4>
                            <div id="previewAnswer" class="text-gray-700 leading-relaxed"></div>
                            <div id="previewTags" class="mt-3 flex flex-wrap gap-1"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?php $this->endSection(); ?>

<?= $this->section('scripts') ?>
<script>
    let faqsTable;

    // Notification function
    function showNotification(type, message) {
        const alertTypes = {
            'success': 'bg-green-100 border-green-400 text-green-700',
            'error': 'bg-red-100 border-red-400 text-red-700',
            'warning': 'bg-yellow-100 border-yellow-400 text-yellow-700',
            'info': 'bg-blue-100 border-blue-400 text-blue-700'
        };
        
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-[9999] ${alertTypes[type] || alertTypes['info']} border px-4 py-3 rounded shadow-lg transition-all duration-300 max-w-md`;
        notification.innerHTML = `
            <div class="flex items-center justify-between">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 font-bold">×</button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    // Modal functions
    function openAddFaqModal() {
        document.getElementById('faqModalTitle').textContent = 'Add FAQ';
        document.getElementById('faqForm').reset();
        document.getElementById('faqId').value = '';
        document.getElementById('uploadProgress').classList.add('hidden');
        document.getElementById('faqModal').classList.remove('hidden');
        
        // Reset progress bar
        $('#progressBar').removeClass('bg-red-600').addClass('bg-secondary').css('width', '0%');
        $('#progressText').text('Preparing...');
        $('#progressPercent').text('0%');
        
        // Reset button states
        $('#submitBtn').prop('disabled', false).html('<i class="fas fa-save w-4 h-4 mr-2 inline z-20"></i><span class="">Save FAQ</span>');
        $('#cancelBtn').prop('disabled', false);
    }
    
    function closeFaqModal() {
        $('#faqModal').addClass('hidden');
        $('#faqForm')[0].reset();
        $('#faqId').val(''); // Clear the edit ID
        $('#faqModalTitle').text('Add FAQ');
        
        // Reset progress bar
        $('#uploadProgress').addClass('hidden');
        $('#progressBar').removeClass('bg-red-600').addClass('bg-secondary').css('width', '0%');
        $('#progressText').text('Preparing...');
        $('#progressPercent').text('0%');
        
        // Reset button states
        $('#submitBtn').prop('disabled', false).html('<i class="fas fa-save w-4 h-4 mr-2 inline z-20"></i><span class="">Save FAQ</span>');
        $('#cancelBtn').prop('disabled', false);
    }
    
    function closeFaqPreviewModal() {
        document.getElementById('faqPreviewModal').classList.add('hidden');
    }
    
    // Initialize FAQs DataTable
    $(document).ready(function() {
        lucide.createIcons();
        
        try {
            faqsTable = $('#faqsTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "<?= base_url('auth/settings/get-faqs') ?>",
                    "type": "POST",
                    "dataSrc": function(json) {
                        console.log('FAQ DataTable Response:', json);
                        console.log('Records Total:', json.recordsTotal, 'Filtered:', json.recordsFiltered, 'Data length:', json.data ? json.data.length : 0);
                        if (json.data && json.data.length > 0) {
                            console.log('First FAQ sample:', json.data[0]);
                        }
                        return json.data;
                    },
                    "error": function(xhr, error, code) {
                        console.error('DataTable AJAX error:', error, code, xhr);
                        alert('Error loading FAQs data. Please check console for details.');
                    }
                },
            "columns": [
                {
                    "data": "question",
                    "defaultContent": "",
                    "className": "text-left max-w-md",
                    "render": function(data, type, row) {
                        if (!data) return '<span class="text-gray-400">No question</span>';
                        const truncated = data.length > 80 ? data.substring(0, 80) + '...' : data;
                        return `
                            <div class="font-medium text-gray-900">${truncated}</div>
                            ${row.is_featured == '1' ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mt-1"><i class="fas fa-star w-3 h-3 mr-1"></i>Featured</span>' : ''}
                        `;
                    }
                },
                {
                    "data": "category",
                    "defaultContent": "general",
                    "className": "text-center",
                    "render": function(data, type, row) {
                        const category = data || 'general';
                        const categoryColors = {
                            'general': 'bg-gray-100 text-gray-800',
                            'programs': 'bg-blue-100 text-blue-800',
                            'membership': 'bg-green-100 text-green-800',
                            'events': 'bg-purple-100 text-purple-800',
                            'donations': 'bg-yellow-100 text-yellow-800',
                            'technical': 'bg-red-100 text-red-800'
                        };
                        
                        const colorClass = categoryColors[category] || 'bg-gray-100 text-gray-800';
                        const displayText = category.charAt(0).toUpperCase() + category.slice(1);
                        
                        return `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${colorClass}">${displayText}</span>`;
                    }
                },
                {
                    "data": "status",
                    "defaultContent": "active",
                    "className": "text-center",
                    "render": function(data, type, row) {
                        const status = data || 'active';
                        const statusColors = {
                            'active': 'bg-green-100 text-green-800',
                            'draft': 'bg-yellow-100 text-yellow-800',
                            'archived': 'bg-red-100 text-red-800'
                        };
                        
                        const colorClass = statusColors[status] || 'bg-gray-100 text-gray-800';
                        const displayText = status.charAt(0).toUpperCase() + status.slice(1);
                        
                        return `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${colorClass}">${displayText}</span>`;
                    }
                },
                {
                    "data": "created_at",
                    "defaultContent": "",
                    "className": "text-center text-sm text-gray-500",
                    "render": function(data, type, row) {
                        if (!data) return '<span class="text-gray-400">N/A</span>';
                        try {
                            const date = new Date(data);
                            return `<span class="font-medium text-sm text-gray-900">${date.toLocaleDateString()}</span>`;
                        } catch (e) {
                            console.error('Date formatting error:', e, data);
                            return '<span class="text-gray-400">Invalid date</span>';
                        }
                    }
                },
                {
                    "data": null,
                    "orderable": false,
                    "className": "text-center",
                    "render": function(data, type, row) {
                        return `
                            <div class="flex justify-end space-x-1">
                                <button onclick="previewFaq('${row.id}')" 
                                        class="inline-flex items-center justify-center w-8 h-8 bg-green-100 hover:bg-green-200 text-green-600 hover:text-green-700 rounded-md transition-colors duration-200"
                                        title="Preview">
                                    <i class="fas fa-eye w-4 h-4"></i>
                                </button>
                                <button onclick="editFaq('${row.id}')" 
                                        class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 hover:bg-blue-200 text-blue-600 hover:text-blue-700 rounded-md transition-colors duration-200"
                                        title="Edit">
                                    <i class="fas fa-edit w-4 h-4"></i>
                                </button>
                                <button onclick="deleteFaq('${row.id}')" 
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
                "searchPlaceholder": "Search FAQs...",
                "processing": "Loading FAQs...",
                "emptyTable": "No FAQs found",
                "info": "Showing _START_ to _END_ of _TOTAL_ FAQs",
                "infoEmpty": "Showing 0 to 0 of 0 FAQs",
                "infoFiltered": "(filtered from _MAX_ total FAQs)",
                "lengthMenu": "Show _MENU_ FAQs per page",
                "search": "Search FAQs:",
                "zeroRecords": "No matching FAQs found"
            },
            "dom": '<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4"<"flex items-center"l><"flex items-center"f>>rtip',
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            "order": [[3, "desc"]],
            "drawCallback": function() {
                lucide.createIcons();
            }
        });
        } catch (error) {
            console.error('Error initializing DataTable:', error);
            alert('Error initializing FAQs table: ' + error.message);
        }

        // FAQ Form Handler with Progress Bar
        $('#faqForm').on('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = $('#submitBtn');
            const cancelBtn = $('#cancelBtn');
            const originalText = submitBtn.html();
            const editId = $('#faqId').val();
            const progressContainer = $('#uploadProgress');
            const progressBar = $('#progressBar');
            const progressText = $('#progressText');
            const progressPercent = $('#progressPercent');
            
            // Show loading state
            submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...').prop('disabled', true);
            cancelBtn.prop('disabled', true);
            
            // Show progress bar
            progressContainer.removeClass('hidden');
            progressText.text('Preparing...');
            progressPercent.text('0%');
            progressBar.css('width', '0%');
            
            const formData = new FormData(this);
            
            $.ajax({
                url: '<?= base_url('auth/settings/faq-save') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    
                    // Simulate progress for form submission
                    setTimeout(function() {
                        progressBar.css('width', '30%');
                        progressPercent.text('30%');
                        progressText.text('Validating data...');
                    }, 100);
                    
                    setTimeout(function() {
                        progressBar.css('width', '60%');
                        progressPercent.text('60%');
                        progressText.text('Saving FAQ...');
                    }, 300);
                    
                    return xhr;
                },
                success: function(response) {
                    // Complete the progress bar
                    progressBar.css('width', '100%');
                    progressPercent.text('100%');
                    progressText.text('Save complete!');
                    
                    // Small delay to show completion
                    setTimeout(function() {
                        if (response.status === 'success') {
                            showNotification('success', response.message);
                            closeFaqModal();
                            faqsTable.ajax.reload();
                            $('#faqForm')[0].reset();
                        } else {
                            showNotification('error', response.message || 'An error occurred');
                        }

                        if (response.errors) {
                            showFormErrors(response.errors);
                        }
                    }, 200);
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'An error occurred while saving';
                    
                    progressText.text('Save failed');
                    progressBar.removeClass('bg-secondary').addClass('bg-red-600');

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
                    setTimeout(function() {
                        progressContainer.addClass('hidden');
                        progressBar.removeClass('bg-red-600').addClass('bg-secondary');
                        progressBar.css('width', '0%');
                    }, 1500);
                }
            });
        });
    });
    
    // Edit FAQ
    function editFaq(id) {
        $.ajax({
            url: `<?= base_url('auth/settings/faq/get/') ?>${id}`,
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.status === 'success') {
                    const data = response.data;
                    document.getElementById('faqModalTitle').textContent = 'Edit FAQ';
                    document.getElementById('faqId').value = data.id;
                    document.getElementById('faqQuestion').value = data.question;
                    document.getElementById('faqAnswer').value = data.answer;
                    document.getElementById('faqOrder').value = data.display_order || 0;
                    document.getElementById('faqTags').value = data.tags || '';
                    document.getElementById('faqFeatured').checked = data.is_featured == '1';
                    document.getElementById('faqSearchable').checked = data.searchable == '1';
                    
                    // Set Select2 values and trigger change
                    $('#faqCategory').val(data.category).trigger('change');
                    $('#faqStatus').val(data.status).trigger('change');
                    
                    document.getElementById('faqModal').classList.remove('hidden');
                } else {
                    showNotification('error', response.message || 'Failed to load FAQ details');
                }
            },
            error: function() {
                showNotification('error', 'Failed to load FAQ details');
            }
        });
    }
    
    function previewFaq(id) {
        console.log('Preview FAQ called with ID:', id);
        
        $.ajax({
            url: `<?= base_url('auth/settings/faq/get/') ?>${id}`,
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Preview FAQ response:', response);
                
                if (response.status === 'success') {
                    const data = response.data;
                    
                    // Safely set category
                    const categoryEl = document.getElementById('previewCategory');
                    if (categoryEl && data.category) {
                        categoryEl.textContent = data.category.toUpperCase();
                    }
                    
                    // Safely set status
                    const statusEl = document.getElementById('previewStatus');
                    if (statusEl && data.status) {
                        statusEl.textContent = data.status.toUpperCase();
                    }
                    
                    // Safely set question
                    const questionEl = document.getElementById('previewQuestion');
                    if (questionEl) {
                        questionEl.textContent = data.question || 'No question';
                    }
                    
                    // Safely set answer
                    const answerEl = document.getElementById('previewAnswer');
                    if (answerEl) {
                        answerEl.innerHTML = data.answer ? data.answer.replace(/\n/g, '<br>') : 'No answer';
                    }
                    
                    // Show tags
                    const tagsContainer = document.getElementById('previewTags');
                    if (tagsContainer) {
                        tagsContainer.innerHTML = '';
                        if (data.tags) {
                            data.tags.split(',').forEach(tag => {
                                const tagEl = document.createElement('span');
                                tagEl.className = 'px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded';
                                tagEl.textContent = tag.trim();
                                tagsContainer.appendChild(tagEl);
                            });
                        }
                    }
                    
                    // Show modal
                    const modal = document.getElementById('faqPreviewModal');
                    if (modal) {
                        modal.classList.remove('hidden');
                        console.log('Preview modal opened');
                    } else {
                        console.error('Preview modal not found!');
                    }
                } else {
                    console.error('Preview failed:', response.message);
                    showNotification('error', response.message || 'Failed to load FAQ preview');
                }
            },
            error: function(xhr, status, error) {
                console.error('Preview AJAX error:', status, error, xhr);
                showNotification('error', 'Failed to load FAQ preview');
            }
        });
    }
    
    function deleteFaq(id) {
        if (confirm('Are you sure you want to delete this FAQ? This action cannot be undone.')) {
            $.ajax({
                url: `<?= base_url('auth/settings/faq/delete/') ?>${id}`,
                type: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        showNotification('success', response.message);
                        faqsTable.ajax.reload();
                    } else {
                        showNotification('error', response.message || 'Failed to delete FAQ');
                    }
                },
                error: function() {
                    showNotification('error', 'An error occurred while deleting the FAQ');
                }
            });
        }
    }
    
    // Filter by category
    function filterByCategory() {
        const category = document.getElementById('categoryFilter').value;
        faqsTable.column(1).search(category).draw();
    }
</script>
<?php $this->endSection(); ?>
