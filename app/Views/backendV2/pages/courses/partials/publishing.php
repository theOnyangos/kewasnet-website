<!-- Publishing -->
<div class="border-b pb-6">
    <h3 class="text-lg font-medium text-dark mb-4">Publishing Status</h3>
    <div class="flex flex-col space-y-3">
        <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
            <input type="radio" name="status" value="0" <?= $course['status'] == 0 || $course['status'] == 'draft' ? 'checked' : '' ?> class="h-4 w-4 text-secondary focus:ring-secondary">
            <div class="ml-3">
                <span class="text-sm font-medium text-dark">Draft</span>
                <p class="text-xs text-gray-500">Save for later editing</p>
            </div>
        </label>
        <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
            <input type="radio" name="status" value="1" <?= $course['status'] == 1 || $course['status'] == 'published' ? 'checked' : '' ?> class="h-4 w-4 text-secondary focus:ring-secondary">
            <div class="ml-3">
                <span class="text-sm font-medium text-dark">Published</span>
                <p class="text-xs text-gray-500">Make it live for students to enroll</p>
            </div>
        </label>
        <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
            <input type="radio" name="status" value="2" <?= $course['status'] == 2 || $course['status'] == 'archived' ? 'checked' : '' ?> class="h-4 w-4 text-secondary focus:ring-secondary">
            <div class="ml-3">
                <span class="text-sm font-medium text-dark">Archived</span>
                <p class="text-xs text-gray-500">Hide from course listings</p>
            </div>
        </label>
    </div>
</div>
