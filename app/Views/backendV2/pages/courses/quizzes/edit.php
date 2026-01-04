<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<main class="flex-1 overflow-y-auto">
    <!-- Page Banner -->
    <?= view('backendV2/partials/page_banner', [
        'pageTitle' => 'Edit Quiz',
        'pageDescription' => 'Manage quiz details and questions',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => base_url('auth/dashboard')],
            ['label' => 'Courses', 'url' => base_url('auth/courses')],
            ['label' => 'Quizzes', 'url' => base_url('auth/courses/quizzes')],
            ['label' => 'Edit Quiz', 'url' => '']
        ],
        'bannerActions' => '<a href="' . site_url('auth/courses/quizzes') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Quizzes
        </a>'
    ]) ?>

    <div class="container-fluid px-4 py-6">
        <div class="max-w-full mx-auto">
            <!-- Quiz Details Card -->
            <div class="bg-white rounded-xl shadow-sm border border-borderColor p-8 mb-6">
                <h3 class="text-lg font-semibold text-dark mb-4">Quiz Details</h3>
                <form id="updateQuizForm">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Quiz Title -->
                        <div class="md:col-span-2">
                            <label for="quiz_title" class="block text-sm font-medium text-dark mb-2">
                                Quiz Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                id="quiz_title" 
                                name="title" 
                                value="<?= esc($quiz['title']) ?>"
                                class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary" 
                                required>
                        </div>

                        <!-- Quiz Description -->
                        <div class="md:col-span-2">
                            <label for="quiz_description" class="block text-sm font-medium text-dark mb-2">
                                Description
                            </label>
                            <textarea id="quiz_description" 
                                    name="description" 
                                    rows="3"
                                    class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary"><?= esc($quiz['description'] ?? '') ?></textarea>
                        </div>

                        <!-- Passing Score -->
                        <div>
                            <label for="passing_score" class="block text-sm font-medium text-dark mb-2">
                                Passing Score (%)
                            </label>
                            <input type="number" 
                                id="passing_score" 
                                name="passing_score" 
                                value="<?= esc($quiz['passing_score'] ?? 70) ?>"
                                min="0" 
                                max="100"
                                class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary">
                            <p class="text-xs text-gray-500 mt-1">Minimum score required to pass (0-100)</p>
                        </div>

                        <!-- Max Attempts -->
                        <div>
                            <label for="max_attempts" class="block text-sm font-medium text-dark mb-2">
                                Maximum Attempts
                            </label>
                            <input type="number" 
                                id="max_attempts" 
                                name="max_attempts" 
                                value="<?= esc($quiz['max_attempts'] ?? '') ?>"
                                min="1"
                                class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary"
                                placeholder="Unlimited">
                            <p class="text-xs text-gray-500 mt-1">Leave empty for unlimited attempts</p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="quiz_status" class="block text-sm font-medium text-dark mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select id="quiz_status" 
                                    name="status" 
                                    class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary"
                                    required>
                                <option value="active" <?= ($quiz['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= ($quiz['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" 
                                class="gradient-btn px-8 py-3 rounded-[50px] text-white font-medium hover:shadow-lg transition-all flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Update Quiz Details
                        </button>
                    </div>
                </form>
            </div>

            <!-- Questions Card -->
            <div class="bg-white rounded-xl shadow-sm border border-borderColor p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-dark">Quiz Questions</h3>
                    <button type="button" 
                            onclick="showAddQuestionModal()"
                            class="gradient-btn flex items-center px-6 py-3 rounded-full text-white hover:shadow-lg transition-all duration-300">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Add Question
                    </button>
                </div>

                <div id="questionsList" class="space-y-4">
                    <!-- Questions will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Add Question Modal -->
<div id="addQuestionModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 p-4" style="overflow-y: auto;">
    <div class="flex items-center justify-center min-h-full py-6">
        <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full my-auto">
        <div class="p-6 border-b border-borderColor">
            <h3 class="text-xl font-semibold text-dark">Add New Question</h3>
        </div>
        
        <form id="addQuestionForm" class="p-6">
            <input type="hidden" name="quiz_id" value="<?= esc($quiz['id']) ?>">
            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
            
            <!-- Question Text -->
            <div class="mb-6">
                <label for="question_text" class="block text-sm font-medium text-dark mb-2">
                    Question <span class="text-red-500">*</span>
                </label>
                <textarea id="question_text" 
                          name="question_text" 
                          rows="3"
                          class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary"
                          placeholder="Enter your question here..."
                          required></textarea>
            </div>

            <!-- Question Type -->
            <div class="mb-6">
                <label for="question_type" class="block text-sm font-medium text-dark mb-2">
                    Question Type <span class="text-red-500">*</span>
                </label>
                <select id="question_type" 
                        name="question_type" 
                        class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary"
                        required>
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="true_false">True/False</option>
                </select>
            </div>

            <!-- Points -->
            <div class="mb-6">
                <label for="points" class="block text-sm font-medium text-dark mb-2">
                    Points <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       id="points" 
                       name="points" 
                       value="1"
                       min="1"
                       class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary"
                       required>
            </div>

            <!-- Answer Options -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-dark mb-3">
                    Answer Options <span class="text-red-500">*</span>
                </label>
                <div id="optionsList" class="space-y-3">
                    <!-- Options will be added here -->
                </div>
                <button type="button" 
                        onclick="addOptionField()"
                        class="mt-3 text-secondary hover:text-secondary/80 text-sm font-medium">
                    <i data-lucide="plus-circle" class="w-4 h-4 inline mr-1"></i>
                    Add Option
                </button>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-borderColor">
                <button type="button" 
                        onclick="closeAddQuestionModal()"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="gradient-btn px-8 py-3 rounded-[50px] text-white font-medium hover:shadow-lg transition-all flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Add Question
                </button>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Edit Question Modal -->
<div id="editQuestionModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 p-4" style="overflow-y: auto;">
    <div class="flex items-center justify-center min-h-full py-6">
        <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full my-auto">
        <div class="p-6 border-b border-borderColor">
            <h3 class="text-xl font-semibold text-dark">Edit Question</h3>
        </div>
        
        <form id="editQuestionForm" class="p-6">
            <input type="hidden" name="question_id" id="edit_question_id">
            <input type="hidden" name="quiz_id" value="<?= esc($quiz['id']) ?>">
            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
            
            <!-- Question Text -->
            <div class="mb-6">
                <label for="edit_question_text" class="block text-sm font-medium text-dark mb-2">
                    Question <span class="text-red-500">*</span>
                </label>
                <textarea id="edit_question_text" 
                          name="question_text" 
                          rows="3"
                          class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary"
                          placeholder="Enter your question here..."
                          required></textarea>
            </div>

            <!-- Question Type -->
            <div class="mb-6">
                <label for="edit_question_type" class="block text-sm font-medium text-dark mb-2">
                    Question Type <span class="text-red-500">*</span>
                </label>
                <select id="edit_question_type" 
                        name="question_type" 
                        class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary"
                        required>
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="true_false">True/False</option>
                </select>
            </div>

            <!-- Points -->
            <div class="mb-6">
                <label for="edit_points" class="block text-sm font-medium text-dark mb-2">
                    Points <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       id="edit_points" 
                       name="points" 
                       value="1"
                       min="1"
                       class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary"
                       required>
            </div>

            <!-- Answer Options -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-dark mb-3">
                    Answer Options <span class="text-red-500">*</span>
                </label>
                <div id="editOptionsList" class="space-y-3">
                    <!-- Options will be loaded here -->
                </div>
                <button type="button" 
                        onclick="addEditOptionField()"
                        class="mt-3 text-secondary hover:text-secondary/80 text-sm font-medium">
                    <i data-lucide="plus-circle" class="w-4 h-4 inline mr-1"></i>
                    Add Option
                </button>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-borderColor">
                <button type="button" 
                        onclick="closeEditQuestionModal()"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="gradient-btn px-8 py-3 rounded-[50px] text-white font-medium hover:shadow-lg transition-all flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Update Question
                </button>
            </div>
        </form>
        </div>
    </div>
</div>

<script>
const quizId = '<?= esc($quiz['id']) ?>';
let optionCounter = 0;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadQuestions();
    addOptionField();
    addOptionField();
    
    lucide.createIcons();
});

// Update Quiz Form
document.getElementById('updateQuizForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch(`<?= site_url('auth/courses/quizzes/edit/') ?>${quizId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: result.message,
                confirmButtonColor: '#10b981'
            });
        } else {
            throw new Error(result.message || 'Failed to update quiz');
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message,
            confirmButtonColor: '#ef4444'
        });
    }
});

// Load Questions
async function loadQuestions() {
    try {
        const response = await fetch(`<?= site_url('auth/courses/quizzes/') ?>${quizId}/questions`);
        const result = await response.json();
        
        if (result.success) {
            displayQuestions(result.data);
        }
    } catch (error) {
        console.error('Error loading questions:', error);
    }
}

// Display Questions
function displayQuestions(questions) {
    const container = document.getElementById('questionsList');
    
    if (!questions || questions.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12 text-gray-500">
                <i data-lucide="help-circle" class="w-12 h-12 mx-auto mb-3 opacity-50"></i>
                <p>No questions added yet. Click "Add Question" to get started.</p>
            </div>
        `;
        lucide.createIcons();
        return;
    }
    
    container.innerHTML = questions.map((question, index) => `
        <div class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <span class="bg-secondary/10 text-secondary px-3 py-1 rounded-full text-xs font-medium mr-3">
                            Question ${index + 1}
                        </span>
                        <span class="text-xs text-gray-500">${question.points} point${question.points > 1 ? 's' : ''}</span>
                    </div>
                    <p class="text-dark font-medium mb-3">${question.question_text}</p>
                    
                    ${question.options && question.options.length > 0 ? `
                        <div class="space-y-2 ml-4">
                            ${question.options.map(option => `
                                <div class="flex items-center">
                                    <span class="${option.is_correct == 1 ? 'text-green-600' : 'text-gray-600'}">
                                        ${option.is_correct == 1 ? '✓' : '○'}
                                    </span>
                                    <span class="ml-2 text-sm ${option.is_correct == 1 ? 'font-medium text-green-700' : 'text-gray-600'}">
                                        ${option.option_text}
                                    </span>
                                </div>
                            `).join('')}
                        </div>
                    ` : '<div class="ml-4 text-sm text-gray-500 italic">No answer options</div>'}
                </div>
                
                <div class="flex gap-2">
                    <button type="button" 
                            onclick="editQuestion('${question.id}')"
                            class="text-blue-500 hover:text-blue-700 p-2 rounded-lg hover:bg-blue-50 transition-colors"
                            title="Edit Question">
                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                    </button>
                    <button type="button" 
                            onclick="deleteQuestion('${question.id}')"
                            class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors"
                            title="Delete Question">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('');
    
    lucide.createIcons();
}

// Show Add Question Modal
function showAddQuestionModal() {
    const modal = document.getElementById('addQuestionModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// Close Add Question Modal
function closeAddQuestionModal() {
    const modal = document.getElementById('addQuestionModal');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
    document.getElementById('addQuestionForm').reset();
    // Reset options
    document.getElementById('optionsList').innerHTML = '';
    optionCounter = 0;
    addOptionField();
    addOptionField();
}

// Add Option Field
function addOptionField() {
    const container = document.getElementById('optionsList');
    const optionHtml = `
        <div class="flex items-center space-x-3 option-row">
            <input type="text" 
                   name="options[]" 
                   class="flex-1 px-4 py-2 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary"
                   placeholder="Option ${optionCounter + 1}"
                   required>
            <label class="flex items-center cursor-pointer">
                <input type="radio" 
                       name="correct_option" 
                       value="${optionCounter}"
                       class="w-4 h-4 text-green-600 focus:ring-green-500"
                       ${optionCounter === 0 ? 'checked' : ''}
                       required>
                <span class="ml-2 text-sm text-gray-600">Correct</span>
            </label>
            ${optionCounter > 1 ? `
                <button type="button" 
                        onclick="this.closest('.option-row').remove()"
                        class="text-red-500 hover:text-red-700 p-2">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            ` : '<div class="w-10"></div>'}
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', optionHtml);
    optionCounter++;
    lucide.createIcons();
}

// Add Question Form Submit
document.getElementById('addQuestionForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('<?= site_url('auth/courses/quizzes/questions/create') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: result.message,
                confirmButtonColor: '#10b981'
            });
            
            closeAddQuestionModal();
            loadQuestions();
        } else {
            throw new Error(result.message || 'Failed to add question');
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message,
            confirmButtonColor: '#ef4444'
        });
    }
});

// Delete Question
async function deleteQuestion(questionId) {
    const result = await Swal.fire({
        title: 'Delete Question?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it'
    });
    
    if (result.isConfirmed) {
        try {
            const response = await fetch(`<?= site_url('auth/courses/quizzes/questions/delete/') ?>${questionId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted',
                    text: result.message,
                    confirmButtonColor: '#10b981'
                });
                
                loadQuestions();
            } else {
                throw new Error(result.message || 'Failed to delete question');
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message,
                confirmButtonColor: '#ef4444'
            });
        }
    }
}

// Edit Question Functions
let editOptionCounter = 0;
let currentQuestionData = null;

function editQuestion(questionId) {
    // Find the question data
    const response = fetch(`<?= site_url('auth/courses/quizzes/') ?>${quizId}/questions`)
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                currentQuestionData = result.data.find(q => q.id === questionId);
                if (currentQuestionData) {
                    showEditQuestionModal(currentQuestionData);
                }
            }
        });
}

function showEditQuestionModal(question) {
    // Populate form fields
    document.getElementById('edit_question_id').value = question.id;
    document.getElementById('edit_question_text').value = question.question_text;
    document.getElementById('edit_question_type').value = question.question_type;
    document.getElementById('edit_points').value = question.points;
    
    // Clear and populate options
    const container = document.getElementById('editOptionsList');
    container.innerHTML = '';
    editOptionCounter = 0;
    
    if (question.options && question.options.length > 0) {
        question.options.forEach((option, index) => {
            addEditOptionField(option.option_text, option.is_correct == 1);
        });
    } else {
        addEditOptionField();
        addEditOptionField();
    }
    
    // Show modal
    const modal = document.getElementById('editQuestionModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    lucide.createIcons();
}

function closeEditQuestionModal() {
    const modal = document.getElementById('editQuestionModal');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
    document.getElementById('editQuestionForm').reset();
    currentQuestionData = null;
}

function addEditOptionField(optionText = '', isCorrect = false) {
    const container = document.getElementById('editOptionsList');
    const optionHtml = `
        <div class="flex items-center space-x-3 option-row">
            <input type="text" 
                   name="options[]" 
                   value="${optionText}"
                   class="flex-1 px-4 py-2 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary"
                   placeholder="Option ${editOptionCounter + 1}"
                   required>
            <label class="flex items-center cursor-pointer">
                <input type="radio" 
                       name="correct_option" 
                       value="${editOptionCounter}"
                       class="w-4 h-4 text-green-600 focus:ring-green-500"
                       ${isCorrect || editOptionCounter === 0 ? 'checked' : ''}
                       required>
                <span class="ml-2 text-sm text-gray-600">Correct</span>
            </label>
            ${editOptionCounter > 1 ? `
                <button type="button" 
                        onclick="this.closest('.option-row').remove()"
                        class="text-red-500 hover:text-red-700 p-2">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            ` : '<div class="w-10"></div>'}
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', optionHtml);
    editOptionCounter++;
    lucide.createIcons();
}

// Edit Question Form Submit
document.getElementById('editQuestionForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const questionId = formData.get('question_id');
    
    try {
        const response = await fetch(`<?= site_url('auth/courses/quizzes/questions/update/') ?>${questionId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: result.message,
                confirmButtonColor: '#10b981'
            });
            
            closeEditQuestionModal();
            loadQuestions();
        } else {
            throw new Error(result.message || 'Failed to update question');
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message,
            confirmButtonColor: '#ef4444'
        });
    }
});
</script>

<?= $this->endSection(); ?>
