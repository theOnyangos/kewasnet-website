<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();

    // dd($forum);
    
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
            'pageTitle' => esc($forum->name),
            'pageDescription' => esc($forum->description),
            'breadcrumbs' => [
                ['label' => 'Forums', 'url' => base_url('auth/forums')],
                ['label' => 'Forum Details']
            ],
            'bannerActions' => '
                <div class="flex items-center gap-3">
                    <a href="' . base_url('auth/forums') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                        Back to Forums
                    </a>
                    <a href="' . base_url('auth/forums/' . $forum->id . '/edit') . '" class="inline-flex items-center px-4 py-2 bg-white/90 backdrop-blur-sm border border-white/20 rounded-lg text-gray-800 hover:bg-white transition-colors">
                        <i data-lucide="settings" class="w-4 h-4 mr-2"></i>
                        Edit Forum
                    </a>
                </div>
            '
        ]) ?>

        <div class="px-6 pb-6">
            <!-- Forum Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Discussions Card -->
                <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg border-l-4 border-blue-500 relative overflow-hidden transition-shadow duration-200">
                    <div class="absolute top-4 right-4 text-blue-100 text-4xl">
                        <i data-lucide="message-square"></i>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-100 p-2 rounded-full text-blue-600">
                            <i data-lucide="message-square" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">Discussions</div>
                            <div class="text-2xl font-bold text-gray-800"><?= $forum->discussion_count ?? 0 ?> <span class="text-sm font-normal text-gray-500">discussions</span></div>
                        </div>
                    </div>
                </div>

                <!-- Replies Card -->
                <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg border-l-4 border-green-500 relative overflow-hidden transition-shadow duration-200">
                    <div class="absolute top-4 right-4 text-green-100 text-4xl">
                        <i data-lucide="reply"></i>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="bg-green-100 p-2 rounded-full text-green-600">
                            <i data-lucide="reply" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">Replies</div>
                            <div class="text-2xl font-bold text-gray-800"><?= $forum->total_replies ?? 0 ?> <span class="text-sm font-normal text-gray-500"><?= $forum->total_replies === 1 ? 'reply' : 'replies' ?></span></div>
                        </div>
                    </div>
                </div>

                <!-- Moderators Card -->
                <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg border-l-4 border-purple-500 relative overflow-hidden transition-shadow duration-200">
                    <div class="absolute top-4 right-4 text-purple-100 text-4xl">
                        <i data-lucide="users"></i>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="bg-purple-100 p-2 rounded-full text-purple-600">
                            <i data-lucide="users" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">Moderators</div>
                            <div class="text-2xl font-bold text-gray-800"><?= $forum->moderator_count ?? 0 ?> <span class="text-sm font-normal text-gray-500">moderators</span></div>
                        </div>
                    </div>
                </div>

                <!-- Last Activity Card -->
                <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg border-l-4 border-yellow-500 relative overflow-hidden transition-shadow duration-200">
                    <div class="absolute top-4 right-4 text-yellow-100 text-4xl">
                        <i data-lucide="activity"></i>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="bg-yellow-100 p-2 rounded-full text-yellow-600">
                            <i data-lucide="activity" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">Last Activity</div>
                            <div class="text-xl font-bold text-gray-800">
                                <?= $forum->last_activity_at ? date('M d, Y h:i A', strtotime($forum->last_activity_at)) : 'No activity yet' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit and Delete Actions Section -->
            <div class="flex flex-wrap gap-3 justify-end">
                <!-- Edit Button -->
                <a href="<?= site_url('auth/forums/' . $forum->id . '/edit') ?>" target="_blank" class="inline-flex items-center px-6 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-700 hover:text-blue-800 rounded-lg transition-colors shadow-sm">
                    <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                    <span class="font-medium">Edit Forum</span>
                </a>
                
                <!-- Delete Button -->
                <button onclick="showDeleteForumModal()" class="inline-flex items-center px-6 py-2.5 bg-red-50 hover:bg-red-100 text-red-700 hover:text-red-800 rounded-lg transition-colors shadow-sm">
                    <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                    <span class="font-medium">Delete Forum</span>
                </button>
            </div>
        </div>

        <div class="px-6 pb-6">
        <!-- Discussions Section -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="mb-4 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px" id="resourceTabs">
                    <li class="mr-2">
                        <button class="inline-block p-4 border-b-2 border-primary text-primary rounded-t-lg tab-button active" 
                                data-tab="opportunities">Discussions</button>
                    </li>
                    <li class="mr-2">
                        <button class="inline-block p-4 border-b-2 border-transparent text-gray-500 hover:text-gray-600 hover:border-gray-300 rounded-t-lg tab-button" 
                                data-tab="applications">Moderators</button>
                    </li>
                    <li class="mr-2">
                        <button class="inline-block p-4 border-b-2 border-transparent text-gray-500 hover:text-gray-600 hover:border-gray-300 rounded-t-lg tab-button" 
                                data-tab="members">Members</button>
                    </li>
                </ul>
            </div>

            <div id="discussionTabs" class="tab-content">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Discussions</h2>
                    <button onclick="window.open('<?= site_url('auth/forums/' . $forum->id) . '/discussions/create' ?>', '_blank')" class="gradient-btn text-white px-5 py-2 rounded-[50px] flex items-center space-x-2">
                        <i data-lucide="plus" class="w-4 h-4 mr-1 z-10"></i>
                        <span>Start Discussion</span>
                    </button>
                </div>

                <table id="discussionsTable" class="data-table stripe hover" width="100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">Title</th>
                            <th class="px-6 py-3 text-left">Author</th>
                            <th class="px-6 py-3 text-left">Replies</th>
                            <th class="px-6 py-3 text-left">Views</th>
                            <th class="px-6 py-3 text-left">Last Activity</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class=""></tbody>
                </table>
            </div>

            <div id="moderatorsTabs" class="tab-content hidden">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Moderators</h2>
                    <button onclick="window.open('<?= site_url('auth/forums/' . $forum->id) . '/moderators/create' ?>', '_blank')" class="gradient-btn text-white px-5 py-2 rounded-[50px] flex items-center space-x-2">
                        <i data-lucide="user-plus" class="w-4 h-4 mr-1 z-10"></i>
                        <span>Add Moderator</span>
                    </button>
                </div>

                <table id="moderatorsTable" class="data-table stripe hover" width="100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">Moderator</th>
                            <th class="px-6 py-3 text-left">Assigned By</th>
                            <th class="px-6 py-3 text-left">Since</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div id="membersTabs" class="tab-content hidden">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Members</h2>
                </div>

                <table id="membersTable" class="data-table stripe hover" width="100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">Member</th>
                            <th class="px-6 py-3 text-left">Email</th>
                            <th class="px-6 py-3 text-left">Joined</th>
                            <th class="px-6 py-3 text-left">Role</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Actions</th>
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
    // Initialize DataTables (if needed)
    let discussionsTable, moderatorsTable, membersTable;
    const discussionTableURL = "<?= base_url('auth/forums/discussions/' . $forum->id . '/get') ?>";
    const moderatorsTableURL = "<?= base_url('auth/forums/moderators/' . $forum->id . '/get') ?>";
    const membersTableURL = "<?= base_url('auth/forums/members/' . $forum->id . '/get') ?>";

    function showDeleteForumModal() {
        openModal({
            title: 'Confirm Deletion',
            iconBg: 'bg-blue-100',
            content: `<?= view('backendV2/pages/forums/partials/delete_confirmation') ?>`,
            size: 'sm:max-w-2xl',
            headerClasses: 'sm:flex sm:items-start',
            onOpen: function() {
                // Initialize Lucide icons
                lucide.createIcons();

                // Delete Action
                $('#confirmDeleteBtn').on('click', function() {
                    confirmForumDelete('<?= $forum->id ?>');
                });
            },
            onClose: function() {
                // Clean up
            }
        });
    }

    $(document).ready(function() {
        // Tab switching functionality
        $('.tab-button').on('click', function() {
            const tabId = $(this).data('tab');
            console.log('Switching to tab:', tabId);
            
            // Remove active from all buttons
            $('.tab-button').removeClass('border-primary text-primary active').addClass('border-transparent text-gray-500');
            
            // Add active to clicked button
            $(this).removeClass('border-transparent text-gray-500').addClass('border-primary text-primary active');
            
            // Hide all tab content
            $('.tab-content').hide();
            
            // Show selected tab content
            if(tabId === 'opportunities') {
                $('#discussionTabs').show();
                // Adjust DataTable columns if table exists
                if(typeof discussionsTable !== 'undefined' && discussionsTable) {
                    setTimeout(() => discussionsTable.columns.adjust(), 100);
                }
            } else if(tabId === 'applications') {
                $('#moderatorsTabs').show();
                // Adjust DataTable columns if table exists
                if(typeof moderatorsTable !== 'undefined' && moderatorsTable) {
                    setTimeout(() => moderatorsTable.columns.adjust(), 100);
                }
            } else if(tabId === 'members') {
                $('#membersTabs').show();
                // Adjust DataTable columns if table exists
                if(typeof membersTable !== 'undefined' && membersTable) {
                    setTimeout(() => membersTable.columns.adjust(), 100);
                }
            }
        });

        // Initialize - show opportunities tab by default (matching the active button)
        $('#discussionTabs').show();
        $('#moderatorsTabs').hide();
        $('#membersTabs').hide();

        discussionsTable = $('#discussionsTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": discussionTableURL,
                "type": "POST",
                "error": function(xhr, error, code) {
                    console.error('DataTable AJAX Error:', error, code);
                    console.error('Response:', xhr.responseText);
                },
                "dataSrc": function(json) {
                    console.log('DataTable Response:', json);
                    return json.data || [];
                }
            },
            "columns": [
                {
                    "data": "title",
                    "render": function(data, type, row) {
                        return `<a href="<?= site_url('auth/discussions/view') ?>/${row.id}" 
                                class="text-blue-600 hover:text-blue-800 hover:underline">
                                ${data}
                            </a>`;
                    }
                },
                {
                    "data": "author_profile",
                    "render": function(data, type, row) {
                        // Create user display name
                        let userName = 'Unknown User';
                        if (row.first_name || row.last_name) {
                            userName = `${row.first_name || ''} ${row.last_name || ''}`.trim();
                        } else if (row.user_id) {
                            userName = `User ${row.user_id}`;
                        }

                        // Generate initials
                        let initials = 'UK';
                        if (row.first_name && row.last_name) {
                            initials = `${row.first_name.charAt(0)}${row.last_name.charAt(0)}`.toUpperCase();
                        } else if (row.first_name) {
                            initials = row.first_name.substring(0, 2).toUpperCase();
                        } else if (row.user_id) {
                            initials = row.user_id.toString().slice(-2).toUpperCase();
                        }

                        // Color generation for consistent user avatars
                        const colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-red-500', 'bg-yellow-500', 'bg-indigo-500', 'bg-pink-500', 'bg-teal-500'];
                        const colorIndex = (parseInt(row.user_id) || 0) % colors.length;
                        const bgColor = colors[colorIndex];

                        // If author_profile (user picture) exists and is not null, show image with fallback
                        if (data && data !== null && data !== '') {
                            return `
                                <div class="flex items-center space-x-3">
                                    <img src="${data}" 
                                        alt="${userName}" 
                                        class="w-8 h-8 rounded-full"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-8 h-8 rounded-full ${bgColor} text-white flex items-center justify-center text-xs font-medium" style="display: none;">
                                        ${initials}
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">${userName}</span>
                                </div>
                            `;
                        } else {
                            // No profile picture, show initials avatar
                            return `
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full ${bgColor} text-white flex items-center justify-center text-xs font-medium">
                                        ${initials}
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">${userName}</span>
                                </div>
                            `;
                        }
                    }
                },
                {
                    "data": "reply_count",
                    "className": "text-center"
                },
                {
                    "data": "view_count",
                    "className": "text-center"
                },
                {
                    "data": "last_reply_at",
                    "render": function(data, type, row) {
                        // Use last_reply_at if available, otherwise use created_at
                        const activityDate = data || row.created_at;
                        return activityDate ? `<span class="text-gray-500 text-sm">${timeAgo(activityDate)}</span>` : 'No activity';
                    }
                },
                {
                    "data": "status",
                    "className": "text-start",
                    "render": function(data) {
                        const status = data || 'unknown';
                        
                        // Define status styling
                        let statusClass = '';
                        let statusIcon = '';
                        let statusText = status.charAt(0).toUpperCase() + status.slice(1);
                        
                        switch(status.toLowerCase()) {
                            case 'active':
                                statusClass = 'bg-green-100 text-green-800';
                                statusIcon = 'check-circle';
                                break;
                            case 'hidden':
                                statusClass = 'bg-yellow-100 text-yellow-800';
                                statusIcon = 'eye-off';
                                break;
                            case 'reported':
                                statusClass = 'bg-orange-100 text-orange-800';
                                statusIcon = 'flag';
                                break;
                            case 'deleted':
                                statusClass = 'bg-red-100 text-red-800';
                                statusIcon = 'trash-2';
                                break;
                            default:
                                statusClass = 'bg-gray-100 text-gray-800';
                                statusIcon = 'help-circle';
                                statusText = 'Unknown';
                                break;
                        }
                        
                        return `
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                                <i data-lucide="${statusIcon}" class="w-3 h-3 mr-1"></i>
                                ${statusText}
                            </span>
                        `;
                    }
                },
                {
                    "data": "id",
                    "orderable": false,
                    "render": function(data, type, row) {
                        return `
                            <div class="flex space-x-2">
                                <a href="<?= base_url('auth/forums/discussions') ?>/${data}/edit" 
                                class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50"
                                title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                <button onclick="confirmDeleteDiscussion('${data}')"
                                        class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50"
                                        title="Delete">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            "responsive": true,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search discussions...",
            },
            "order": [[4, 'desc']], // Default sort by last activity
            "drawCallback": function(settings) {
                // Reinitialize Lucide icons after each draw
                lucide.createIcons();
            }
        });

        // Moderators table initialization
        moderatorsTable = $('#moderatorsTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": moderatorsTableURL,
                "type": "POST",
                "error": function(xhr, error, code) {
                    console.error('Moderators DataTable AJAX Error:', error, code);
                    console.error('Response:', xhr.responseText);
                },
                "dataSrc": function(json) {
                    console.log('Moderators DataTable Response:', json);
                    return json.data || [];
                }
            },
            "columns": [
                {
                    "data": "moderator_name",
                    "render": function(data, type, row) {
                        // Generate user initials as fallback
                        let initials = 'UK';
                        if (row.moderator_name) {
                            const names = row.moderator_name.split(' ');
                            if (names.length >= 2) {
                                initials = `${names[0].charAt(0)}${names[names.length-1].charAt(0)}`.toUpperCase();
                            } else {
                                initials = row.moderator_name.substring(0, 2).toUpperCase();
                            }
                        }

                        // Color generation for consistent user avatars
                        const colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-red-500', 'bg-yellow-500', 'bg-indigo-500', 'bg-pink-500', 'bg-teal-500'];
                        const colorIndex = (parseInt(row.user_id) || 0) % colors.length;
                        const bgColor = colors[colorIndex];

                        // Use moderator_profile as the image URL
                        const profileImage = row.moderator_profile;
                        const moderatorTitle = row.moderator_title || 'Moderator';

                        if (profileImage && profileImage !== null && profileImage !== '') {
                            return `
                                <div class="flex items-center space-x-3">
                                    <img src="${profileImage}" 
                                         alt="${data}" 
                                         class="w-10 h-10 rounded-full border-2 border-gray-200"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-10 h-10 rounded-full ${bgColor} text-white flex items-center justify-center text-sm font-medium border-2 border-gray-200" style="display: none;">
                                        ${initials}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">${data}</div>
                                        <div class="text-sm text-gray-500">${moderatorTitle}</div>
                                    </div>
                                </div>
                            `;
                        } else {
                            return `
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full ${bgColor} text-white flex items-center justify-center text-sm font-medium border-2 border-gray-200">
                                        ${initials}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">${data}</div>
                                        <div class="text-sm text-gray-500">${moderatorTitle}</div>
                                    </div>
                                </div>
                            `;
                        }
                    }
                },
                {
                    "data": "assigned_by",
                    "render": function(data) {
                        return data && data.trim() !== '' ? data : 'System Admin';
                    }
                },
                {
                    "data": "assigned_at",
                    "render": function(data) {
                        if (data) {
                            const date = new Date(data);
                            return `
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900">${date.toLocaleDateString('en-US', { 
                                        year: 'numeric', 
                                        month: 'short', 
                                        day: 'numeric' 
                                    })}</div>
                                    <div class="text-gray-500">${date.toLocaleTimeString('en-US', { 
                                        hour: '2-digit', 
                                        minute: '2-digit' 
                                    })}</div>
                                </div>
                            `;
                        }
                        return 'N/A';
                    }
                },
                {
                    "data": "is_active",
                    "render": function(data, type, row) {
                        const isActive = data === "1" || data === 1 || data === true;
                        return `
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                            }">
                                <i data-lucide="${isActive ? 'check-circle' : 'x-circle'}" class="w-3 h-3 mr-1"></i>
                                ${isActive ? 'Active' : 'Inactive'}
                            </span>
                        `;
                    }
                },
                {
                    "data": "id",
                    "orderable": false,
                    "render": function(data, type, row) {
                        const isActive = row.is_active === "1" || row.is_active === 1 || row.is_active === true;
                        
                        let actions = `
                            <div class="flex space-x-2">
                                <button onclick="revokeModerator('${data}')" 
                                        class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50"
                                        title="Remove Moderator">
                                    <i data-lucide="user-x" class="w-4 h-4"></i>
                                </button>
                        `;
                        
                        if (!isActive) {
                            actions += `
                                <button onclick="reactivateModerator('${data}')" 
                                        class="text-green-600 hover:text-green-900 p-1 rounded-full hover:bg-green-50"
                                        title="Reactivate Moderator">
                                    <i data-lucide="user-check" class="w-4 h-4"></i>
                                </button>
                            `;
                        }
                        
                        actions += `</div>`;
                        return actions;
                    }
                }
            ],
            "responsive": true,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search moderators...",
            },
            "drawCallback": function(settings) {
                // Reinitialize Lucide icons after each draw
                lucide.createIcons();
            }
        });

        // Members table initialization
        membersTable = $('#membersTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": membersTableURL,
                "type": "POST",
                "error": function(xhr, error, code) {
                    console.error('Members DataTable AJAX Error:', error, code);
                    console.error('Response:', xhr.responseText);
                    console.error('Status:', xhr.status);
                    console.error('Status Text:', xhr.statusText);
                },
                "dataSrc": function(json) {
                    console.log('Members DataTable Response:', json);
                    if (json.error) {
                        console.error('Server returned error:', json.error);
                    }
                    return json.data || [];
                }
            },
            "columns": [
                {
                    "data": "member_name",
                    "render": function(data, type, row) {
                        // Generate user initials as fallback
                        let initials = 'UK';
                        if (row.member_name) {
                            const names = row.member_name.split(' ');
                            if (names.length >= 2) {
                                initials = `${names[0].charAt(0)}${names[names.length-1].charAt(0)}`.toUpperCase();
                            } else {
                                initials = row.member_name.substring(0, 2).toUpperCase();
                            }
                        }

                        // Color generation for consistent user avatars
                        const colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-red-500', 'bg-yellow-500', 'bg-indigo-500', 'bg-pink-500', 'bg-teal-500'];
                        const colorIndex = (parseInt(row.user_id) || 0) % colors.length;
                        const bgColor = colors[colorIndex];

                        // Use member_profile as the image URL
                        const profileImage = row.member_profile;

                        if (profileImage && profileImage !== null && profileImage !== '') {
                            return `
                                <div class="flex items-center space-x-3">
                                    <img src="${profileImage}" 
                                         alt="${data}" 
                                         class="w-10 h-10 rounded-full border-2 border-gray-200"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-10 h-10 rounded-full ${bgColor} text-white flex items-center justify-center text-sm font-medium border-2 border-gray-200" style="display: none;">
                                        ${initials}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">${data}</div>
                                    </div>
                                </div>
                            `;
                        } else {
                            return `
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full ${bgColor} text-white flex items-center justify-center text-sm font-medium border-2 border-gray-200">
                                        ${initials}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">${data}</div>
                                    </div>
                                </div>
                            `;
                        }
                    }
                },
                {
                    "data": "email",
                    "render": function(data) {
                        return data ? `<a href="mailto:${data}" class="text-blue-600 hover:underline">${data}</a>` : 'N/A';
                    }
                },
                {
                    "data": "joined_at",
                    "render": function(data) {
                        if (data) {
                            const date = new Date(data);
                            return `
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900">${date.toLocaleDateString('en-US', { 
                                        year: 'numeric', 
                                        month: 'short', 
                                        day: 'numeric' 
                                    })}</div>
                                    <div class="text-gray-500">${date.toLocaleTimeString('en-US', { 
                                        hour: '2-digit', 
                                        minute: '2-digit' 
                                    })}</div>
                                </div>
                            `;
                        }
                        return 'N/A';
                    }
                },
                {
                    "data": "role",
                    "render": function(data, type, row) {
                        const isModerator = data === 'moderator';
                        return `
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                isModerator ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'
                            }">
                                <i data-lucide="${isModerator ? 'shield' : 'user'}" class="w-3 h-3 mr-1"></i>
                                ${isModerator ? 'Moderator' : 'Member'}
                            </span>
                        `;
                    }
                },
                {
                    "data": "is_blocked",
                    "render": function(data, type, row) {
                        const isBlocked = data == 1;
                        return `
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                isBlocked ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'
                            }">
                                <i data-lucide="${isBlocked ? 'lock' : 'unlock'}" class="w-3 h-3 mr-1"></i>
                                ${isBlocked ? 'Blocked' : 'Active'}
                            </span>
                        `;
                    }
                },
                {
                    "data": "id",
                    "orderable": false,
                    "render": function(data, type, row) {
                        const isModerator = row.role === 'moderator';
                        const isBlocked = row.is_blocked == 1;
                        
                        let actions = `
                            <div class="flex space-x-2">
                        `;
                        
                        if (!isModerator) {
                            actions += `
                                <button onclick="promoteMember('${data}')" 
                                        class="text-purple-600 hover:text-purple-900 p-1 rounded-full hover:bg-purple-50"
                                        title="Promote to Moderator">
                                    <i data-lucide="shield-plus" class="w-4 h-4"></i>
                                </button>
                            `;
                        }
                        
                        if (isBlocked) {
                            actions += `
                                <button onclick="unblockMember('${data}')" 
                                        class="text-green-600 hover:text-green-900 p-1 rounded-full hover:bg-green-50"
                                        title="Unblock Member">
                                    <i data-lucide="unlock" class="w-4 h-4"></i>
                                </button>
                            `;
                        } else {
                            actions += `
                                <button onclick="blockMember('${data}')" 
                                        class="text-yellow-600 hover:text-yellow-900 p-1 rounded-full hover:bg-yellow-50"
                                        title="Block Member">
                                    <i data-lucide="lock" class="w-4 h-4"></i>
                                </button>
                            `;
                        }
                        
                        actions += `
                                <button onclick="removeMember('${data}')" 
                                        class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50"
                                        title="Remove Member">
                                    <i data-lucide="user-minus" class="w-4 h-4"></i>
                                </button>
                            </div>
                        `;
                        
                        return actions;
                    }
                }
            ],
            "responsive": true,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search members...",
            },
            "drawCallback": function(settings) {
                // Reinitialize Lucide icons after each draw
                lucide.createIcons();
            }
        });

        // Time ago function for last activity
        function timeAgo(date) {
            const seconds = Math.floor((new Date() - new Date(date)) / 1000);
            let interval = Math.floor(seconds / 31536000);
            
            if (interval >= 1) return interval + " year" + (interval === 1 ? "" : "s") + " ago";
            interval = Math.floor(seconds / 2592000);
            if (interval >= 1) return interval + " month" + (interval === 1 ? "" : "s") + " ago";
            interval = Math.floor(seconds / 86400);
            if (interval >= 1) return interval + " day" + (interval === 1 ? "" : "s") + " ago";
            interval = Math.floor(seconds / 3600);
            if (interval >= 1) return interval + " hour" + (interval === 1 ? "" : "s") + " ago";
            interval = Math.floor(seconds / 60);
            if (interval >= 1) return interval + " minute" + (interval === 1 ? "" : "s") + " ago";
            return Math.floor(seconds) + " second" + (seconds === 1 ? "" : "s") + " ago";
        }

        // Delete discussion function with SweetAlert
        function confirmDeleteDiscussion(discussionId) {
            Swal.fire({
                title: 'Delete Discussion?',
                text: "This action cannot be undone. All replies and attachments will be permanently deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `<?= base_url('auth/forums/discussions') ?>/${discussionId}/delete`,
                        type: 'DELETE',
                        dataType: 'json',
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Deleting...',
                                text: 'Please wait',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message || 'Discussion has been deleted successfully.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            
                            // Reload the discussions table
                            discussionsTable.ajax.reload();
                        },
                        error: function(xhr) {
                            let errorMessage = 'Failed to delete discussion';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errorMessage
                            });
                        }
                    });
                }
            });
        }

    });

    // Moderator management functions (global scope for onclick handlers)
    function revokeModerator(moderatorId) {
        Swal.fire({
            title: 'Revoke Moderator?',
            html: `
                <p class="mb-4">Are you sure you want to revoke this moderator's permissions?</p>
                <div class="text-left">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for removal (optional)</label>
                    <textarea id="revoke-notes" class="swal2-input w-full p-2 border border-gray-300 rounded-md" 
                              placeholder="Enter reason for removing this moderator..." 
                              rows="3"></textarea>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, revoke!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            focusConfirm: false,
            preConfirm: () => {
                return {
                    notes: document.getElementById('revoke-notes').value
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const notes = result.value.notes || '';
                
                $.ajax({
                    url: '<?= site_url('auth/forums/moderators/revoke/') ?>' + moderatorId,
                    type: 'POST',
                    data: {
                        notes: notes,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Revoked!',
                            text: 'Moderator has been revoked successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        moderatorsTable.ajax.reload();
                    },
                    error: function(xhr) {
                        let errorMsg = 'Failed to revoke moderator';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMsg
                        });
                    }
                });
            }
        });
    }

    function reactivateModerator(moderatorId) {
        Swal.fire({
            title: 'Reactivate Moderator?',
            text: "Are you sure you want to reactivate this moderator?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, reactivate!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('auth/forums/moderators/reactivate/') ?>' + moderatorId,
                    type: 'POST',
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Reactivated!',
                            text: 'Moderator has been reactivated successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        moderatorsTable.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to reactivate moderator'
                        });
                    }
                });
            }
        });
    }

    // Handle Delete Forum
    function confirmForumDelete(forumId) {
        // Disable submit button during request
        $('#confirmDeleteBtn').prop('disabled', true);
        $('#confirmDeleteBtn').html(`
            <span class="inline-block animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></span>
            Processing...
        `);
        
        $.ajax({
            url: '<?= site_url('auth/forums/') ?>' + forumId,
            method: 'POST',
            dataType: 'json',
            data: {
                csrf_test_name: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.status === 'success') {
                    closeModal();
                    showToast(response.message, 'success');
                    // Redirect to forums list after successful deletion
                    setTimeout(function() {
                        window.location.href = '<?= site_url('auth/forums') ?>';
                    }, 1500);
                } else {
                    showToast(response.message || 'Error deleting forum', 'error');
                    $('#confirmDeleteBtn').prop('disabled', false);
                    $('#confirmDeleteBtn').html('Delete Permanently');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                const errorMsg = response && response.message ? response.message : 'Error deleting forum';
                showToast(errorMsg, 'error');
                $('#confirmDeleteBtn').prop('disabled', false);
                $('#confirmDeleteBtn').html('Delete Permanently');
            },
            complete: function() {
                // Don't re-enable button on success
                if ($('#confirmDeleteBtn').html().includes('Processing')) {
                    $('#confirmDeleteBtn').prop('disabled', false);
                    $('#confirmDeleteBtn').html('Delete Permanently');
                }
            }
        });
    }

    // Function to show toast notifications
    function showToast(message, type = 'success') {
        // Remove existing toasts
        $('.custom-toast').remove();
        
        // Create toast element
        var toast = $(`
            <div class="custom-toast fixed top-4 right-4 px-4 py-2 rounded-md shadow-lg text-white z-50 ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            }">
                <div class="flex items-center">
                    <i data-lucide="${
                        type === 'success' ? 'check-circle' : 'alert-circle'
                    }" class="w-5 h-5 mr-2"></i>
                    ${message}
                </div>
            </div>
        `);
        
        // Append to body
        $('body').append(toast);
        lucide.createIcons();
        
        // Animate in
        toast.hide().css({opacity: 0, x: 100});
        toast.show().animate({opacity: 1, x: 0}, 300);
        
        // Auto-remove after delay
        setTimeout(function() {
            toast.animate({opacity: 0, y: -20}, 300, function() {
                $(this).remove();
            });
        }, 5000);
    }

    // Promote member to moderator
    function promoteMember(memberId) {
        Swal.fire({
            title: 'Promote to Moderator?',
            text: "This member will be granted moderator privileges for this forum.",
            html: `
                <div class="text-left mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes (optional)</label>
                    <textarea id="promote-notes" class="swal2-input w-full p-2 border border-gray-300 rounded-md" 
                              placeholder="Enter any notes about this promotion..." 
                              rows="3"></textarea>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#8b5cf6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, promote!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            focusConfirm: false,
            preConfirm: () => {
                return {
                    notes: document.getElementById('promote-notes').value
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const notes = result.value.notes || '';
                
                $.ajax({
                    url: '<?= site_url('auth/forums/members/promote/') ?>' + memberId,
                    type: 'POST',
                    data: {
                        notes: notes,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Promoted!',
                            text: 'Member has been promoted to moderator successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        membersTable.ajax.reload();
                        moderatorsTable.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Failed to promote member. Please try again.'
                        });
                    }
                });
            }
        });
    }

    // Remove member from forum
    function removeMember(memberId) {
        Swal.fire({
            title: 'Remove Member?',
            text: "This member will be removed from the forum and lose access to all discussions.",
            html: `
                <div class="text-left mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for removal (optional)</label>
                    <textarea id="remove-notes" class="swal2-input w-full p-2 border border-gray-300 rounded-md" 
                              placeholder="Enter reason for removing this member..." 
                              rows="3"></textarea>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, remove!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            focusConfirm: false,
            preConfirm: () => {
                return {
                    notes: document.getElementById('remove-notes').value
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const notes = result.value.notes || '';
                
                $.ajax({
                    url: '<?= site_url('auth/forums/members/remove/') ?>' + memberId,
                    type: 'POST',
                    data: {
                        notes: notes,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Removed!',
                            text: 'Member has been removed from the forum successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        membersTable.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Failed to remove member. Please try again.'
                        });
                    }
                });
            }
        });
    }

    // Block member from forum
    function blockMember(memberId) {
        Swal.fire({
            title: 'Block Member?',
            text: 'This member will not be able to post or comment in this forum.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#eab308',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, block them',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('auth/forums/members/block/') ?>' + memberId,
                    type: 'POST',
                    data: {
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Blocked!',
                            text: 'Member has been blocked successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        membersTable.ajax.reload(null, false);
                        lucide.createIcons();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Failed to block member. Please try again.'
                        });
                    }
                });
            }
        });
    }

    // Unblock member from forum
    function unblockMember(memberId) {
        Swal.fire({
            title: 'Unblock Member?',
            text: 'This member will be able to post and comment again.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#22c55e',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, unblock them',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('auth/forums/members/unblock/') ?>' + memberId,
                    type: 'POST',
                    data: {
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Unblocked!',
                            text: 'Member has been unblocked successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        membersTable.ajax.reload(null, false);
                        lucide.createIcons();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Failed to unblock member. Please try again.'
                        });
                    }
                });
            }
        });
    }

    // Initialize Lucide icons when content is loaded or updated
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
        </div>
    </main>
<?= $this->endSection() ?>