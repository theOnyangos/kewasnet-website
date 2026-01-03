<!-- Basic Information -->
<div class="border-b pb-6">
    <h3 class="text-lg font-medium text-dark mb-4">Basic Information</h3>

    <div class="mb-4">
        <label for="title" class="block text-sm font-medium text-dark mb-1">Course Title <span class="text-red-500">*</span></label>
        <input type="text" id="title" name="title" value="<?= esc($course['title']) ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Enter course title" required>
        <p class="mt-1 text-xs text-gray-500">A clear and descriptive title for your course</p>
    </div>

    <div class="mb-4">
        <label for="slug" class="block text-sm font-medium text-dark mb-1">URL Slug <span class="text-red-500">*</span></label>
        <div class="flex w-full">
            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-borderColor bg-lightGray text-dark text-sm whitespace-nowrap">
                kewasnet.co.ke/learning-hub/courses/
            </span>
            <input type="text" id="slug" name="slug" value="<?= esc($course['slug']) ?>" class="flex-1 min-w-0 px-4 py-3 rounded-none rounded-r-md border border-borderColor focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="url-friendly-slug">
        </div>
        <p class="mt-1 text-xs text-gray-500">Auto-generated from title. Edit if needed.</p>
    </div>

    <div class="mb-4">
        <label for="summary" class="block text-sm font-medium text-dark mb-1">Summary <span class="text-red-500">*</span></label>
        <textarea id="summary" name="summary" rows="3" maxlength="500" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary resize-none" required><?= esc($course['summary']) ?></textarea>
        <div class="flex justify-between text-xs text-gray-500">
            <span>A compelling summary for course cards</span>
            <span id="summary-count">0/500</span>
        </div>
    </div>
</div>
