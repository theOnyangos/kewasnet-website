<!-- Add Section Modal -->
<div id="sectionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-2xl">
        <h3 class="text-2xl font-bold text-primary mb-6" id="sectionModalTitle">Add Section</h3>
        <form id="addSectionForm">
            <input type="hidden" id="section_id" name="section_id">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-dark mb-2">Section Title <span class="text-red-500">*</span></label>
                    <input type="text" id="section_title" name="section_title" placeholder="e.g., Introduction to Web Development" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary" required>
                    <p class="text-xs text-gray-500 mt-1">Give this section a clear, descriptive title</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark mb-2">Description</label>
                    <textarea id="section_description" name="section_description" rows="3" placeholder="Brief overview of what students will learn in this section..." class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary resize-none"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Provide a brief summary of what this section covers</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark mb-2">Order Index</label>
                    <input type="number" id="section_order" name="order_index" value="1" min="1" placeholder="1" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary">
                    <p class="text-xs text-gray-500 mt-1">Set the position of this section in the course curriculum</p>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeSectionModal()" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="gradient-btn px-8 py-3 rounded-[50px] text-white font-medium hover:shadow-lg transition-all">
                        <span id="sectionSubmitText">Add Section</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
