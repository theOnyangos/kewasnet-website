// Global Variables
const mpesaPaymentForm = "#handleMpesaPayment";
const mpesaPaymentUrl = baseUrl + "client/process_mpesa_payment";
const mpesaPaymentButton = $(mpesaPaymentForm).find("button[type=submit]");

const paystackPaymentForm = "#handlePaystackPayment";
const paystackPaymentUrl = baseUrl + "client/process_paystack_payment";
const paystackPaymentButton = $(paystackPaymentForm).find(
  "button[type=submit]"
);

let checkoutRequestId = null;
let pk_payment_reference;

function enableCompleteOrderButton() {
  $("#completeOrderButton")
    .prop("disabled", false)
    .removeClass("bg-gray-500")
    .addClass("bg-gradient-to-r");
}

function payCourseWithPaystack(email, phoneNumber) {}

$(document).ready(function () {
  $("#completeOrderButton")
    .prop("disabled", true)
    .removeClass("bg-gradient-to-r")
    .addClass("bg-gray-500");

  $(mpesaPaymentForm).submit(function (e) {
    e.preventDefault();

    Swal.fire({
      icon: "info",
      text: " Mpesa Payment module is currently unavailable please use paystack instead to complete your purchase.",
    });

    // mpesaPaymentButton.prop("disabled", true).html("Processing...");

    // const formData = $(this).serialize();

    // formData += `&checkoutRequestId=${checkoutRequestId}`;

    // $.post(
    //   mpesaPaymentUrl,
    //   formData,
    //   function (response) {
    //     if (response.status === "success" && response.status_code === 200) {
    //       checkoutRequestId = response.checkoutRequestId;
    //       enableCompleteOrderButton();
    //       toastAlert(response.message, 5000, "success");
    //       showRequestMessage(response.message, 5000);
    //     } else {
    //       mpesaPaymentButton
    //         .prop("disabled", false)
    //         .html("Pay " + currencyFormatter(cartTotal));
    //       toastAlert(response.message, 5000, "error");
    //     }
    //   },
    //   "json"
    // ).done(function () {
    //   mpesaPaymentButton
    //     .prop("disabled", false)
    //     .html("Pay " + currencyFormatter(cartTotal));
    // });
  });

  $(paystackPaymentForm).submit(function (e) {
    e.preventDefault();
    
    paystackPaymentButton.prop("disabled", true).html("Processing...");

    const email = $("#pk_email").val();
    const phone = $("#pk_phone").val();
    const amount = $("#pk_amount").val();

    const url = baseUrl + "client/paystack_settings";

    $.get(url, (response) => {
      const { data, status, status_code, message } = response;
      
      if (status === "success" && status_code === 200) {
        const publicKey = data.public_key;
        
        let handler = PaystackPop.setup({
          key: publicKey,
          email: email,
          amount: amount * 100,
          ref: generateReference(phone),
          currency: "KES",
          onClose: function () {
            alert("Window closed.");
          },
          callback: function (response) {
            pk_payment_reference = response.reference;
            paystackPaymentButton
              .prop("disabled", false)
              .html("Payment Complete");
            enableCompleteOrderButton();
            toastAlert("Payment was successful", 5000, "success");
            $("#completeOrderButton").trigger('click'); 
          },
        });

        handler.openIframe();
      } else {
        console.error(message);
        paystackPaymentButton
          .prop("disabled", false)
          .html("Pay " + currencyFormatter(cartTotal));
      }
    });
  });

  $("#completeOrderButton").click(function () {
    const paymentMethod = $("#paymentMethod").val();
    const paystackPhone = $("#pk_phone").val();
    const button = $(this);
    const data = {
      country_id: $("#mySelectInput").val(),
      payment_reference: pk_payment_reference,
      amount: cartTotal,
      phone:  paystackPhone,
    };

    // console.log(data);

    if (paymentMethod === "mpesa") {
      if (!checkoutRequestId) {
        toastAlert("Please complete the payment to continue", 5000, "error");
        return;
      }
    } else if (paymentMethod === "paystack") {
      if (!pk_payment_reference) {
        toastAlert("Please complete the payment to continue", 5000, "error");
        return;
      }
    }

    button.attr("disabled", true);
    button.html(BUTTON_LOADER + "Completing order please wait...");

    $.post(baseUrl + "client/complete_payment", data, function (response) {
      if (response.status === "success" && response.status_code === 201) {
        toastAlert(response.message, 5000, "success");
        showRequestMessage(response.message, 5000);
        setTimeout(() => {
          window.location.href = response.url;
        }, 3000);
      } else {
        toastAlert(response.message, 5000, "error");
      }
    }).done(function () {
      button.attr("disabled", false);
      button.html("Complete Order");
    });
  });

  $("#paymentMethod").change(function () {
    var selectedOption = $(this).val();

    if (selectedOption === "mpesa") {
      $("#mpesaFields").removeClass("hidden");
      $("#paystackFields").addClass("hidden");
    } else if (selectedOption === "paystack") {
      $("#mpesaFields").addClass("hidden");
      $("#paystackFields").removeClass("hidden");
    } else {
      $("#mpesaFields").addClass("hidden");
      $("#paystackFields").addClass("hidden");
    }
  });

  $.get(baseUrl + "client/get_countries", function (data) {
    const renderHtml = $(".appendResults");

    let html = "";

    data.forEach((country) => {
      html += `<option value="${country.id}" ${
        country.nicename === "Kenya" ? "selected" : ""
      }>${country.nicename}</option>`;
    });

    renderHtml.html(html);
  });

  $("#mySelectInput").select2({
    placeholder: "Select a country",
  });

  $("#paymentMethod").select2({
    placeholder: "Select a payment method",
  });

  getCheckoutCartItems();
  getCheckoutSummary();
});
