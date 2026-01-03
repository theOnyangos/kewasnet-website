<!-- All Javascript files -->
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>

<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- If you want gradient fills -->
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-gradient"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>

<!-- For Excel export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- For PDF export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- jQuery Modal -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />

<!-- Select 2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<!-- Drop Zone -->
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Custom JS Files -->
<script src="<?= base_url('assets/js/custom.js') ?>"></script>
<script src="<?= base_url('assets/js/notifications.js') ?>"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize GSAP animations
        gsap.registerPlugin();

        // Initialize Lucide icons
        lucide.createIcons();

        $('.summernote-editor').summernote({
            height: 300,
            dialogsInBody: true,
            disableDragAndDrop: false,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onImageUpload: function(files) {
                    uploadSummernoteImage(files[0]);
                },
                onMediaDelete: function($target) {
                    deleteSummernoteImage($target.attr('src'));
                },
                onInit: function() {
                    // Replace fullscreen button icon
                    $('.note-btn-fullscreen').html('<i class="fas fa-expand-arrows-alt"></i>');
                },
                onFullscreen: function() {
                    // Force background color when toggling fullscreen
                    $('.note-editor.note-frame.fullscreen .note-editable').css('background', 'white');
                }
            }
        });

        // Handle upload image in summernote
        function uploadSummernoteImage(file) {
            var formData = new FormData();
            formData.append('file', file);

            fetch('<?= base_url('summernote/upload'); ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.url) {
                    $('.summernote-editor').summernote('insertImage', data.url);
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Image upload failed');
            });
        }

        // Handle delete uploaded images
        function deleteSummernoteImage(src) {
            if (confirm('Are you sure you want to delete this image?')) {
                fetch('<?= base_url('summernote/delete'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'src=' + encodeURIComponent(src)
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.error || 'Failed to delete image');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Image deletion failed');
                });
            }
        }
        
        /* === Sidebar State Management - Check localStorage for saved state === */
        let isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        
        // Initialize sidebar state
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleSidebar');
        
        // Apply initial state
        function applySidebarState() {
            if (isCollapsed) {
                sidebar.style.width = '80px';
                document.querySelectorAll('.sidebar-label, .sidebar-footer-text').forEach(el => {
                    el.style.display = 'none';
                });
                document.querySelectorAll('.sidebar-icon').forEach(icon => {
                    icon.classList.add('mx-auto');
                    icon.classList.remove('md:mr-3', 'md:ml-0');
                });
                const menuIcon = document.querySelector('.sidebar-toggle-icon');
                if (menuIcon) {
                    menuIcon.setAttribute('data-lucide', 'x');
                    lucide.createIcons();
                }
            } else {
                sidebar.style.width = '280px';
                document.querySelectorAll('.sidebar-label, .sidebar-footer-text').forEach(el => {
                    el.style.display = 'block';
                });
                document.querySelectorAll('.sidebar-icon').forEach(icon => {
                    icon.classList.add('md:mr-3', 'md:ml-0');
                    icon.classList.remove('mx-auto');
                });
                const menuIcon = document.querySelector('.sidebar-toggle-icon');
                if (menuIcon) {
                    menuIcon.setAttribute('data-lucide', 'menu');
                    lucide.createIcons();
                }
            }
        }
        
        // Apply initial state on load
        applySidebarState();
        
        /* === Sidebar Toggle === */
        toggleBtn.addEventListener('click', function() {
            isCollapsed = !isCollapsed;
            localStorage.setItem('sidebarCollapsed', isCollapsed);
            
            if (isCollapsed) {
                gsap.to(sidebar, {
                    width: 80,
                    duration: 0.3,
                    ease: "power2.inOut",
                    onComplete: function() {
                        // Change icon to X
                        const menuIcon = document.querySelector('.sidebar-toggle-icon');
                        if (menuIcon) {
                            menuIcon.setAttribute('data-lucide', 'x');
                            lucide.createIcons();
                        }
                        
                        // Hide elements
                        document.querySelectorAll('.sidebar-label, .sidebar-footer-text').forEach(el => {
                            el.style.opacity = '0';
                            setTimeout(() => {
                                el.style.display = 'none';
                            }, 200);
                        });
                        
                        // Center icons
                        document.querySelectorAll('.sidebar-icon').forEach(icon => {
                            icon.classList.add('mx-auto');
                            icon.classList.remove('md:mr-3', 'md:ml-0');
                        });
                    }
                });
            } else {
                // Show elements first
                document.querySelectorAll('.sidebar-label, .sidebar-footer-text').forEach(el => {
                    el.style.display = 'block';
                    setTimeout(() => {
                        el.style.opacity = '1';
                    }, 10);
                });
                
                gsap.to(sidebar, {
                    width: 280,
                    duration: 0.3,
                    ease: "power2.inOut",
                    onStart: function() {
                        // Change icon to menu
                        const menuIcon = document.querySelector('.sidebar-toggle-icon');
                        if (menuIcon) {
                            menuIcon.setAttribute('data-lucide', 'menu');
                            lucide.createIcons();
                        }
                        
                        // Restore icon positions
                        document.querySelectorAll('.sidebar-icon').forEach(icon => {
                            icon.classList.add('md:mr-3', 'md:ml-0');
                            icon.classList.remove('mx-auto');
                        });
                    }
                });
            }
        });

        /* === Tooltip Hover - Add hover effects for tooltips === */
        document.querySelectorAll('.group\\/minimized').forEach(item => {
            item.addEventListener('mouseenter', function() {
                if (isCollapsed) {
                    const label = this.querySelector('.sidebar-label');
                    if (label) {
                        label.style.display = 'block';
                        setTimeout(() => {
                            label.style.opacity = '1';
                        }, 10);
                    }
                }
            });
            
            item.addEventListener('mouseleave', function() {
                if (isCollapsed) {
                    const label = this.querySelector('.sidebar-label');
                    if (label) {
                        label.style.opacity = '0';
                        setTimeout(() => {
                            label.style.display = 'none';
                        }, 200);
                    }
                }
            });
        });

        /* === Responsive Sidebar Handler === */
        function handleResponsiveSidebar() {
            const screenWidth = window.innerWidth;
            
            // On mobile devices (< 768px), force sidebar to be collapsed
            if (screenWidth < 768) {
                if (!isCollapsed) {
                    isCollapsed = true;
                    localStorage.setItem('sidebarCollapsed', isCollapsed);
                    applySidebarState();
                }
                // Hide sidebar on very small screens
                if (screenWidth < 640) {
                    sidebar.style.display = 'none';
                }
            } else {
                // Show sidebar on larger screens
                sidebar.style.display = 'block';
                
                // Restore saved state on desktop
                const savedState = localStorage.getItem('sidebarCollapsed') === 'true';
                if (savedState !== isCollapsed) {
                    isCollapsed = savedState;
                    applySidebarState();
                }
            }
        }

        // Initialize and add resize listener
        handleResponsiveSidebar();
        window.addEventListener('resize', handleResponsiveSidebar);
    });
</script>