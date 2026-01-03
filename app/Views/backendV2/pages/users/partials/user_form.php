<form id="createUserForm" action="<?= base_url('auth/users/create') ?>" method="POST" class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
            <input type="text" name="first_name" placeholder="Enter first name" id="first_name" class="w-full px-4 py-3 border border-borderColor rounded-lg !focus:ring-2 !focus:ring-secondary">
        </div>
        
        <div>
            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
            <input type="text" name="last_name" placeholder="Enter last name" id="last_name" class="w-full px-4 py-3 border border-borderColor rounded-lg !focus:ring-2 !focus:ring-secondary">
        </div>
    </div>
     
    <div>
        <label for="role_id" class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
        <select id="role_id" name="role_id" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary select2">
            <option value="">Select Role</option>
            <?php foreach ($roles as $role): ?>
                <option value="<?= $role['role_id'] ?>"><?= htmlspecialchars($role['role_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
        <input type="email" name="email" id="email" placeholder="Enter email address" class="w-full px-4 py-3 border border-borderColor rounded-lg !focus:ring-2 !focus:ring-secondary">
    </div>

    <div>
        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
        <input type="tel" name="phone_number" id="phone_number" placeholder="Enter phone number" class="w-full px-4 py-3 border border-borderColor rounded-lg !focus:ring-2 !focus:ring-secondary">
    </div>

    <div>
        <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
        <textarea name="bio" id="bio" rows="3" placeholder="Enter bio" class="w-full px-4 py-3 border border-borderColor rounded-lg !focus:ring-2 !focus:ring-secondary"></textarea>
    </div>

    <!-- CSRF Token for security -->
    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

    <div class="flex justify-end space-x-3 pt-2">
        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-primaryShades-100 text-primary rounded-full shadow-md">
            Cancel
        </button>
        <button type="submit" class="gradient-btn flex items-center px-4 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
            <span class="mr-2"><i data-lucide="plus" class="w-4 h-4"></i></span>
            <span>Create User</span>
        </button>
    </div>
</form>