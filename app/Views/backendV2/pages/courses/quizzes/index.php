<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>

<!-- Page Banner -->
<?= view('backendV2/partials/page_banner', [
    'pageTitle' => 'Manage Quizzes',
    'pageDescription' => 'Create and manage course quizzes',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => base_url('auth/dashboard')],
        ['label' => 'Courses', 'url' => base_url('auth/courses')],
        ['label' => 'Quizzes', 'url' => '']
    ],
    'bannerActions' => '<a href="' . site_url('auth/courses/quizzes/create') . '" class="inline-flex items-center px-4 py-2 bg-white text-primary hover:bg-white/90 rounded-lg font-medium transition-colors">
        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
        Create Quiz
    </a>'
]) ?>

<div class="container-fluid px-4 py-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-borderColor p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Quizzes</p>
                    <p class="text-2xl font-bold text-dark mt-1" id="totalQuizzes">0</p>
                </div>
                <div class="p-3 bg-primary/10 rounded-lg">
                    <i data-lucide="file-question" class="w-6 h-6 text-primary"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-borderColor p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Quizzes</p>
                    <p class="text-2xl font-bold text-dark mt-1" id="activeQuizzes">0</p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-borderColor p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Questions</p>
                    <p class="text-2xl font-bold text-dark mt-1" id="totalQuestions">0</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i data-lucide="help-circle" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quizzes Table -->
    <div class="bg-white rounded-xl shadow-sm border border-borderColor">
        <div class="p-6 border-b border-borderColor">
            <h3 class="text-lg font-semibold text-dark">All Quizzes</h3>
        </div>
        <div class="p-6">
            <table id="quizzesTable" class="data-table stripe hover" style="width:100%">
                <thead>
                    <tr class="border-b border-borderColor">
                        <th>Quiz Title</th>
                        <th>Questions</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-center py-3 px-4 font-semibold text-dark">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    lucide.createIcons();
    
    // Initialize DataTable
    const table = $('#quizzesTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '<?= site_url("auth/courses/quizzes/list") ?>',
            type: 'GET',
            dataSrc: function(json) {
                if (json.success) {
                    updateStats(json.data);
                    return json.data;
                }
                return [];
            },
            error: function(xhr, error, thrown) {
                console.error('Error loading quizzes:', error);
            }
        },
        columns: [
            { 
                data: 'title',
                render: function(data, type, row) {
                    return `<div class="font-medium text-dark">${data}</div>
                            ${row.description ? `<div class="text-xs text-gray-500 mt-1">${row.description.substring(0, 60)}...</div>` : ''}`;
                }
            },
            { 
                data: 'question_count',
                render: function(data, type, row) {
                    const count = data || 0;
                    const label = count === 1 ? 'question' : 'questions';
                    return `<span class="text-sm text-gray-600">${count} ${label}</span>`;
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    if (data === 'active') {
                        return '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700"><i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>Active</span>';
                    }
                    return '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700"><i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>Inactive</span>';
                }
            },
            { 
                data: 'created_at',
                render: function(data) {
                    return data ? new Date(data).toLocaleDateString() : '-';
                }
            },
            {
                data: 'id',
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="editQuiz('${data}')" class="text-blue-600 hover:text-blue-800" title="Edit">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </button>
                            <button onclick="deleteQuiz('${data}')" class="text-red-600 hover:text-red-800" title="Delete">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        drawCallback: function() {
            lucide.createIcons();
        },
        language: {
            emptyTable: "No quizzes found. Create your first quiz!",
            processing: "Loading quizzes..."
        }
    });

    function updateStats(data) {
        const total = data.length;
        const active = data.filter(q => q.status === 'active').length;
        const totalQuestions = data.reduce((sum, quiz) => sum + (parseInt(quiz.question_count) || 0), 0);
        
        $('#totalQuizzes').text(total);
        $('#activeQuizzes').text(active);
        $('#totalQuestions').text(totalQuestions);
    }

    window.editQuiz = function(quizId) {
        window.location.href = '<?= site_url("auth/courses/quizzes/edit") ?>/' + quizId;
    };

    window.deleteQuiz = function(quizId) {
        Swal.fire({
            title: 'Delete Quiz?',
            text: 'This will permanently delete the quiz and all its questions. This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // TODO: Implement delete functionality
                Swal.fire('Info', 'Delete functionality to be implemented', 'info');
            }
        });
    };
});
</script>
<?= $this->endSection() ?>
