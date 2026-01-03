<form id="editResourceForm" class="space-y-4">
    <input type="hidden" name="resource_id" id="edit_resource_id">
    
    <div class="grid grid-cols-1 gap-4">
        <div>
            <label for="edit_resource_name" class="block text-sm font-medium text-gray-700 mb-1">Resource Name <span class="text-red-500">*</span></label>
            <input type="text" name="resource_name" placeholder="Enter resource name" id="edit_resource_name" 
                    class="w-full max-w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <p class="mt-1 text-xs text-gray-500">Provide a clear and descriptive name for the resource.</p>
        </div>
    </div>

    <div>
        <label for="edit_category_id" class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
        <select id="edit_category_id" name="category_id" class="w-full px-4 py-3 border border-borderColor rounded-lg !focus:ring-2 !focus:ring-secondary select2">
            <option value="">Select Category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <p class="mt-1 text-xs text-gray-500">Choose the most appropriate category for the resource.</p>
    </div>

    <div>
        <label for="edit_resource_description" class="block text-sm font-medium text-gray-700 mb-1">Resource Description</label>
        <textarea name="resource_description" id="edit_resource_description" rows="3" placeholder="Enter resource description" class="w-full px-4 py-3 border border-borderColor rounded-lg !focus:ring-2 !focus:ring-secondary"></textarea>
    </div>

    <div>
        <div class="flex items-center">
            <input type="checkbox" name="is_featured" id="edit_is_featured" value="1" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
            <label for="edit_is_featured" class="ml-2 block text-sm text-gray-700">
                Mark as featured resource
            </label>
        </div>
        <p class="mt-1 text-xs text-gray-500">Featured resources will be highlighted and displayed prominently on the website.</p>
    </div>

    <!-- Current File Display -->
    <div id="current-file-display" class="border border-gray-200 rounded-lg p-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Current File</label>
        <div id="current-file-info" class="text-sm text-gray-600">
            <!-- File info will be populated here -->
        </div>
        <input type="hidden" name="current_attachment_id" id="current_attachment_id">
        <input type="hidden" name="remove_current_file" id="remove_current_file" value="0">
    </div>

    <!-- Optional: Replace File -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Replace File (Optional)</label>
        <div id="edit-upload-container" class="border border-dashed border-slate-200 rounded-lg p-4 text-center cursor-pointer hover:border-secondaryShades-400 transition-colors"></div>
        <input type="hidden" name="resource_filename" id="edit_resource_filename" accept="application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
        <p class="mt-1 text-xs text-gray-500">Leave empty to keep the current file.</p>
    </div>

    <!-- Progress Bar -->
    <div id="edit-upload-progress" class="mt-2 hidden">
        <div class="flex justify-between text-xs text-gray-500 mb-1">
            <span>Uploading...</span>
            <span id="edit-progress-percent">0%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div id="edit-progress-bar" class="bg-primary-600 h-2 rounded-full" style="width: 0%"></div>
        </div>
    </div>

    <!-- Preview Container -->
    <div id="edit-preview-container" class="mt-2 flex flex-wrap gap-2"></div>

    <!-- CSRF Token for security -->
    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

    <div class="flex justify-end space-x-3 pt-2">
        <button type="button" onclick="closeModal()" class="flex items-center justify-center gap-2 px-4 py-2 bg-primaryShades-100 text-primary rounded-full shadow-md">
            <span>Cancel</span>
            <i data-lucide="circle-minus" class="text-primary w-4 h-4 z-10"></i>
        </button>
        <button type="submit" class="gradient-btn flex items-center justify-center gap-2 px-4 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
            <span>Update Resource</span>
            <i data-lucide="save" class="text-white w-4 h-4 z-10"></i>
        </button>
    </div>
</form>
