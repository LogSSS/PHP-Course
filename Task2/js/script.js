const uploadContainer = document.getElementById("upload-container");
const fileUpload = document.getElementById("file-upload");

fileUpload.addEventListener("change", updateFileName);

uploadContainer.addEventListener("dragover", (e) => {
  e.preventDefault();
  uploadContainer.classList.add("active");
});

uploadContainer.addEventListener("dragleave", () => {
  uploadContainer.classList.remove("active");
});

uploadContainer.addEventListener("drop", (e) => {
  e.preventDefault();
  uploadContainer.classList.remove("active");
  const file = e.dataTransfer.files[0];
  fileUpload.files = e.dataTransfer.files;
  updateFileName();
});

function updateFileName() {
  const fileName = fileUpload.value.split("\\").pop();
  const fileLabel = document.querySelector(".file-name");
  fileLabel.textContent = fileName ? `File: ${fileName}` : "";
}
