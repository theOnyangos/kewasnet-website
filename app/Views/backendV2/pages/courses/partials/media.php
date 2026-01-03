<!-- Media -->
<div class="border-b pb-6">
    <h3 class="text-lg font-medium text-dark mb-4">Course Media</h3>

    <?php if (!empty($course['image_url'])): ?>
    <div class="mb-4">
        <label class="block text-sm font-medium text-dark mb-2">Current Image</label>
        <img src="<?= base_url($course['image_url']) ?>" alt="Course Image" class="max-w-xs rounded-lg border">
    </div>
    <?php endif; ?>

    <div class="mb-4">
        <label class="block text-sm font-medium text-dark mb-1">Update Course Image</label>
        <input type="file" id="image" name="image" accept="image/*" class="w-full px-4 py-3 border border-borderColor rounded-lg">
    </div>

    <div>
        <label for="preview_video_url" class="block text-sm font-medium text-dark mb-1">Preview Video URL</label>
        <input type="url" id="preview_video_url" name="preview_video_url" value="<?= $course['preview_video_url'] ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg">
    </div>
</div>
