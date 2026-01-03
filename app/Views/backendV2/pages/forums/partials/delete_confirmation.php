<div class="">
    <div class="mb-6">
        <div class="flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50">
            <i data-lucide="alert-triangle" class="w-5 h-5 mr-2"></i>
            <span class="font-medium mr-2">Warning!</span> This action cannot be undone.
        </div>
        <p class="text-gray-700 mb-2">You're about to delete the forum <span class="font-semibold text-red-500">"<?= $forum->name ?>"</span>.</p>
        <p class="text-gray-700">All discussions and replies in this forum will be permanently deleted.</p>
    </div>
    
    <div class="flex justify-end space-x-3">
        <button onclick="closeModal()" class="flex justify-center items-center bg-red-50 hover:bg-red-100 px-4 py-2 rounded-md text-red-700 transition-colors">
            <i data-lucide="circle-minus" class="w-4 h-4 mr-2"></i>
            Cancel
        </button>
        <button id="confirmDeleteBtn" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors flex items-center">
            <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
            Delete Permanently
        </button>
    </div>
</div>