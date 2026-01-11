<!-- Learning Goals -->
<div class="border-b pb-6">
    <h3 class="text-lg font-medium text-dark mb-4">Learning Goals</h3>
    <div>
        <label class="block text-sm font-medium text-dark mb-1">What will students learn?</label>
        <p class="mb-4 text-xs text-gray-500">Add key learning outcomes that students will achieve by taking this course. Click "Add Goal" to add more items.</p>
        
        <div id="goalsContainer" class="space-y-3 mb-4">
            <?php
            // Parse existing goals - could be array, newline-separated, or single string
            $existingGoals = [];
            if (!empty($course['goals'])) {
                if (is_array($course['goals'])) {
                    $existingGoals = $course['goals'];
                } elseif (is_string($course['goals'])) {
                    // Try to decode as JSON first
                    $decoded = json_decode($course['goals'], true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $existingGoals = $decoded;
                    } else {
                        // Split by newlines if not JSON
                        $existingGoals = array_filter(array_map('trim', explode("\n", $course['goals'])));
                    }
                }
            }
            
            // If no goals exist, show one empty field
            if (empty($existingGoals)) {
                $existingGoals = [''];
            }
            
            foreach ($existingGoals as $index => $goal):
            ?>
            <div class="goal-item flex gap-2 items-start" data-index="<?= $index ?>">
                <div class="flex-1">
                    <input type="text" name="goals[]" value="<?= esc($goal) ?>" placeholder="Enter a learning goal (e.g., Understand the fundamentals of X)" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                </div>
                <button type="button" class="remove-goal-btn px-4 py-3 bg-red-50 border border-red-300 rounded-lg text-red-600 hover:bg-red-100 transition-colors flex-shrink-0" style="<?= count($existingGoals) > 1 ? '' : 'display: none;' ?>" title="Remove this goal">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <?php endforeach; ?>
        </div>
        
        <button type="button" id="addGoalBtn" class="px-4 py-2 bg-secondary/10 border border-secondary rounded-lg text-secondary hover:bg-secondary/20 transition-colors text-sm font-medium inline-flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Add Goal
        </button>
    </div>
</div>
