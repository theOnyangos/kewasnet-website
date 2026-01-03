<!-- Add Lecture Modal -->
<div id="lectureModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <h3 class="text-2xl font-bold text-primary mb-6" id="lectureModalTitle">Add Lecture</h3>
        <form id="addLectureForm">
            <input type="hidden" id="lecture_section_id" name="section_id">
            <input type="hidden" id="lecture_id" name="lecture_id">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-dark mb-2">Lecture Title <span class="text-red-500">*</span></label>
                    <input type="text" id="lecture_title" name="lecture_title" placeholder="e.g., Understanding HTML Tags and Attributes" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary" required>
                    <p class="text-xs text-gray-500 mt-1">Enter a clear, descriptive title for this lecture</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark mb-2">Description</label>
                    <textarea id="lecture_description" name="lecture_description" rows="3" placeholder="What students will learn in this lecture..." class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary resize-none"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Provide a brief overview of the lecture content</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">Video URL (YouTube/Vimeo)</label>
                        <input type="url" id="lecture_video_url" name="video_url" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary" placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/...">
                        <p class="text-xs text-gray-500 mt-1">Paste the full URL of your video</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">Duration (minutes)</label>
                        <input type="number" id="lecture_duration" name="duration" min="1" placeholder="15" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary">
                        <p class="text-xs text-gray-500 mt-1">Lecture length in minutes</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">Order Index</label>
                        <input type="number" id="lecture_order" name="order_index" value="1" min="1" placeholder="1" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary">
                        <p class="text-xs text-gray-500 mt-1">Position of this lecture within the section</p>
                    </div>
                    <div class="flex flex-col gap-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="lecture_is_preview" name="is_preview" class="w-4 h-4 text-secondary border-gray-300 rounded focus:ring-2 focus:ring-secondary">
                            <span class="ml-2 text-sm font-medium text-dark">Preview</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="lecture_is_free" name="is_free_preview" class="w-4 h-4 text-secondary border-gray-300 rounded focus:ring-2 focus:ring-secondary">
                            <span class="ml-2 text-sm font-medium text-dark">Free Preview</span>
                        </label>
                        <p class="text-xs text-gray-500">Allow non-enrolled users to watch this lecture</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeLectureModal()" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="gradient-btn px-8 py-3 rounded-[50px] text-white font-medium hover:shadow-lg transition-all">
                        <span id="lectureSubmitText">Add Lecture</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
