<!-- Course Details -->
<div class="border-b pb-6">
    <h3 class="text-lg font-medium text-dark mb-4">Course Details</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-dark mb-1">Category <span class="text-red-500">*</span></label>
            <select name="category" id="category" class="select2 w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary">
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $course['category_id'] ? 'selected' : '' ?>><?= esc($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-dark mb-1">Level <span class="text-red-500">*</span></label>
            <select name="level" id="level" class="select2 w-full px-4 py-3 border border-borderColor rounded-lg">
                <option value="">Select Level</option>
                <option value="beginner" <?= strtolower(trim($course['level'])) == 'beginner' ? 'selected' : '' ?>>Beginner</option>
                <option value="intermediate" <?= strtolower(trim($course['level'])) == 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
                <option value="advanced" <?= strtolower(trim($course['level'])) == 'advanced' ? 'selected' : '' ?>>Advanced</option>
            </select>
        </div>

        <div>
            <label for="duration" class="block text-sm font-medium text-dark mb-1">Duration (hours)</label>
            <input type="number" id="duration" name="duration" value="<?= $course['duration'] ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg">
        </div>
    </div>
</div>
