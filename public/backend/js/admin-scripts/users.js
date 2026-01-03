const dataTable = "#usersDataTable";
const getUsersUrl = baseUrl + "admin/get_systemUsers";
const usersRegistrationForm = "#handleNewUserRegistration";
const userRegistrationUrl = baseUrl + "admin/register_system_user";
const userRegistrationButton = $(usersRegistrationForm).find(
  "button[type=submit]"
);

function getSystemUsers() {
  table = $(dataTable).DataTable({
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    columnDefs: [{ targets: -1, orderable: false }],
    ajax: {
      url: getUsersUrl,
      type: "GET",
    },
    columns: [
      {
        data: null,
        render: function (data, type, row) {
          let image = data.picture ? data.picture : defaultImage;
          const userName = data.first_name + " " + data.last_name;

          return `<div class="d-flex gap-2">
            <div class="userDatatable__imgWrapper d-flex align-items-center">
                <a href="#" class="profile-image rounded-circle d-block m-0 wh-38" style="background-image:url(${image}); background-size: cover;"></a>
            </div>

            <div class="userDatatable-inline-title">
                <a href="#" class="text-dark fw-500"><h6>${userName}</h6></a>
                <p class="d-block mb-0">${data.email}</p>
            </div>
        </div>`;
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          return `
            <div class="d-flex align-items-center">
                <span class="userDatatable-inline-title">${data.role_name}</span>
            </div>`;
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          return `
            <div class="d-flex align-items-center">
                <i class="fas fa-phone-alt me-2"></i>
                <span class="userDatatable-inline-title text-primary">${data.phone}</span>
            </div>`;
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          const employmentNumber = data.employee_number
            ? data.employee_number
            : "--";
          return `
            <div class="d-flex align-items-center">
                <span>${employmentNumber}</span>
            </div>`;
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          let button;
          let title;

          if (data.account_status === "active") {
            button = "bg-opacity-success color-success";
            title = "Active";
          }

          if (data.account_status === "suspended") {
            button = "bg-opacity-warning color-warning";
            title = "Suspended";
          }

          if (data.account_status === "blocked") {
            button = "bg-opacity-danger color-danger";
            title = "Blocked";
          }

          return `<div class="userDatatable-content d-inline-block">
                <span class="${button} rounded-pill userDatatable-content-status active">${title}</span>
            </div>`;
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          return `
            <div class="d-flex align-items-center">
                <span>${formatDate(data.created_at)}</span>
            </div>`;
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          let button;
          let title;

          if (data.account_status === "active") {
            button = "btn-transparent-success";
            title = "suspend";
          }

          if (data.account_status === "suspended") {
            button = "btn-transparent-warning";
            title = "retrieve";
          }

          if (data.account_status === "blocked") {
            button = "btn-transparent-danger";
            title = "retrieve";
          }

          return (
            '<div class="d-flex align-items-center justify-content-center gap-2">' +
            '<button class="btn ' +
            button +
            ' btn-xs" data-action="suspend"><i class="fas fa-exclamation-circle"></i> ' +
            title +
            " </button>" +
            '<a href="' +
            baseUrl +
            "admin/show_admin_details/" +
            data.id +
            '" class="btn btn-transparent-info btn-xs" data-action="edit"><i class="fas fa-edit"></i> Details</a>' +
            '<button class="btn btn-transparent-danger btn-xs" data-action="delete"><i class="fas fa-trash-alt"></i> Delete </button>' +
            "</div>"
          );
        },
      },
    ],
  });
}

function editUserData(data) {
  const url = baseUrl + "admin/show_admin_details/" + data.id;
  window.location.href = url;
}

function deleteUserAccount(data) {
  console.log(data);
  const url = baseUrl + "admin/delete_system_user/" + data.id;

  Swal.fire({
    title: "Are you sure?",
    text:
      "You are about to delete " +
      data.first_name +
      " " +
      data.last_name +
      "'s details. This action cannot be reversed!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
    showLoaderOnConfirm: true, // Show loader until response
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        text:
          "Deleting " +
          data.first_name +
          " " +
          data.last_name +
          "'s account. Please wait...",
      });
      Swal.showLoading();
      $.post(url, function (response) {
        if (response.status === "success" && response.status_code === 200) {
          Swal.close();
          toastAlert(response.message, 3000, "success");
          table.ajax.reload();
        } else if (
          response.status === "error" &&
          response.status_code === 500
        ) {
          toastAlert(response.message, 3000, "error");
        }
      });
    } else {
      toastAlert("The action has been cancelled", 3000, "error");
    }
  });
}

$(dataTable).on("click", "button", function (e) {
  e.preventDefault();
  const data = table.row($(this).parents("tr")).data();
  const action = $(this).attr("data-action");

  if (action == "edit") {
    editUserData(data);
  }

  if (action == "delete") {
    deleteUserAccount(data);
  }

  if (action == "suspend") {
    suspendUserAccount(data);
  }
});

// Suspend User Account
function suspendUserAccount(data) {
  const url = baseUrl + "admin/suspend_system_user/" + data.id;

  Swal.fire({
    title: "Are you sure?",
    text:
      "You are about to suspend " +
      data.first_name +
      " " +
      data.last_name +
      "'s account. This action cannot be reversed!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText:
      data.account_status === "active"
        ? "Yes, suspend it!"
        : "Yes, retrieve it!",
    showLoaderOnConfirm: true, // Show loader until response
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        text:
          "Suspending " +
          data.first_name +
          " " +
          data.last_name +
          "'s account. Please wait...",
      });
      Swal.showLoading();
      $.post(url, function (response) {
        if (response.status === "success" && response.status_code === 200) {
          Swal.close();
          toastAlert(response.message, 3000, "success");
          table.ajax.reload();
        } else if (
          response.status === "error" &&
          response.status_code === 500
        ) {
          toastAlert(response.message, 3000, "error");
        }
      });
    } else {
      toastAlert("The action has been cancelled", 3000, "error");
    }
  });
}

$(document).ready(function () {
  getSystemUsers();

  // Handle User Registration
  $(usersRegistrationForm).submit(function (event) {
    event.preventDefault();
    const formData = new FormData(this);
    const url = userRegistrationUrl;

    Swal.fire({
      title: "Are you sure?",
      text: "You are about to Create a new user account. The users login credentials will be send to their respective email address. Ensure that the email address provided is valid and accessible by the user.",
      icon: "info",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, send it!",
      showLoaderOnConfirm: true, // Show loader until response
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          text: "Creating account and sending email to user. Please wait...",
        });
        Swal.showLoading();

        $.ajax({
          url: url,
          type: "POST",
          data: formData,
          contentType: false,
          processData: false,
          dataType: "json",
          beforeSend: function () {
            userRegistrationButton.prop("disabled", true);
            userRegistrationButton.html(
              '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Registering...'
            );
          },
          success: function (response) {
            const { status, status_code, message, errors } = response;
            if (status === "success" && status_code === 200) {
              toastAlert(message, 3000, "success");
              $(usersRegistrationForm)[0].reset();
              userRegistrationButton.prop("disabled", false);
              userRegistrationButton.html("Register User");
              table.ajax.reload();
            }

            if (status === "error" && status_code === 400) {
              $.each(errors, function (field, error) {
                const errorDiv = $(
                  '<div class="invalid-feedback">' + error + "</div>"
                );
                $("#" + field)
                  .closest(".form-group")
                  .append(errorDiv);
                $("#" + field).addClass("is-invalid");
              });

              alertError(message);
            }
          },
          error: function (message) {
            toastAlert("An error occurred while processing your request", 5000);
          },
          complete: function () {
            userRegistrationButton.removeAttr("disabled");
            userRegistrationButton.html("Submit Form");
          },
        });
      } else {
        toastAlert("The action has been cancelled", 3000, "error");
      }
    });
  });
});
