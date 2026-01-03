$(document).ready(function () {
  const baseUrl = $('meta[name="base-url"]').attr("content");
  const button_loader = `<svg width="20" height="20" fill="currentColor" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                                    </path>
                                </svg>`;

  const $carouselInner = $(".carousel-inner");
  const $prevBtn = $("#prevBtn");
  const $nextBtn = $("#nextBtn");
  const totalImages = 6; // Replace with the actual number of images

  let currentIndex = 0;

  // Function to update the carousel display
  const updateCarousel = () => {
    const translateValue = -currentIndex * 100 + "%";
    $carouselInner.css("transform", "translateX(" + translateValue + ")");
  };

  // Event listener for the previous button
  $prevBtn.on("click", function () {
    currentIndex = (currentIndex - 1 + totalImages) % totalImages;
    updateCarousel();
  });

  // Event listener for the next button
  $nextBtn.on("click", function () {
    currentIndex = (currentIndex + 1) % totalImages;
    updateCarousel();
  });

  // Initialize the carousel
  updateCarousel();

  // Collapsable Content
  $(".collapsible-content").hide();

  // Toggle the collapsible content on button click
  $(".toggle-btn").on("click", function () {
    var target = $(this).data("target");
    $(target).slideToggle();

    var chevronIcon = $(this).find(".chevron-icon");

    if (chevronIcon.attr("name") === "chevron-up-outline") {
      chevronIcon.attr("name", "chevron-down-outline");
    } else {
      chevronIcon.attr("name", "chevron-up-outline");
    }
  });

  // SweetAlert2 Toast Alert
  function toastAlert(message, duration) {
    const Toast = Swal.mixin({
      toast: true,
      position: "top-end",
      showConfirmButton: false,
      timer: duration,
      iconColor: "#27aae0",
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener("mouseenter", Swal.stopTimer);
        toast.addEventListener("mouseleave", Swal.resumeTimer);
      },
    });
    Toast.fire({
      icon: "success",
      title: message,
    });
  }

  var navbar = document.getElementById("navbar");
  var stickyOffset = 20;

  if (navbar) {
    stickNavbar();
  }

  function stickNavbar() {
    if (window.pageYOffset >= stickyOffset) {
      navbar.classList.add("sticky-nav");
    } else {
      navbar.classList.remove("sticky-nav");
    }
  }
  window.onscroll = function () {
    stickNavbar();
  };

  // Back to top button
  var backToTopButton = document.querySelector(".back-to-top");
  window.addEventListener("scroll", scrollFunction);
  function scrollFunction() {
    if (window.pageYOffset > 300) {
      // Show back to top button
      if (!backToTopButton.classList.contains("btnEntrance")) {
        backToTopButton.classList.remove("btnExit");
        backToTopButton.classList.add("btnEntrance");
        backToTopButton.style.display = "block";
      }
    } else {
      // Hide back to top button
      if (backToTopButton.classList.contains("btnEntrance")) {
        backToTopButton.classList.remove("btnEntrance");
        backToTopButton.classList.add("btnExit");
        setTimeout(function () {
          backToTopButton.style.display = "none";
        }, 250);
      }
    }
  }

  backToTopButton.addEventListener("click", smoothScrollBackToTop);
  function smoothScrollBackToTop() {
    var targetPosition = 0;
    var startPosition = window.pageYOffset;
    var distance = targetPosition - startPosition;
    var duration = 750;
    var start = null;
    window.requestAnimationFrame(step);
    function step(timestamp) {
      if (!start) start = timestamp;
      var progress = timestamp - start;
      window.scrollTo(
        0,
        easeInOutCubic(progress, startPosition, distance, duration)
      );
      if (progress < duration) window.requestAnimationFrame(step);
    }
  }

  function easeInOutCubic(t, b, c, d) {
    t /= d / 2;
    if (t < 1) return (c / 2) * t * t * t + b;
    t -= 2;
    return (c / 2) * (t * t * t + 2) + b;
  }

  //====================== Custom Notifications =============================
  // This function shows the request messages
  function showRequestMessage(message, duration, icon = "success") {
    const requestMessage = document.querySelector(".error-message");
    requestMessage.style.display = "block";
    requestMessage.innerHTML = `
            <p class="text-${
              icon === "error" ? "red" : "green"
            }-600 text-sm font-bold mb-2 border-[0.9px] border-${
      icon === "error" ? "red" : "green"
    }-500 p-3 rounded-md bg-${
      icon === "error" ? "red" : "green"
    }-100 flex justify-start items-center gap-2">
                <span class="w-[30px]"><ion-icon name="${
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
    setTimeout(() => {
      // $(`.cancel-${icon === "error" ? "error" : "success"}-button`).click();
      $(".error-message").fadeOut("slow");
    }, duration);
  }

  //====================== Chat popup =============================
  var chatWindowVisible = false;

  function showChatWindow() {
    $("#chat-window").removeClass("hidden");
    $("#chat-window").css({ opacity: 1, bottom: 0 });
    gsap.from("#chat-window", {
      duration: 0.5,
      y: 100,
      opacity: 0,
      ease: "power4.out",
    });
    chatWindowVisible = true;
  }

  $("#generalEnquiry").select2({
    placeholder: "Select an enquiry...",
  });

  $("#responseType").select2({
    placeholder: "Select your preferred response type...",
  });

  function hideChatWindow() {
    $("#chat-window").addClass("hidden");
    chatWindowVisible = false;
  }

  $("#chat-button").click(function () {
    if (!chatWindowVisible) {
      showChatWindow();
      $("#chat-button").addClass("hidden");
    } else {
      hideChatWindow();
      $("#chat-button").removeClass("hidden");
    }
  });

  // Close chat window when the close button is clicked
  $("#close-chat").click(function () {
    hideChatWindow();
  });

  // Send message functionality
  $("#handleChatMessageEnquiry").on("submit", function (e) {
    e.preventDefault();
    const form = $(this);
    const url = form.attr("action");
    const formData = form.serialize();
    const button = form.find("button[type=submit]");

    // Disable the button to prevent multiple clicks
    button.attr("disabled", true);
    button.html(button_loader + "Sending...");

    $.post(url, formData, function (data) {
      if (data.status === "success") {
        showRequestMessage(data.message, 5000);
        toastAlert(data.message, 3000);

        // Close chat window after sending message
        // hideChatWindow();
      }

      if (
        (data.status === "error" && data.status_code === 500) ||
        data.status_code === 400 ||
        data.status_code === 401 ||
        data.status_code === 405
      ) {
        showRequestMessage(data.message, 5000, "error");
      }
    }).done(function (data) {
      // Clear the form fields
      form.trigger("reset");

      // Enable the button
      button.attr("disabled", false);
      button.html("Send");
    });
  });

  // Enable WhatsApp click to Chat 
  var wa_btnSetting = {
    btnColor: "#24d366",
    ctaText: "Chat With Us",
    cornerRadius: 40,
    marginBottom: 20,
    marginLeft: 20,
    marginRight: 20,
    paddingLeft: 20,
    paddingRight: 20,
    btnPosition: "left",
    whatsAppNumber: "+254705530499",
    welcomeMessage:
      "👋 Welcome to KEWASNET Customer Care! 👋\n\n Hello and thank you for reaching out to KEWASNET! 🌟 We're delighted to assist you.\n\n ℹ️ For inquiries about our services, water projects, or any assistance you may need, feel free to ask. Our team is here to provide you with the information and support you're looking for.\n\n 🔍 To help us better assist you, please provide details about your inquiry. We'll do our best to respond promptly.\n\n 🕒 Our office hours are 9:00am - 4:00pm, Monday to Friday. If you message us outside these hours, we'll get back to you as soon as we're back online.\n\n 🌐 Visit our website for more information: https://kewasnet.co.ke\n\n 📞 If you prefer to speak with us directly, you can reach our customer care hotline at +254 (705)- 530-499.\n\n Thank you for choosing KEWASNET! We look forward to assisting you. 🚰💙",
    welcomeMessage:
      "👋 Welcome to KEWASNET Customer Care! 👋\n\n" +
      "Hello and thank you for reaching out to KEWASNET! 🌟 We're delighted to assist you.\n\n" +
      "ℹ️ For inquiries about our services, water projects, or any assistance you may need, feel free to ask. Our team is here to provide you with the information and support you're looking for.\n\n" +
      "🔍 To help us better assist you, please provide details about your inquiry. We'll do our best to respond promptly.\n\n" +
      "🕒 Our office hours are 9:00am - 4:00pm, Monday to Friday. If you message us outside these hours, we'll get back to you as soon as we're back online.\n\n" +
      "🌐 Visit our website for more information: https://kewasnet.co.ke\n\n" +
      "📞 If you prefer to speak with us directly, you can reach our customer care hotline at +254 (705)- 530-499.\n\n" +
      "Thank you for choosing KEWASNET! We look forward to assisting you. 🚰💙",
    zIndex: 999,
    btnColorScheme: "light",
  };

  window.onload = () => {
    _waEmbed(wa_btnSetting);
  };

  // Close modal when close button or overlay is clicked
  $(".close, .modal").click(function () {
    $("#cartModal").fadeOut("slow");
  });

  // Prevent modal from closing when clicking on the modal content
  $(".modal-content").click(function (event) {
    event.stopPropagation();
  });

  // Get cart count
  countCartItems();

  // Get checkout summary
  getCheckoutSummary();
});

// Currency formatter function
function currencyFormatter(amount) {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "KES",
  }).format(amount);
}

// Count cart items
function countCartItems() {
  const cartItems = $("#cartItemsCount");
  const url = baseUrl + "client/get_cart_count";

  $.get(url, function (response) {
    if (response.status === "success" && response.status_code === 200) {
      $(cartItems).text(response.data);
    }
  });
}

// Handle cart change
function handleCartChange(count) {
  if (count === 0) {
    $("#proceedToCheckout")
      .attr("disabled", true)
      .removeClass("bg-gradient-to-r")
      .addClass("bg-gray-500");
    $("#proceedToCheckout").attr("href", "javascript:;");
  } else {
    $("#proceedToCheckout")
      .attr("disabled", false)
      .removeClass("bg-gray-500")
      .addClass("bg-gradient-to-r");
    $("#proceedToCheckout").attr("href", baseUrl + "client/checkout");
  }
}

// Get cart items
$(document).on("click", "#openCart", function () {
  const url = baseUrl + "client/get_cart_items";

  $.get(url, function (response) {
    if (response.status === "success" && response.status_code === 200) {
      $("#renderCartItems").html(response.data);
      $("#cartModal").fadeIn("slow");

      // Disable the proceed to checkout button
      const cartItemsCount = response.count;
      handleCartChange(cartItemsCount);
    }
  });
});

// Remove item from cart
function handleRemoveFromCart($itemId) {
  const url = baseUrl + "client/remove_from_cart/" + $itemId;
  console.log(url);

  $.post(url, { itemId: $itemId }, function (response) {
    if (response.status === "success" && response.status_code === 200) {
      const cartItemsCount = response.count;
      handleCartChange(cartItemsCount);
      $("#renderCartItems").html(response.data);
      countCartItems();
      getCheckoutCartItems();
      getCheckoutSummary();
    }
  });
}

// Get checkout cart items
function getCheckoutCartItems() {
  const url = baseUrl + "client/get_checkout_cart_items";

  $("#renderCheckoutCartItems")
    .html(`<div class="h-[200px] w-full flex justify-center items-center gap-3 bg-white/50 rounded-md">
                        ${BUTTON_LOADER}
                        <p class="text-center text-gray-500 roboto">Loading cart items...</p>
                    </div>`);

  $.get(url, function (response) {
    if (response.status === "success" && response.status_code === 200) {
      $("#renderCheckoutCartItems").html(response.data);
    }
  });
}

let cartTotal;

// Get checkout count
function getCheckoutSummary() {
  const url = baseUrl + "client/get_checkout_summary";

  $("#mpesaButton").html(BUTTON_LOADER);
  $("#paystackButton").html(BUTTON_LOADER);
  $("#subtotalSummary").html(BUTTON_LOADER);
  $("#discountSummary").html(BUTTON_LOADER);
  $("#totalSummary").html(BUTTON_LOADER);

  $.get(url, function (response) {
    if (response.status === "success" && response.status_code === 200) {
      const checkoutSummary = response.data;
      cartTotal = checkoutSummary.total_price;
      
      console.log(checkoutSummary)

      $("#paystackAmount").val(checkoutSummary.total_price);
      $("#mpesaAmount").val(checkoutSummary.total_price);
      $("#pk_amount").val(checkoutSummary.total_price);

      $("#mpesaButton").html(
        "Pay Ksh. " + currencyFormatter(checkoutSummary.total_price)
      );
      $("#paystackButton").html(
        "Pay Ksh. " + currencyFormatter(checkoutSummary.total_price)
      );
      $("#subtotalSummary").html(currencyFormatter(checkoutSummary.subtotals));
      $("#discountSummary").html(
        currencyFormatter(checkoutSummary.total_discount)
      );
      $("#totalSummary").html(currencyFormatter(checkoutSummary.total_price));
    }
  });
}

// Function to open share dialog with generated link
function shareOnSocialMedia(platform) {
  var urlToShare = encodeURIComponent(window.location.href);
  switch (platform) {
    case "facebook":
      window.open(
        "https://www.facebook.com/sharer/sharer.php?u=" + urlToShare,
        "_blank"
      );
      break;
    case "twitter":
      window.open(
        "https://twitter.com/intent/tweet?url=" + urlToShare,
        "_blank"
      );
      break;
    case "whatsapp":
      window.open("https://api.whatsapp.com/send?text=" + urlToShare, "_blank");
      break;
    case "linkedin":
      window.open(
        "https://www.linkedin.com/sharing/share-offsite/?url=" + urlToShare,
        "_blank"
      );
      break;
    case "telegram":
      window.open("https://t.me/share/url?url=" + urlToShare, "_blank");
      break;
    case "email":
      window.open(
        "mailto:?subject=Check out this link&body=" + urlToShare,
        "_blank"
      );
      break;
    case "instagram":
      window.open(
        "https://www.instagram.com/sharer/sharer.php?u=" + urlToShare,
        "_blank"
      );
      break;
    case "youtube":
      window.open(
        "https://www.youtube.com/sharer/sharer.php?u=" + urlToShare,
        "_blank"
      );
      break;
    default:
      break;
  }

  return false;
}

// Function to copy link to clipboard
function copyLinkToClipboard() {
  var urlToCopy = window.location.href;
  var tempInput = document.createElement("input");
  tempInput.value = urlToCopy;
  document.body.appendChild(tempInput);
  tempInput.select();
  document.execCommand("copy");
  document.body.removeChild(tempInput);
  toastAlert("Link copied to clipboard", 3000);
}

const facebook = document.getElementById("facebook");

if (facebook) {
  facebook.addEventListener("click", function (event) {
    event.preventDefault();
    shareOnSocialMedia("facebook");
  });
}

const twitter = document.getElementById("twitter");

if (twitter) {
  twitter.addEventListener("click", function (event) {
    event.preventDefault();
    shareOnSocialMedia("twitter");
  });
}

const whatsapp = document.getElementById("whatsapp");

if (whatsapp) {
  whatsapp.addEventListener("click", function (event) {
    event.preventDefault();
    shareOnSocialMedia("whatsapp");
  });
}

const linkedin = document.getElementById("linkedin");

if (linkedin) {
  linkedin.addEventListener("click", function (event) {
    event.preventDefault();
    shareOnSocialMedia("linkedin");
  });
}

const email = document.getElementById("shareEmail");

if (email) {
  email.addEventListener("click", function (event) {
    event.preventDefault();
    shareOnSocialMedia("email");
  });
}

const instagram = document.getElementById("instagram");

if (instagram) {
  instagram.addEventListener("click", function (event) {
    event.preventDefault();
    shareOnSocialMedia("instagram");
  });
}

const youtube = document.getElementById("youtube");

if (youtube) {
  youtube.addEventListener("click", function (event) {
    event.preventDefault();
    shareOnSocialMedia("youtube");
  });
}

const copyLink = document.getElementById("copyLink");

if (copyLink) {
  copyLink.addEventListener("click", function (event) {
    event.preventDefault();
    copyLinkToClipboard();
  });
}

function toUnicode(str) {
  return str.replace(/[\u00A0-\u9999<>\&]/gim, function (i) {
    return "&#" + i.charCodeAt(0) + ";";
  });
}
