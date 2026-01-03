<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('frontendV2/website/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-primary to-secondary text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl font-bold mb-6">Our Programs</h1>
            <p class="text-xl max-w-3xl mx-auto">
                Comprehensive initiatives driving sustainable water, sanitation, and hygiene solutions across Kenya
            </p>
        </div>
    </section>

    <!-- Programs Grid -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if (!empty($programs)): ?>
                    <?php foreach ($programs as $program): ?>
                        <?php 
                        // Convert to object if it's an array for consistency
                        $program = is_array($program) ? (object)$program : $program;
                        ?>
                        <!-- Program Card -->
                        <div class="bg-light rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                            <?php if (!empty($program->image_url)): ?>
                                <!-- Program Image -->
                                <div class="w-full h-48 overflow-hidden bg-gray-100">
                                    <img src="<?= base_url($program->image_url) ?>" 
                                         alt="<?= esc($program->title) ?>" 
                                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                </div>
                            <?php endif; ?>
                            
                            <div class="p-8">
                                <div class="w-16 h-16 <?= $program->background_color ?> rounded-lg flex items-center justify-center mb-6">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <?= $program->icon_svg ?>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold mb-4 text-primaryShades-800"><?= esc($program->title) ?></h3>
                                <p class="text-dark mb-6">
                                    <?= esc($program->description) ?>
                                </p>
                                <a href="<?= base_url('programs/' . $program->slug) ?>" class="text-primary font-semibold hover:underline">Learn More →</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-12">
                        <h3 class="text-xl font-semibold text-gray-600 mb-4">No programs available</h3>
                        <p class="text-gray-500">Please check back later for updates on our programs.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-primary text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-6">Get Involved in Our Programs</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto">
                Join us in creating lasting change in Kenya's WASH sector through collaboration, innovation, and shared commitment.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button type="button" onclick="showCreatePartnerModal()" class="bg-white text-primary px-8 py-3 rounded-lg font-semibold hover:bg-lightGray transition-colors">
                    Partner With Us
                </button>
                <a href="#" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-primary transition-colors">
                    Download Program Guide
                </a>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    const createPartnerURL = '<?= base_url('api/partners/create') ?>';
    let selectedFile = null; // Add this variable declaration

    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    }

    function showCreatePartnerModal() {
        openModal({
            title: 'Register as partner',
            iconBg: 'bg-blue-100',
            content: `<?= view('frontendV2/website/pages/programs/partials/partner_form') ?>`,
            size: 'sm:max-w-2xl',
            headerClasses: 'sm:flex sm:items-start',
            onOpen: function() {
                initFileUpload();

                $('.select2').select2({
                    theme: 'bootstrap-5',
                    width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                    placeholder: $(this).data('placeholder'),
                    minimumResultsForSearch: Infinity,
                });

                $('#partnerForm')[0].reset();
                $('#logo_filename').val('');
                selectedFile = null;
                $('#preview-container').empty();
                clearFormErrors(); // Clear any previous errors

                $('#partnerForm').off('submit').on('submit', function(e) {
                    e.preventDefault();

                    const form = $(this);
                    const submitBtn = form.find('button[type="submit"]');
                    const originalBtnText = submitBtn.html();
                    
                    submitBtn.prop('disabled', true);
                    submitBtn.html(`
                        <span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                        Processing...
                    `);
                    
                    // Show progress bar
                    $('#upload-progress').removeClass('hidden');
                    $('#progress-bar').css('width', '0%');
                    $('#progress-percent').text('0%');

                    // Clear previous errors
                    clearFormErrors();

                    // Prepare form data
                    const formData = new FormData(form[0]);
                    
                    // If we have a file selected, upload it first
                    if (selectedFile) {
                        formData.append('partner_logo', selectedFile);
                    }

                    $.ajax({
                        url: createPartnerURL,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        xhr: function() {
                            const xhr = new window.XMLHttpRequest();
                            xhr.upload.addEventListener('progress', function(e) {
                                if (e.lengthComputable) {
                                    const percent = Math.round((e.loaded / e.total) * 100);
                                    $('#progress-bar').css('width', percent + '%');
                                    $('#progress-percent').text(percent + '%');
                                }
                            }, false);
                            return xhr;
                        },
                        success: function(response) {
                            // Parse the response if it's a string
                            const data = typeof response === 'string' ? JSON.parse(response) : response;
                            
                            if (data.status === 'success') {
                                showNotification('success', data.message || 'Partner created successfully!');
                                
                                // Close modal after a brief delay to show notification
                                setTimeout(() => {
                                    closeModal();
                                }, 1500);
                                
                                // Reset form
                                form[0].reset();
                                selectedFile = null;
                                $('#preview-container').empty();
                                
                            } else {
                                if (data.errors) {
                                    showFormErrors(data.errors);
                                } else {
                                    showNotification('error', data.message || 'Failed to create partner');
                                }
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'An error occurred while saving';
                            
                            try {
                                const response = typeof xhr.responseText === 'string' ? JSON.parse(xhr.responseText) : xhr.responseJSON;
                                if (response && response.message) {
                                    errorMessage = response.message;
                                } else if (response && response.errors) {
                                    showFormErrors(response.errors);
                                    return;
                                }
                            } catch (e) {
                                console.error('Error parsing response:', e);
                            }
                            
                            showNotification('error', errorMessage);
                        },
                        complete: function() {
                            submitBtn.prop('disabled', false).html(originalBtnText);
                            $('#upload-progress').addClass('hidden');
                        }
                    });
                });
            },
            onClose: function() {
                // Cleanup or reset actions
                $('#partnerForm')[0].reset();
                $('#logo_filename').val('');
                selectedFile = null;
                $('#preview-container').empty();
                clearFormErrors();
            }
        });
    }

    function initFileUpload() {
        const uploadContainer = $('#upload-container');
        
        // Clear previous content and events
        uploadContainer.off().empty().html(`
            <div class="dz-message">
                <i data-lucide="upload" class="w-8 h-8 text-slate-400 mx-auto"></i>
                <p class="text-sm text-slate-500 mt-2">Drag & drop your logo here or click to browse</p>
                <p class="text-xs text-slate-400 mt-1">(Max file size: 5MB)</p>
            </div>
        `);
        lucide.createIcons();

        // Handle click to select file
        uploadContainer.on('click', function() {
            $('<input>').attr({
                type: 'file',
                accept: 'image/*',
                class: 'hidden'
            }).on('change', function(e) {
                handleFileSelect(e.target.files);
                $(this).remove(); // Clean up the temporary input
            }).click();
        });

        // Handle drag and drop
        uploadContainer.on('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('border-primary-500 bg-gray-50');
        }).on('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('border-primary-500 bg-gray-50');
        }).on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('border-primary-500 bg-gray-50');
            if (e.originalEvent.dataTransfer.files.length) {
                handleFileSelect(e.originalEvent.dataTransfer.files);
            }
        });
    }

    function handleFileSelect(files) {
        if (files.length === 0) return;

        const file = files[0];
        
        // Validate file type
        if (!file.type.match('image.*')) {
            showNotification('error', 'Please upload an image file (JPEG, PNG, etc.)');
            return;
        }

        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            showNotification('error', 'File size exceeds 5MB limit');
            return;
        }

        // Store the selected file
        selectedFile = file;

        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#preview-container').html(`
                <div class="relative w-24 h-24 rounded-md overflow-hidden border border-gray-200 shadow-sm group">
                    <img src="${e.target.result}" class="w-full h-full object-contain bg-gray-50">
                    <button type="button" class="absolute top-0 right-0 bg-white rounded-full p-1 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity">
                        <i data-lucide="x" class="w-4 h-4 text-red-500"></i>
                    </button>
                </div>
            `);
            
            // Handle remove button
            $('#preview-container button').on('click', function(e) {
                e.stopPropagation();
                $('#preview-container').empty();
                selectedFile = null;
            });
            
            lucide.createIcons();
        };
        reader.readAsDataURL(file);
    }

    function showFormErrors(errors) {
        // Clear previous errors first
        clearFormErrors();
        
        $.each(errors, function(field, message) {
            const input = $(`[name="${field}"]`);
            
            if (input.length) {
                // Add Tailwind error classes
                input.addClass('border-red-500 focus:border-red-500 focus:ring-red-500');
                input.removeClass('border-borderColor focus:border-primary focus:ring-primary');
                
                // Create error message with icon
                const errorElement = $('<div>')
                    .addClass('text-red-500 text-sm mt-1 error-message flex items-center gap-1')
                    .html(`
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <span>${message}</span>
                    `);
                
                // Handle Select2 elements specifically
                if (field === 'partnership_type') {
                    const select2Container = input.next('.select2-container');
                    if (select2Container.length) {
                        // Style the Select2 container
                        select2Container.find('.select2-selection').addClass('border-red-500');
                        select2Container.after(errorElement);
                    } else {
                        input.after(errorElement);
                    }
                } else {
                    input.after(errorElement);
                }
            }
        });
    }

    function clearFormErrors() {
        // Remove error classes
        $('input, select, textarea').removeClass('border-red-500 focus:border-red-500 focus:ring-red-500');
        $('input, select, textarea').addClass('border-borderColor focus:border-primary focus:ring-primary');
        
        // Remove error messages
        $('.error-message').remove();
        
        // Clear Select2 error styling
        $('.select2-container .select2-selection').removeClass('border-red-500');
    }
</script>
<?= $this->endSection() ?>