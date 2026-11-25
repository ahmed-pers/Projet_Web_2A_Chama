/* ===== shared by all pages: confirmation before a destructive POST =====
   (no inline onsubmit: the Content-Security-Policy only allows scripts from 'self') */
document.addEventListener("submit", (event) => {
  const message = event.target.getAttribute && event.target.getAttribute("data-confirm");
  if (message && !window.confirm(message)) {
    event.preventDefault();
  }
});

/* ===== report form only ===== */
const form = document.getElementById("report-form");

if (form) {
  const checkbox = document.getElementById("add-description");
  const textarea = document.getElementById("description");
  const addressBtn = document.getElementById("mode-address");
  const coordsBtn = document.getElementById("mode-coords");
  const addressFields = document.getElementById("address-fields");
  const coordsFields = document.getElementById("coords-fields");
  const addressInput = document.getElementById("address");
  const latInput = document.getElementById("latitude");
  const lngInput = document.getElementById("longitude");
  const photoInput = document.getElementById("photo");
  const photoPreview = document.getElementById("photo-preview");
  const categorySelect = document.getElementById("category");

  const errors = {
    photo: document.getElementById("photo-error"),
    location: document.getElementById("location-error"),
    category: document.getElementById("category-error"),
    description: document.getElementById("description-error"),
  };

  const MAX_PHOTO_BYTES = 5 * 1024 * 1024;
  const ALLOWED_TYPES = ["image/jpeg", "image/png", "image/webp"];
  let previewUrl = null;

  /* ---------- error helpers ---------- */
  function showError(key, message) {
    errors[key].textContent = message;
    errors[key].hidden = false;
  }

  function clearError(key) {
    errors[key].textContent = "";
    errors[key].hidden = true;
  }

  function clearAllErrors() {
    Object.keys(errors).forEach(clearError);
  }

  /* ---------- description ---------- */
  // Enable or disable the textarea depending on the checkbox
  function toggleDescription() {
    textarea.disabled = !checkbox.checked;

    if (checkbox.checked) {
      textarea.focus();
    } else {
      textarea.value = ""; // clear the text if the user unchecks
    }
  }

  /* ---------- location mode ---------- */
  // Show the address block or the coordinates block
  function setLocationMode(mode) {
    const useAddress = mode === "address";

    addressFields.hidden = !useAddress;
    coordsFields.hidden = useAddress;

    // disabled inputs are not required and not submitted
    addressInput.disabled = !useAddress;
    latInput.disabled = useAddress;
    lngInput.disabled = useAddress;

    addressBtn.setAttribute("aria-pressed", useAddress);
    coordsBtn.setAttribute("aria-pressed", !useAddress);

    clearError("location");
  }

  /* ---------- photo preview + check ---------- */
  function resetPreview() {
    if (previewUrl) URL.revokeObjectURL(previewUrl);
    previewUrl = null;
    photoPreview.removeAttribute("src");
    photoPreview.hidden = true;
  }

  function checkPhoto() {
    const file = photoInput.files[0];
    if (!file) {
      resetPreview();
      showError("photo", "Please add a photo.");
      return false;
    }
    if (!ALLOWED_TYPES.includes(file.type)) {
      resetPreview();
      showError("photo", "Only JPG, PNG or WebP pictures are accepted.");
      return false;
    }
    if (file.size > MAX_PHOTO_BYTES) {
      resetPreview();
      showError("photo", "The photo is too big (5 MB maximum).");
      return false;
    }
    clearError("photo");
    return true;
  }

  function onPhotoChange() {
    if (!checkPhoto()) return;
    resetPreview();
    previewUrl = URL.createObjectURL(photoInput.files[0]);
    photoPreview.src = previewUrl;
    photoPreview.hidden = false;
  }

  /* ---------- other checks (convenience only: the server validates again) ---------- */
  function checkLocation() {
    if (!addressInput.disabled) {
      if (addressInput.value.trim() === "") {
        showError("location", "Please give an address or coordinates.");
        return false;
      }
    } else {
      const lat = parseFloat(latInput.value);
      const lng = parseFloat(lngInput.value);
      const ok = !isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180;
      if (!ok) {
        showError("location", "Enter a valid latitude (-90 to 90) and longitude (-180 to 180).");
        return false;
      }
    }
    clearError("location");
    return true;
  }

  function checkCategory() {
    if (categorySelect.value === "") {
      showError("category", "Please choose a category.");
      return false;
    }
    clearError("category");
    return true;
  }

  /* ---------- listeners (registered once at load) ---------- */
  checkbox.addEventListener("change", toggleDescription);
  addressBtn.addEventListener("click", () => setLocationMode("address"));
  coordsBtn.addEventListener("click", () => setLocationMode("coordinates"));
  photoInput.addEventListener("change", onPhotoChange);
  categorySelect.addEventListener("change", checkCategory);

  form.addEventListener("submit", (event) => {
    const results = [
      ["photo", checkPhoto()],
      ["location", checkLocation()],
      ["category", checkCategory()],
    ];
    const firstBad = results.find(([, ok]) => !ok);

    if (firstBad) {
      event.preventDefault();
      const target = {
        photo: photoInput,
        location: addressInput.disabled ? latInput : addressInput,
        category: categorySelect,
      }[firstBad[0]];
      target.focus();
    }
  });

  // The Clear button resets the checkbox, so lock the textarea again
  form.addEventListener("reset", () => {
    textarea.disabled = true;
    setLocationMode("address");
    resetPreview();
    clearAllErrors();
  });
}
