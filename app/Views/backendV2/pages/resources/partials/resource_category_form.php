<form id="createResourceCategoryForm" class="space-y-4">
    <div class="grid grid-cols-1 gap-4">
        <div>
            <label for="resource_category_name" class="block text-sm font-medium text-gray-700 mb-1">Category Name <span class="text-red-500">*</span></label>
            <input type="text" name="resource_category_name" placeholder="Enter resource category name" id="resource_category_name" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>
    </div>

    <div>
        <label for="resource_category_description" class="block text-sm font-medium text-gray-700 mb-1">Resource Description</label>
        <textarea name="resource_category_description" id="resource_category_description" rows="3" placeholder="Enter resource description" class="w-full px-4 py-3 border border-borderColor rounded-lg !focus:ring-2 !focus:ring-secondary"></textarea>
    </div>

    <!-- CSRF Token for security -->
    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

    <div class="flex justify-end space-x-3 pt-2">
        <button type="button" onclick="closeModal()" class="flex items-center justify-center gap-2 px-4 py-2 bg-primaryShades-100 text-primary rounded-full shadow-md">
            <span>Cancel</span>
            <i data-lucide="circle-minus" class="text-primary w-4 h-4 z-10"></i>
        </button>
        <button type="submit" class="gradient-btn flex items-center justify-center gap-2 px-4 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
            <span>Create Category</span>
            <i data-lucide="circle-plus" class="text-white w-4 h-4 z-10"></i>
        </button>
    </div>
</form>