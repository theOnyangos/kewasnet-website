<form action="<?= site_url('auth/pillars/create-document-type') ?>" method="POST" class="pt-3">
    <div class="">
        <!-- Document Type Name Field -->
         <div class="mb-6">
            <div class="">
                <label for="name" class="block text-sm font-medium text-dark mb-1">Document Type Name <span class="text-red-500">*</span></label>
                <input type="text" id="documentTypeName" name="name" maxlength="255" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Enter category name">
            </div>
            <!-- Input short description -->
            <p class="text-xs text-gray-500"> This title will be used to organize pillar articles. </p>
        </div>

        <!-- Slug Field -->
        <div class="mb-6">
            <label for="articleSlug" class="block text-sm font-medium text-dark mb-1">URL Slug <span class="text-red-500">*</span></label>
            <div class="flex w-full">
                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-borderColor bg-lightGray text-dark text-sm whitespace-nowrap">
                    <?= base_url() ?>
                </span>
                <input type="text" id="articleSlug" name="slug" maxlength="100" class="flex-1 min-w-0 px-4 py-3 rounded-none rounded-r-md border border-borderColor focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="url-friendly-slug">
            </div>
            <p class="mt-1 text-xs text-gray-500">This will be the URL for your document type. It should be unique and descriptive.</p>
        </div>

        <!-- Color Name Field -->
        <div class="mb-6">
            <div class="">
                <label for="color" class="block text-sm font-medium text-dark mb-1">Color <span class="text-red-500">*</span></label>
                <input type="text" id="color" name="color" maxlength="7" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="#RRGGBB">
            </div>
            <p class="text-xs text-gray-500"> This color will be used to sort your pillar articles. </p>
        </div>

        <div class="flex justify-end">
            <!-- Close Button -->
            <button type="button" onclick="closeModal()" class="bg-primaryShades-50 hover:bg-primaryShades-100 text-primary hover:text-gray-700 px-8 py-2 mr-3 rounded-full flex gap-2 items-center transition-all duration-300">
                <i data-lucide="x" class="w-4 h-4"></i>
                <span>Close</span>
            </button>

            <!-- Submit Button -->
            <button id="submitDocumentTypeBtn" type="submit" class="gradient-btn flex items-center px-8 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
                <i data-lucide="check" class="w-4 h-4 mr-2 z-10"></i>
                <span>Create</span>
            </button>
        </div>
    </div>
</form>