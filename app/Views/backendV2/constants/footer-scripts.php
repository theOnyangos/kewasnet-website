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

        /* === Notification Polling Manager === */
        (function() {
            const NotificationPollingManager = {
                pollInterval: null,
                currentCount: 0,
                pollIntervalMs: 30000, // 30 seconds default
                errorBackoff: 1, // Exponential backoff multiplier
                maxBackoff: 300000, // Max 5 minutes on error
                isPolling: false,
                consecutiveErrors: 0,

                init: function() {
                    // Ensure badge element exists
                    const badge = document.getElementById('notificationBadge');
                    if (!badge) {
                        console.error('Notification badge element not found');
                        return;
                    }

                    console.log('NotificationPollingManager initialized');

                    // Fetch initial count immediately
                    this.fetchCount(true);

                    // Start polling
                    this.startPolling();
                },

                startPolling: function() {
                    if (this.isPolling) {
                        return;
                    }

                    const self = this;
                    this.isPolling = true;
                    this.pollInterval = setInterval(() => {
                        // Only poll if page is visible
                        if (!document.hidden) {
                            self.fetchCount(false);
                        }
                    }, this.getPollInterval());
                },

                stopPolling: function() {
                    if (this.pollInterval) {
                        clearInterval(this.pollInterval);
                        this.pollInterval = null;
                    }
                    this.isPolling = false;
                },

                getPollInterval: function() {
                    // Apply exponential backoff on errors
                    const backoffMultiplier = Math.min(this.errorBackoff, this.maxBackoff / this.pollIntervalMs);
                    return Math.min(this.pollIntervalMs * backoffMultiplier, this.maxBackoff);
                },

                fetchCount: function(isInitial = false) {
                    const self = this;
                    fetch('<?= base_url('auth/notifications/unread-count') ?>', {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            const newCount = parseInt(data.count) || 0;
                            const previousCount = self.currentCount;
                            
                            console.log('Notification count fetched:', newCount);
                            
                            // Reset error backoff on success
                            self.consecutiveErrors = 0;
                            self.errorBackoff = 1;
                            
                            // Update badge
                            self.currentCount = newCount;
                            self.updateBadge(newCount);
                            
                            // Play sound if count increased (but not on initial load)
                            if (!isInitial && newCount > previousCount && newCount > 0) {
                                self.playNotificationSound();
                            }
                            
                            // Restart polling with normal interval if we had errors
                            if (self.consecutiveErrors > 0 && self.errorBackoff > 1) {
                                self.stopPolling();
                                self.startPolling();
                            }
                        } else {
                            console.error('Failed to fetch notification count:', data.message || 'Unknown error');
                            throw new Error(data.message || 'Failed to fetch notification count');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching notification count:', error);
                        self.consecutiveErrors++;
                        
                        // Exponential backoff on errors
                        self.errorBackoff = Math.min(self.errorBackoff * 2, self.maxBackoff / self.pollIntervalMs);
                        
                        // Restart polling with new interval
                        if (self.isPolling) {
                            self.stopPolling();
                            self.startPolling();
                        }
                    });
                },

                updateBadge: function(count) {
                    const badge = document.getElementById('notificationBadge');
                    if (!badge) {
                        console.error('Notification badge element not found');
                        return;
                    }

                    const badgeCount = parseInt(count) || 0;
                    console.log('Updating badge with count:', badgeCount);

                    if (badgeCount > 0) {
                        badge.textContent = badgeCount > 99 ? '99+' : badgeCount.toString();
                        // Remove hidden class first
                        badge.classList.remove('hidden');
                        badge.classList.add('flex');
                        // Use setProperty with !important to override Tailwind's hidden class
                        badge.style.setProperty('display', 'flex', 'important');
                        badge.style.setProperty('visibility', 'visible', 'important');
                        badge.style.setProperty('opacity', '1', 'important');
                        
                        // Verify it's visible
                        setTimeout(() => {
                            const computedStyle = window.getComputedStyle(badge);
                            console.log('Badge computed display:', computedStyle.display);
                            console.log('Badge computed visibility:', computedStyle.visibility);
                            if (computedStyle.display === 'none') {
                                console.warn('Badge display is still none, forcing visibility');
                                badge.style.setProperty('display', 'flex', 'important');
                            }
                        }, 100);
                        
                        console.log('Badge shown with count:', badgeCount);
                    } else {
                        badge.classList.add('hidden');
                        badge.classList.remove('flex');
                        badge.style.setProperty('display', 'none', 'important');
                        console.log('Badge hidden (count is 0)');
                    }
                    
                    // Also update via NotificationManager if it exists (for notifications.js compatibility)
                    if (window.NotificationManager && typeof window.NotificationManager.updateCount === 'function') {
                        // Don't call it to avoid double updates, but sync the count
                        window.NotificationPollingManager.currentCount = badgeCount;
                    }
                },

                playNotificationSound: function() {
                    const sound = document.getElementById('notification-sound');
                    if (!sound) return;

                    try {
                        sound.volume = 0.3;
                        sound.play().catch(error => {
                            // Ignore playback errors
                        });
                    } catch (error) {
                        console.error('Error playing notification sound:', error);
                    }
                },

                disconnect: function() {
                    this.stopPolling();
                }
            };

            // Handle page visibility changes (pause polling when page is hidden)
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    NotificationPollingManager.stopPolling();
                } else {
                    // Resume polling when page becomes visible
                    if (!NotificationPollingManager.isPolling) {
                        NotificationPollingManager.fetchCount(false);
                        NotificationPollingManager.startPolling();
                    }
                }
            });

            // Expose globally for debugging
            window.NotificationPollingManager = NotificationPollingManager;

            // Initialize after DOM is ready with a delay to avoid blocking page load
            function initializePolling() {
                // Add 1 second delay to ensure page rendering is complete
                setTimeout(() => {
                    NotificationPollingManager.init();
                }, 1000);
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializePolling);
            } else {
                // DOM is already ready
                initializePolling();
            }

            // Cleanup on page unload
            window.addEventListener('beforeunload', () => {
                NotificationPollingManager.disconnect();
            });

            /* === SSE Implementation (Disabled - Kept for future reference) ===
            const NotificationSSEManager = {
                // SSE code commented out but kept for potential future use
                // To enable SSE: uncomment this code and replace NotificationPollingManager.init() 
                // with NotificationSSEManager.init() in the initialization section above
            };
            */
        })();
    });
</script>