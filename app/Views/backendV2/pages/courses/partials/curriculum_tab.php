<!-- Curriculum Tab -->
<div id="content-curriculum" class="course-tab-panel hidden">
    <div class="p-6 space-y-6">
        <!-- Sections List -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-800">Course Curriculum</h3>
                <p class="text-sm text-gray-500">Organize your course into sections and lectures</p>
            </div>
            <button type="button" onclick="showAddSectionModal()" class="gradient-btn flex items-center px-6 py-3 rounded-full text-white hover:shadow-lg transition-all duration-300 whitespace-nowrap">
                <i data-lucide="plus" class="w-5 h-5 mr-2 z-10"></i>
                <span class="font-medium">Add Section</span>
            </button>
        </div>

        <div id="sections-container" class="space-y-4">
            <?php if (empty($sections)): ?>
            <div class="text-center py-12 bg-gray-50 rounded-lg">
                <i data-lucide="layers" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                <p class="text-gray-600">No sections yet. Add your first section to start building the curriculum.</p>
            </div>
            <?php else: ?>
                <?php foreach ($sections as $section): ?>
                <div class="border border-gray-200 rounded-lg p-4 bg-white section-item" data-section-id="<?= $section['id'] ?>">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-800"><?= esc($section['title']) ?></h4>
                            <p class="text-sm text-gray-600 mt-1"><?= esc($section['description']) ?></p>
                            <?php
                            $lectureModel = new \App\Models\CourseLectureModel();
                            $lectureCount = $lectureModel->where('section_id', $section['id'])
                                ->where('deleted_at', null)
                                ->countAllResults();
                            $totalDuration = $lectureModel->where('section_id', $section['id'])
                                ->where('deleted_at', null)
                                ->selectSum('duration')
                                ->get()
                                ->getRow()
                                ->duration ?? 0;
                            ?>
                            <div class="flex gap-4 mt-2 text-xs text-gray-500">
                                <span><i data-lucide="book-open" class="w-3 h-3 inline"></i> <?= $lectureCount ?> lecture<?= $lectureCount != 1 ? 's' : '' ?></span>
                                <span><i data-lucide="clock" class="w-3 h-3 inline"></i> <?= $totalDuration ?> min</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="editSection('<?= $section['id'] ?>')" class="text-blue-600 hover:text-blue-800">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </button>
                            <button onclick="deleteSection('<?= $section['id'] ?>')" class="text-red-600 hover:text-red-800">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                            <button onclick="toggleLectures('<?= $section['id'] ?>')" class="text-gray-600 hover:text-gray-800">
                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <div id="lectures-<?= $section['id'] ?>" class="mt-4 pl-4 border-l-2 border-gray-200 hidden">
                        <button onclick="showAddLectureModal('<?= $section['id'] ?>')" class="text-sm text-blue-600 hover:text-blue-800 mb-2 flex items-center gap-1">
                            <i data-lucide="plus" class="w-3 h-3"></i> Add Lecture
                        </button>
                        <div class="space-y-2" id="lecture-list-<?= $section['id'] ?>">
                            <?php
                            $lectureModel = new \App\Models\CourseLectureModel();
                            $lectures = $lectureModel->where('section_id', $section['id'])
                                ->where('deleted_at', null)
                                ->orderBy('order_index', 'ASC')
                                ->findAll();

                            if (empty($lectures)): ?>
                                <p class="text-sm text-gray-500">No lectures yet</p>
                            <?php else: ?>
                                <?php foreach ($lectures as $lecture): ?>
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded hover:bg-gray-100">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="play-circle" class="w-4 h-4 text-gray-400"></i>
                                        <span class="text-sm"><?= esc($lecture['title']) ?></span>
                                        <?php if ($lecture['is_free_preview']): ?>
                                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded">Free</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex gap-2">
                                        <button onclick="editLecture('<?= $lecture['id'] ?>')" class="text-blue-600 hover:text-blue-800">
                                            <i data-lucide="edit" class="w-3 h-3"></i>
                                        </button>
                                        <button onclick="deleteLecture('<?= $lecture['id'] ?>')" class="text-red-600 hover:text-red-800">
                                            <i data-lucide="trash-2" class="w-3 h-3"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
