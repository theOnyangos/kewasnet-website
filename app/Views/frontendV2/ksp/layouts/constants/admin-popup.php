<!-- Admin Floating Menu -->
<?php if(session()->get('isAdmin')): ?>
<div id="adminFloatingMenu">
    <!-- Main Floating Button -->
    <button id="adminMenuButton">
        <i data-lucide="shield"></i>
        
        <!-- Ping Animation -->
        <span class="ping-animation">
            <span class="ping-circle"></span>
            <span class="ping-dot"></span>
        </span>
    </button>
    
    <!-- Menu Panel (Hidden by default) -->
    <div id="adminMenuPanel">
        <!-- Header -->
        <div class="menu-header">
            <i data-lucide="shield"></i>
            <h3>Admin Mode Active</h3>
        </div>
        
        <!-- Content -->
        <div class="menu-content">
            <p>You are currently using the application with administrator privileges.</p>
            
            <div class="warning-banner">
                <i data-lucide="alert-triangle"></i>
                <span>Actions may affect all users. Please proceed with caution.</span>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="menu-footer">
            <button onClick="location.href='/auth/dashboard'" id="goToAdminBtn" class="admin-link">
                Go to Admin Dashboard
            </button>
        </div>
    </div>
</div>

<style>
    #adminFloatingMenu {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1000;
    }

    #adminMenuButton {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(79, 70, 229, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        transition: all 0.3s ease;
    }

    #adminMenuButton:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 25px rgba(79, 70, 229, 0.4);
    }

    #adminMenuButton i {
        width: 24px;
        height: 24px;
    }

    .ping-animation {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .ping-circle {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background-color: rgba(79, 70, 229, 0.4);
        animation: ping 2s infinite;
    }

    .ping-dot {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: white;
        transform: translate(-50%, -50%);
        animation: ping 2s cubic-bezier(0,0,0.2,1) infinite;
    }

    @keyframes ping {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        75%, 100% {
            transform: scale(2);
            opacity: 0;
        }
    }

    #adminMenuPanel {
        position: absolute;
        bottom: 70px;
        right: 0;
        width: 300px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px);
        transition: all 0.3s ease;
    }

    #adminMenuPanel.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .menu-header {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
        padding: 20px;
        border-radius: 12px 12px 0 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .menu-header i {
        width: 24px;
        height: 24px;
    }

    .menu-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .menu-content {
        padding: 20px;
    }

    .menu-content p {
        margin: 0 0 15px 0;
        color: #4b5563;
        line-height: 1.5;
    }

    .warning-banner {
        background-color: #fef3c7;
        border: 1px solid #f59e0b;
        border-radius: 8px;
        padding: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #92400e;
    }

    .warning-banner i {
        width: 18px;
        height: 18px;
        color: #f59e0b;
    }

    .menu-footer {
        padding: 0 20px 20px;
    }

    .admin-link {
        width: 100%;
        padding: 12px 20px;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .admin-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }
</style>

<?= $this->section('scripts') ?>
<script>
// Admin Floating Menu Script
document.addEventListener('DOMContentLoaded', function() {
    var menuPanel = document.getElementById('adminMenuPanel');
    var menuButton = document.getElementById('adminMenuButton');

    // Show on page load, then hide after 3 seconds
    if (menuPanel) {
        menuPanel.classList.add('active');
        setTimeout(function() {
            menuPanel.classList.remove('active');
        }, 3000);
    }

    // Toggle on button click
    if (menuButton && menuPanel) {
        menuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            menuPanel.classList.toggle('active');
        });

        // Hide if clicking outside
        document.addEventListener('click', function(e) {
            if (menuPanel.classList.contains('active') && 
                !menuPanel.contains(e.target) && 
                !menuButton.contains(e.target)) {
                menuPanel.classList.remove('active');
            }
        });
    }
    
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
<?= $this->endSection() ?>
<?php endif; ?>