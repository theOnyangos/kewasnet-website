<!-- Options -->
<div class="border-b pb-6">
    <h3 class="text-lg font-medium text-dark mb-4">Options</h3>

    <div class="mb-4">
        <input type="checkbox" id="certificate" name="certificate" value="1" <?= $course['certificate'] ? 'checked' : '' ?> class="h-4 w-4 text-secondary">
        <label for="certificate" class="ml-2 text-sm text-dark">Offer Certificate</label>
    </div>

    <div>
        <label class="block text-sm font-medium text-dark mb-1">Resources/Requirements</label>
        <p class="mb-4 text-xs text-gray-500">Add any required materials, software, tools, or prerequisites students need before taking this course. Click "Add Resource" to add more items.</p>
        
        <div id="resourcesContainer" class="space-y-3 mb-4">
            <?php
            // Parse existing resources - could be array, newline-separated, or single string
            $existingResources = [];
            if (!empty($course['resources'])) {
                if (is_array($course['resources'])) {
                    $existingResources = $course['resources'];
                } elseif (is_string($course['resources'])) {
                    // Try to decode as JSON first
                    $decoded = json_decode($course['resources'], true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $existingResources = $decoded;
                    } else {
                        // Split by newlines or commas if not JSON
                        $existingResources = array_filter(array_map('trim', preg_split('/[\n,]+/', $course['resources'])));
                    }
                }
            }
            
            // If no resources exist, show one empty field
            if (empty($existingResources)) {
                $existingResources = [''];
            }
            
            foreach ($existingResources as $index => $resource):
            ?>
            <div class="resource-item flex gap-2 items-start" data-index="<?= $index ?>">
                <div class="flex-1">
                    <input type="text" name="resources[]" value="<?= esc($resource) ?>" placeholder="Enter a resource or requirement (e.g., Internet connection, Laptop with X software)" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                </div>
                <button type="button" class="remove-resource-btn px-4 py-3 bg-red-50 border border-red-300 rounded-lg text-red-600 hover:bg-red-100 transition-colors flex-shrink-0" style="<?= count($existingResources) > 1 ? '' : 'display: none;' ?>" title="Remove this resource">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <?php endforeach; ?>
        </div>
        
        <button type="button" id="addResourceBtn" class="px-4 py-2 bg-secondary/10 border border-secondary rounded-lg text-secondary hover:bg-secondary/20 transition-colors text-sm font-medium inline-flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Add Resource
        </button>
    </div>
</div>
