<form id="partnerForm" action="<?= base_url('auth/partners/create') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">

    <!-- CSRF Protection -->
    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
    
    <!-- Partner Name -->
    <div>
        <label for="partner_name" class="block text-sm font-medium text-gray-700 mb-1">Partner Name <span class="text-red-500">*</span></label>
        <input type="text" id="partner_name" name="partner_name" placeholder="Kenya Water & Sanitation Civil Society Network"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <p class="text-xs text-gray-500">Please enter the official name of the partner organization.</p>
    </div>

    <!-- Select Partnership Type -->
    <div>
        <label for="partnership_type" class="block text-sm font-medium text-gray-700 mb-1">Partnership Type <span class="text-red-500">*</span></label>
        <select id="partnership_type" name="partnership_type" 
                class="select2 w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-white appearance-none">
            <option value="" disabled selected>Select partnership type</option>
            <option value="strategic_partner">Strategic Partner</option>
            <option value="supporter">Implementation Partner</option>
            <option value="donor">Funding Partner</option>
            <option value="supporter">Technical Partner</option>
        </select>
        <p class="text-xs text-gray-500">Choose the type of partnership that best describes your organization.</p>
    </div>
    
    <!-- Website URL -->
    <div>
        <label for="website_url" class="block text-sm font-medium text-gray-700 mb-1">Website URL <span class="text-red-500">*</span></label>
        <input type="url" id="website_url" name="website_url" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                placeholder="https://example.com">
        <p class="text-xs text-gray-500">Enter the full URL of the partner's official website.</p>
    </div>
    
    <!-- Logo Upload -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Partner Logo <span class="text-red-500">*</span></label>
        
        <!-- Upload Container -->
        <div>
            <div id="upload-container" class="dropzone border-2 border-dashed border-slate-300 rounded-lg p-4 text-center cursor-pointer hover:border-secondaryShades-400 transition-colors">
                <div class="dz-message" data-dz-message>
                    <i data-lucide="upload" class="w-8 h-8 text-slate-400 mx-auto"></i>
                    <p class="text-sm text-slate-500 mt-2">Drag & drop your logo here or click to browse</p>
                    <p class="text-xs text-slate-400 mt-1">(Max file size: 5MB)</p>
                </div>
            </div>
            <input type="hidden" name="logo_filename" id="logo_filename">
        </div>
        
        <!-- Progress Bar -->
        <div id="upload-progress" class="mt-2 hidden">
            <div class="flex justify-between text-xs text-gray-500 mb-1">
                <span>Uploading...</span>
                <span id="progress-percent">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div id="progress-bar" class="bg-primary-600 h-2 rounded-full" style="width: 0%"></div>
            </div>
        </div>
        
        <!-- Preview Container -->
        <div id="preview-container" class="mt-2 flex flex-wrap gap-2"></div>
    </div>
    
    <!-- Form Actions -->
    <div class="flex justify-end space-x-3 pt-2">
        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-primaryShades-100 text-primary rounded-full shadow-md">
            Cancel
        </button>
        <button type="submit" class="gradient-btn flex items-center px-4 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
            <span class="mr-2"><i data-lucide="plus" class="w-4 h-4"></i></span>
            <span>Create Partner</span>
        </button>
    </div>
</form>