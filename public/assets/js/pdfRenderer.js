const container = $("#pdf-viewer");
const prevButton = $("#prev-page");
const nextButton = $("#next-page");
const pageNumSpan = $("#page-num");
const pageCountSpan = $("#page-count");
const downloadLink = $("#download-link");

$(document).ready(function () {
  // Get all the resources
  getCompanyResources();
});

// SweetAlert2 Toast Alert
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

$(".close").click(function () {
  $("#resourcePDFModal").fadeOut("slow");
});

$(".pdf-modal-content", ".close").click(function (event) {
  event.stopPropagation();
});

pdfjsLib.GlobalWorkerOptions.workerSrc =
  "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.worker.min.js";

let pdfDoc = null;
let pageNum = 1;
let pageCount = 0;
let pdfId;
let resourceUrl;
let downloadFileName;

function renderPage(pageNumber) {
  pdfDoc.getPage(pageNumber).then((page) => {
    const viewport = page.getViewport({ scale: 1.5 });
    const canvas = document.querySelector("#pdf-viewer");
    const context = canvas.getContext("2d");

    canvas.height = viewport.height;
    canvas.width = viewport.width;

    container.html("");
    container.append(canvas);

    page.render({
      canvasContext: context,
      viewport: viewport,
    });

    pageNumSpan.text(pageNumber);
  });
}

function loadPDF(url) {
  return pdfjsLib.getDocument(url).promise.then((doc) => {
    pdfDoc = doc;
    pageCount = pdfDoc.numPages;
    pageCountSpan.text(pageCount);
    renderPage(pageNum);
  });
}

prevButton.on("click", () => {
  if (pageNum <= 1) return;
  pageNum--;
  renderPage(pageNum);
});

nextButton.on("click", () => {
  if (pageNum >= pageCount) return;
  pageNum++;
  renderPage(pageNum);
});

downloadLink.on("click", function handleClick(event) {
  downloadLink.off("click", handleClick);

  if (!pdfDoc) {
    downloadLink.on("click", handleClick);
    return;
  }

  $.ajax({
    url: registerCountUrl,
    type: "POST",
    contentType: "application/json",
    data: JSON.stringify({ resource_id: pdfId }),
    success: function (response) {
      if (response.status === "success" && response.status_code === 200) {
        downloadLink
          .attr("href", resourceUrl)
          .attr("download", downloadFileName + ".pdf");
        downloadLink[0].click();
        toastAlert(response.message, 3000, "success");
      } else {
        console.error("Failed to update download count");
        toastAlert(
          "Something went wrong, please try again later",
          3000,
          "error"
        );
      }
    },
    error: function (xhr, status, error) {
      console.error("Error updating download count:", error);
      toastAlert("Something went wrong, please try again later", 3000, "error");
    },
    complete: function () {
      // Reattach the click event handler
      downloadLink.on("click", handleClick);
    },
  });
  // Prevent the default click action
  event.preventDefault();
});

// This function gets all the resources
const getCompanyResources = () => {
  const resourcesUrl = baseUrl + "client/get_resources";
  $.get(resourcesUrl, (data) => {
    if (data.status === "success" && data.status_code === 200) {
      const resources = data.data;
      $("#renderResources").append(resources);
    }
  });
};

// Open event registration modal
function handleDownloadResource(button, resourceData) {
  pdfId = resourceData.id;
  resourceUrl = resourceData.file_path;
  downloadFileName = resourceData.file_name;

  $(button).html(BUTTON_LOADER + " loading...");

  loadPDF(resourceData.file_path)
    .then(() => {
      $(button).html("Download");
      $("#resourcePDFModal").fadeIn("slow");
    })
    .catch((err) => {
      console.error("Error loading PDF:", err);
    });
}
