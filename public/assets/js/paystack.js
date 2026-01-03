const eventId = $('input[name="eventId"]').val();
const getEventAttendanceUrl =
  baseUrl + "client/get_event_attendance/" + eventId;
const renderEventDetails = ".renderEventDetails";

const registerEventForm = "#handleEventRegistration";
const registerEventUrl =
  baseUrl + "client/register_event_attendance/" + eventId;
const registerEventButton = $(registerEventForm).find("button[type=submit]");

let eventData;
let registrationId;
let eventPrice;

function toastAlert(message, duration, icon = "success") {
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
    icon: icon,
    title: message,
  });
}

function handleNavigateBack() {
  window.history.back();
}

function payWithPaystack(email, phoneNumber) {
  const url = baseUrl + "client/paystack_settings";

  $.get(url, (response) => {
    const { data, status, status_code, message } = response;

    if (status === "success" && status_code === 200) {
      const publicKey = data.public_key;

      let handler = PaystackPop.setup({
        key: publicKey,
        email: email,
        amount: eventPrice * 100,
        ref: generateReference(phoneNumber),
        currency: "KES",
        onClose: function () {
          alert("Window closed.");
        },
        callback: function (response) {
          let message =
            "Your payment was successfully processed. Thank you. 🎉";
          storePaymentReference(response.reference);
          toastAlert(message, 5000, "success");
        },
      });

      handler.openIframe();
    } else {
      console.error(message);
    }
  });
}

function storePaymentReference(reference) {
  const url = baseUrl + "client/store_payment_reference";
  const data = {
    reference: reference,
    registration_id: registrationId,
    amount: eventPrice,
    status: 1,
  };

  $.post(url, data, (response) => {
    const { status, status_code, message } = response;

    if (status === "success" && status_code === 200) {
      console.log(message);
    } else {
      console.error(message);
    }
  });
}

$(document).ready(function () {
  $("#toggleRegistrationForm").click(function () {
    $(".hidden").toggle();
    $(this).text("Cancel").toggle();
  });

  $("#openEventRegistrationModal").click(function () {
    const button = $(this);

    button
      .attr("disabled", true)
      .html(BUTTON_LOADER + " " + "Loading please wait...");

    $.get(getEventAttendanceUrl, function (response) {
      if (response.status === "success") {
        button.attr("disabled", false).text("Register Now");
        $(renderEventDetails).html(response.data);
        eventPrice = parseInt(response.event_price);
        $("#eventRegistrationModal").fadeIn("slow");
      }
    })
      .fail(function () {
        button.attr("disabled", false).text("Register Now");
        toastAlert(
          "An error occurred while fetching event details. Please try again later.",
          5000,
          "error"
        );
      })
      .always(function () {
        button.attr("disabled", false).text("Register Now");
      });
  });

  $(".close, .close-modal").click(function () {
    $("#eventRegistrationModal").fadeOut("slow");
    $("#eventPaymentModal").fadeOut("slow");
  });

  $(".modal-content-event").click(function (event) {
    event.stopPropagation();
  });
});

$("#handleEventRegistration").on("submit", function (e) {
  e.preventDefault();

  const form = $(this);
  const formData = form.serialize();
  const registerEventButton = $(this).find("button[type=submit]");
  const registerEventUrl =
    baseUrl + "client/register_event_attendance/" + eventId;

  registerEventButton
    .attr("disabled", true)
    .html(BUTTON_LOADER + " " + "Loading please wait...");

  $.post(registerEventUrl, formData, function (response) {
    const { status, status_code, errors, message, data } = response;

    if (status === "success") {
      toastAlert(message, 5000, "success");
      form.trigger("reset");

      if (data.registration_id) {
        registrationId = data.registration_id;
        $("#eventRegistrationModal").fadeOut("slow");

        payWithPaystack(data.email, data.phone);
      } else {
        $("#eventRegistrationModal").fadeOut("slow");
      }
    }

    if (status === "error" && status_code === 400) {
      $.each(errors, function (field, error) {
        const errorDiv = $(
          '<div class="text-red-500 text-sm">' + error + "</div>"
        );
        form
          .find("#" + field)
          .closest(".form-group")
          .find(".event-input")
          .after(errorDiv);
      });

      toastAlert(message, 5000, "error");
    }

    if (status === "error" && status_code === 422) {
      toastAlert(message, 5000, "error");
    }
  })
    .fail(function () {
      toastAlert(
        "An error occurred while registering for the event. Please try again later.",
        5000,
        "error"
      );
    })
    .always(function () {
      registerEventButton.attr("disabled", false).text("Reserve Spot");
    });
});
