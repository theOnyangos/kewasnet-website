<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto">
        <!-- Page Banner -->
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Manage Quizzes',
            'pageDescription' => 'Create and manage course quizzes',
            'breadcrumbs' => [
                ['label' => 'Courses', 'url' => base_url('auth/courses')],
                ['label' => 'Quizzes']
            ],
            'bannerActions' => '<div class="flex items-center gap-3">
                <a href="' . site_url('auth/courses/create') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                    Create Course
                </a>
                <button type="button" onclick="window.open(\'' . site_url('ksp/learning-hub') . '\', \'_blank\')" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                    <i data-lucide="external-link" class="w-4 h-4 mr-2"></i>
                    View Frontend
                </button>
            </div>'
        ]) ?>

        <div class="px-6 pb-6">
        <!-- Quick Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 stats-container">
            <!-- Total Quizzes Card -->
            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Total Quizzes</p>
                        <h3 class="text-2xl font-bold mt-1" id="totalQuizzes">0</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="file-question" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="clipboard-list" class="w-4 h-4 mr-1"></i> All quiz items
                </p>
            </div>

            <!-- Active Quizzes Card -->
            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Active Quizzes</p>
                        <h3 class="text-2xl font-bold mt-1" id="activeQuizzes">0</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="play" class="w-4 h-4 mr-1"></i> Currently active
                </p>
            </div>

            <!-- Total Questions Card -->
            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Total Questions</p>
                        <h3 class="text-2xl font-bold mt-1" id="totalQuestions">0</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="help-circle" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="list" class="w-4 h-4 mr-1"></i> Across all quizzes
                </p>
            </div>

            <!-- Average Questions Card -->
            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Avg Questions</p>
                        <h3 class="text-2xl font-bold mt-1" id="avgQuestions">0</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="bar-chart-2" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> Per quiz
                </p>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <?php
            $uri = service('uri');
            $segments = $uri->getSegments();
            $activeClass = "border-b-2 border-cyan-500 text-sm text-cyan-600";
            $inactiveClass = "border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300";

            // Determine active tab based on URI segment
            $activeTab = '';
            if (isset($segments[2])) {
                $activeTab = $segments[2];
            }

            // Set default tab
            if (empty($activeTab) || $activeTab === 'index') {
                $activeTab = 'overview';
            }
        ?>
        <div class="bg-white rounded-t-xl shadow-sm pb-6 pt-10">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <a href="<?= base_url('auth/courses') ?>"
                        class="py-4 px-1 <?= ($activeTab === 'overview' || $activeTab === 'index') ? $activeClass : $inactiveClass ?>">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 mr-2 inline"></i>
                        Overview
                    </a>
                    <a href="<?= base_url('auth/courses/courses') ?>"
                        class="py-4 px-1 <?= $activeTab === 'courses' ? $activeClass : $inactiveClass ?>">
                        <i data-lucide="book-open" class="w-4 h-4 mr-2 inline"></i>
                        All Courses
                    </a>
                    <a href="<?= base_url('auth/courses/sections') ?>"
                        class="py-4 px-1 <?= $activeTab === 'sections' ? $activeClass : $inactiveClass ?>">
                        <i data-lucide="layers" class="w-4 h-4 mr-2 inline"></i>
                        Sections
                    </a>
                    <a href="<?= base_url('auth/courses/lectures') ?>"
                        class="py-4 px-1 <?= $activeTab === 'lectures' ? $activeClass : $inactiveClass ?>">
                        <i data-lucide="play-circle" class="w-4 h-4 mr-2 inline"></i>
                        Lectures
                    </a>
                    <a href="<?= base_url('auth/courses/quizzes') ?>"
                        class="py-4 px-1 <?= $activeTab === 'quizzes' ? $activeClass : $inactiveClass ?>">
                        <i data-lucide="clipboard-list" class="w-4 h-4 mr-2 inline"></i>
                        Quizzes
                    </a>
                    <a href="<?= base_url('auth/courses/enrollments') ?>"
                        class="py-4 px-1 <?= $activeTab === 'enrollments' ? $activeClass : $inactiveClass ?>">
                        <i data-lucide="users" class="w-4 h-4 mr-2 inline"></i>
                        Enrollments
                    </a>
                </nav>
            </div>
        </div>

        <!-- Quizzes Table -->
        <div class="bg-white rounded-b-xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">All Quizzes</h1>
                    <p class="mt-1 text-sm text-slate-500">Manage and view all course quizzes</p>
                </div>
                <div class="flex space-x-3 mt-4 md:mt-0">
                    <a href="<?= site_url('auth/courses/quizzes/create') ?>" class="gradient-btn flex items-center px-8 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
                        <i data-lucide="plus" class="w-4 h-4 mr-2 z-10"></i>
                        <span>Create Quiz</span>
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="quizzesTable" class="data-table stripe hover" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th>Quiz Title</th>
                            <th>Questions</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </main>

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
        const avgQuestions = total > 0 ? Math.round(totalQuestions / total) : 0;
        
        $('#totalQuizzes').text(total);
        $('#activeQuizzes').text(active);
        $('#totalQuestions').text(totalQuestions);
        $('#avgQuestions').text(avgQuestions);
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
