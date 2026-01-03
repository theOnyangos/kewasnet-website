<!-- Pricing -->
<div class="border-b pb-6">
    <h3 class="text-lg font-medium text-dark mb-1">Pricing</h3>
    <p class="text-xs text-gray-500 mb-4">Configure pricing for your course</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="flex flex-col space-y-3">
            <label class="flex items-center cursor-pointer p-3 border border-borderColor rounded-lg hover:bg-gray-50 has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-50">
                <input type="radio" name="is_paid" value="0" <?= !$course['is_paid'] ? 'checked' : '' ?> class="h-4 w-4 text-secondary focus:ring-secondary">
                <div class="ml-3">
                    <span class="text-sm font-medium text-dark">Free Course</span>
                    <p class="text-xs text-gray-500">No payment required</p>
                </div>
            </label>
            <label class="flex items-center cursor-pointer p-3 border border-borderColor rounded-lg hover:bg-gray-50 has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-50">
                <input type="radio" name="is_paid" value="1" <?= $course['is_paid'] ? 'checked' : '' ?> class="h-4 w-4 text-secondary focus:ring-secondary">
                <div class="ml-3">
                    <span class="text-sm font-medium text-dark">Paid Course</span>
                    <p class="text-xs text-gray-500">Requires payment to enroll</p>
                </div>
            </label>
        </div>

        <div id="price-field" style="<?= $course['is_paid'] ? '' : 'display: none;' ?>">
            <label for="price" class="block text-sm font-medium text-dark mb-1">Price (KES)</label>
            <input type="number" id="price" name="price" value="<?= $course['price'] ?>" step="0.01" min="0" placeholder="0.00" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary">
            <p class="text-xs text-gray-500 mt-1">Regular course price</p>
        </div>

        <div id="discount-field" style="<?= $course['is_paid'] ? '' : 'display: none;' ?>">
            <label for="discount_price" class="block text-sm font-medium text-dark mb-1">Discount Price (KES)</label>
            <input type="number" id="discount_price" name="discount_price" value="<?= $course['discount_price'] ?>" step="0.01" min="0" placeholder="0.00" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary">
            <p class="text-xs text-gray-500 mt-1">Optional discounted price</p>
        </div>
    </div>
</div>
