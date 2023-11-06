const uploadContainer = document.getElementById("upload-container");
const fileUpload1 = document.getElementById("file-upload1");
const fileUpload2 = document.getElementById("file-upload2");
let counter = 0;

fileUpload1.addEventListener("change", updateFileName1);
fileUpload2.addEventListener("change", updateFileName2);

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
  const files = e.dataTransfer.files;

  if (counter == 0) {
    fileUpload1.files = files;
    updateFileName1();
    counter++;
  } else {
    fileUpload2.files = files;
    updateFileName2();
    counter = 0;
  }
});

function updateFileName1() {
  const fileName = fileUpload1.value.split("\\").pop();
  const fileLabel = document.getElementById("file-name1");
  fileLabel.textContent = fileName ? `File: ${fileName}` : "";
}

function updateFileName2() {
  const fileName = fileUpload2.value.split("\\").pop();
  const fileLabel = document.getElementById("file-name2");
  fileLabel.textContent = fileName ? `File: ${fileName}` : "";
}
