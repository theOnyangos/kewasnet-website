<!--  JQuery Script -->
<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<script>
    // Initialize Lucide icons when DOM is ready
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Custom Notifications Function
    function showNotification(type, message) {
    // Create notification element
    const notification = document.createElement("div");
    notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full bg-white border-l-4 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
        type === "success" ? "border-green-500" : "border-red-500"
    }`;

    notification.innerHTML = `
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="${
                    type === "success" ? "check-circle" : "x-circle"
                    }" class="w-5 h-5 ${
        type === "success" ? "text-green-500" : "text-red-500"
    }"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900">${message}</p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button type="button" class="inline-flex text-gray-400 hover:text-gray-600" onclick="this.closest('.fixed').remove()">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        `;

    document.body.appendChild(notification);
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Animate in
    setTimeout(() => {
        notification.classList.remove("translate-x-full");
    }, 100);

    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.add("translate-x-full");
        setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
        }, 300);
    }, 5000);
    }

    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            minimumResultsForSearch: Infinity,
        });
    });

    // Base URL for AJAX requests
    const baseUrl = $('meta[name="base-url"]').attr("content");
    const BUTTON_LOADER = `<svg width="20" height="20" fill="currentColor" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
        <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
        </path>
    </svg>`;

    function showRequestMessage(message, duration, icon = "success") {
        const requestMessage = document.querySelector(".error-message");
        requestMessage.innerHTML = `
                <p class="text-${
                    icon === "error" ? "red" : "green"
                }-600 text-sm font-bold mb-2 border-[0.9px] border-${
        icon === "error" ? "red" : "green"
        }-500 p-3 rounded-md bg-${
        icon === "error" ? "red" : "green"
        }-100 flex justify-start items-center gap-2">
                    <span class="w-[50px]"><ion-icon name="${
                        icon === "error"
                        ? "alert-circle-outline"
                        : "checkmark-circle-outline"
                    }" class="text-[28px]"></ion-icon></span>
                    ${message}
                </p>
                <button class="text-${
                    icon === "error" ? "red" : "green"
                }-600 cancel-${
        icon === "error" ? "error" : "success"
        }-button" id="cancel${icon === "error" ? "Error" : "Success"}">
                    <ion-icon name="close-circle-outline" class="text-[28px]"></ion-icon>
                </button>
            `;
    }

    // Custom Notifications Function
    function showNotification(type, message) {
        // Create notification element
        const notification = document.createElement("div");
        notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full bg-white border-l-4 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
            type === "success" ? "border-green-400" : "border-red-500"
        }`;

        notification.innerHTML = `
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i data-lucide="${
                        type === "success" ? "check-circle" : "x-circle"
                        }" class="w-5 h-5 ${
            type === "success" ? "text-green-400" : "text-red-500"
        }"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900">${message}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <button type="button" class="inline-flex text-gray-400 hover:text-gray-600" onclick="this.closest('.fixed').remove()">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            `;

        document.body.appendChild(notification);
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Animate in
        setTimeout(() => {
            notification.classList.remove("translate-x-full");
        }, 100);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add("translate-x-full");
            setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
            }, 300);
        }, 5000);
    }

    function generateReference(userPhoneNumber) {
        const date = new Date();
        const day = String(date.getDate()).padStart(2, "0");
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const year = date.getFullYear();
        const formattedDate = day + month + year;

        const random = generateRandomDigits(6);

        const phoneNumber = userPhoneNumber.replace(/\D/g, "").substring(1);

        const reference = "REF_" + formattedDate + random + phoneNumber;

        return reference;
    }

    function generateRandomDigits(length) {
        let digits = "";
        for (let i = 0; i < length; i++) {
        digits += Math.floor(Math.random() * 10);
        }
        return digits;
    }

    // ScrollTrigger animations for other sections
    gsap.registerPlugin(ScrollTrigger);

    // Mobile menu toggle
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        const overlay = document.getElementById('mobile-menu-overlay');
        menu.classList.toggle('mobile-menu-open');
        overlay.classList.toggle('overlay-visible');
    }

    // GSAP Animations
    document.addEventListener('DOMContentLoaded', () => {
        // Sticky header effect
        const header = document.querySelector('header');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('shadow-lg', 'py-2');
                header.classList.remove('py-3');
            } else {
                header.classList.remove('shadow-lg', 'py-2');
                header.classList.add('py-3');
            }
        });

        // Water button effect
        const waterButtons = document.querySelectorAll('.water-btn');
        waterButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                let x = e.clientX - e.target.getBoundingClientRect().left;
                let y = e.clientY - e.target.getBoundingClientRect().top;
                
                let ripple = document.createElement('span');
                ripple.style.left = `${x}px`;
                ripple.style.top = `${y}px`;
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 1000);
            });
        });
    });

    document.querySelectorAll('section').forEach((section, index) => {
        if (index > 0) { // Skip hero section
            gsap.from(section, {
                scrollTrigger: {
                    trigger: section,
                    start: 'top 80%',
                    toggleActions: 'play none none none'
                },
                y: 50,
                opacity: 0,
                duration: 1,
                ease: 'power3.out'
            });
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        // Initialize icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        
        // Back to Top Button
        const backToTopBtn = document.getElementById('back-to-top');
        
        // Show/hide back to top button based on scroll position
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                backToTopBtn.classList.remove('hidden');
                gsap.to(backToTopBtn, { opacity: 1, duration: 0.3 });
            } else {
                gsap.to(backToTopBtn, { 
                    opacity: 0, 
                    duration: 0.3,
                    onComplete: () => backToTopBtn.classList.add('hidden')
                });
            }
        });
        
        // Smooth scroll to top
        backToTopBtn.addEventListener('click', (e) => {
            e.preventDefault();
            gsap.to(window, { 
                scrollTo: 0, 
                duration: 0.8, 
                ease: "power2.inOut"
            });
        });
        
        // WhatsApp Button Animation
        const whatsappBtn = document.getElementById('whatsapp-chat');
        
        // Pulse animation
        gsap.to(whatsappBtn, {
            y: -5,
            duration: 1.5,
            repeat: -1,
            yoyo: true,
            ease: "sine.inOut"
        });
        
        // Show WhatsApp button after delay
        setTimeout(() => {
            whatsappBtn.classList.remove('hidden');
            gsap.from(whatsappBtn, {
                y: 50,
                opacity: 0,
                duration: 0.5,
                ease: "back.out(1.2)"
            });
        }, 3000); // Appears after 3 seconds
    });
</script>