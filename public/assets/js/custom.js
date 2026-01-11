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
  lucide.createIcons();

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

// Comprehensive Toast Notification Function
function showToast(message, type = "success") {
  // Remove existing toasts
  $(".custom-toast").remove();

  let bgColor, iconName;
  switch (type) {
    case "success":
      bgColor = "bg-green-500";
      iconName = "check-circle";
      break;
    case "error":
      bgColor = "bg-red-500";
      iconName = "alert-circle";
      break;
    case "warning":
      bgColor = "bg-yellow-500";
      iconName = "alert-triangle";
      break;
    case "info":
      bgColor = "bg-blue-500";
      iconName = "info";
      break;
    default:
      bgColor = "bg-gray-500";
      iconName = "bell";
  }

  // Create toast element
  const toast = $(`
        <div class="custom-toast fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg text-white z-50 ${bgColor} max-w-sm">
            <div class="flex items-center">
                <i data-lucide="${iconName}" class="w-5 h-5 mr-2 flex-shrink-0"></i>
                <span class="text-sm">${message}</span>
            </div>
        </div>
    `);

  // Append to body and show
  $("body").append(toast);
  lucide.createIcons();
  toast.hide().fadeIn(300);

  // Auto-remove after delay
  setTimeout(
    () => toast.fadeOut(300, () => toast.remove()),
    type === "error" ? 5000 : 3000
  );
}

// Utility functions
function formatFileSize(bytes) {
  if (bytes === 0) return "0 Bytes";
  const k = 1024;
  const sizes = ["Bytes", "KB", "MB", "GB"];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
}

// Format Date
function formatDate(dateString) {
  const options = { year: "numeric", month: "long", day: "numeric" };
  return new Date(dateString).toLocaleDateString(undefined, options);
}

// Function to initialize Lucide icons
function initializeLucideIcons() {
  if (typeof lucide !== "undefined") {
    // First, remove any existing icons to avoid duplicates
    const existingIcons = document.querySelectorAll("[data-lucide]");
    existingIcons.forEach((icon) => {
      if (icon._lucideSvg) {
        icon._lucideSvg.remove();
        delete icon._lucideSvg;
      }
    });

    // Initialize new icons
    lucide.createIcons();
  } else {
    console.warn("Lucide is not available");
    // Fallback: try to initialize after a delay
    setTimeout(initializeLucideIcons, 100);
  }
}

// Function to show form errors
function showFormErrors(errors) {
    clearFormErrors();
    
    $.each(errors, function(fieldName, message) {
        const $field = $('[name="' + fieldName + '"]');
        
        if ($field.length) {
            $field.addClass('is-invalid border-red-500');
            
            const $errorDiv = $('<div>', {
                class: 'invalid-feedback text-red-500 text-xs mt-1',
                text: message
            });
            
            $field.after($errorDiv);
            
            // Scroll to first error
            if ($('.is-invalid').first().is($field)) {
                $field.focus();
                $('html, body').animate({
                    scrollTop: $field.offset().top - 100
                }, 500);
            }
        }
    });
}

// Function to clear form errors
function clearFormErrors() {
    $('.is-invalid').removeClass('is-invalid border-red-500');
    $('.invalid-feedback').remove();
}

$(document).ready(function () {
  // Initialize Select2 only if the library is loaded
  if (typeof $.fn.select2 !== 'undefined') {
    $(".select2").each(function() {
      $(this).select2({
        theme: "bootstrap-5",
        width: $(this).data("width")
          ? $(this).data("width")
          : $(this).hasClass("w-100")
          ? "100%"
          : "style",
        placeholder: $(this).data("placeholder"),
        minimumResultsForSearch: Infinity,
      });
    });
  }

  // Initialize datepicker if jQuery UI is loaded
  if (typeof $.fn.datepicker !== 'undefined') {
    $(function () {
      $("#datepicker").datepicker();
    });
  }
});
