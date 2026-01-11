<!-- Media -->
<div class="border-b pb-6">
    <h3 class="text-lg font-medium text-dark mb-4">Course Media</h3>

    <?php if (!empty($course['image_url'])): ?>
    <div class="mb-4">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-dark mb-2">Current Image</label>
                <img src="<?= base_url($course['image_url']) ?>" alt="Course Image" class="max-w-xs rounded-lg border" id="currentImage">
            </div>
            <button type="button" id="removeImageBtn" class="mt-7 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors flex items-center gap-2">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                <span>Remove Image</span>
            </button>
        </div>
        <input type="hidden" name="remove_image" id="removeImageFlag" value="0">
    </div>
    <?php endif; ?>

    <!-- Update Course Image -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-dark mb-1">Update Course Image</label>
        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-borderColor border-dashed rounded-md">
            <div class="space-y-1 text-center">
                <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="flex text-sm text-slate-600">
                    <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-secondary hover:text-secondaryShades-600">
                        <span>Upload course image</span>
                        <input id="image" name="image" type="file" accept="image/*" class="sr-only">
                    </label>
                    <p class="pl-1">or drag and drop</p>
                </div>
                <p class="text-xs text-slate-500">PNG, JPG up to 2MB</p>
                <div id="image-preview" class="mt-4"></div>
            </div>
        </div>
    </div>

    <div>
        <label for="preview_video_url" class="block text-sm font-medium text-dark mb-1">Preview Video URL</label>
        <input type="url" id="preview_video_url" name="preview_video_url" value="<?= $course['preview_video_url'] ?? '' ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg">
    </div>
</div>
