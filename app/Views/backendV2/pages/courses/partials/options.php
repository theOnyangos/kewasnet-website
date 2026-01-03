<!-- Options -->
<div class="border-b pb-6">
    <h3 class="text-lg font-medium text-dark mb-4">Options</h3>

    <div class="mb-4">
        <input type="checkbox" id="certificate" name="certificate" value="1" <?= $course['certificate'] ? 'checked' : '' ?> class="h-4 w-4 text-secondary">
        <label for="certificate" class="ml-2 text-sm text-dark">Offer Certificate</label>
    </div>

    <div>
        <label for="resources" class="block text-sm font-medium text-dark mb-1">Resources/Requirements</label>
        <textarea id="resources" name="resources" rows="3" class="w-full px-4 py-3 border border-borderColor rounded-lg resize-none"><?= $course['resources'] ?></textarea>
    </div>
</div>
