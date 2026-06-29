<main class="main-content">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Add Gallery</h2>
      <a href="<?= base_url('gallery') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    <?php if (isset($validation)): ?>
      <div class="alert alert-danger">
        <?= $validation->listErrors() ?>
      </div>
    <?php endif; ?>

    <form action="<?= base_url('gallery/add') ?>"
          method="post"
          enctype="multipart/form-data"
          class="card shadow-sm p-4">

      <?= csrf_field() ?>

      <!-- 🔹 Gallery Title -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Gallery Title</label>
        <input type="text"
               name="title"
               class="form-control"
               placeholder="Enter gallery title"
               required>
      </div>

      <div class="row">
        <!-- 🔹 From Date -->
        <div class="col-md-6 mb-3">
          <label class="form-label fw-semibold">From Date</label>
          <input type="date"
                 name="from_date"
                 id="from_date"
                 class="form-control"
                 required>
        </div>

        <!-- 🔹 To Date -->
        <div class="col-md-6 mb-3">
          <label class="form-label fw-semibold">To Date</label>
          <input type="date"
                 name="to_date"
                 id="to_date"
                 class="form-control"
                 required>
        </div>
      </div>

      <!-- 🔹 Upload Gallery Images -->
      <div class="mb-3">
        <label class="form-label fw-semibold">
          Upload Gallery Images
        </label>
        <input type="file"
               name="gallery_images[]"
               id="gallery_images"
               multiple
               accept="image/*"
               class="form-control"
               required>
      </div>

      <!-- 🔹 Dynamic Image Description Fields -->
      <div id="image-description-container"></div>

      <!-- 🔹 Upload Gallery Document -->
      <div class="mb-3">
        <label class="form-label fw-semibold">
          Upload Gallery Document
        </label>
        <input type="file"
               name="gallery_document"
               accept=".pdf,.doc,.docx"
               class="form-control">
      </div>

      <!-- 🔹 Document Description -->
      <div class="mb-3">
        <label class="form-label fw-semibold">
          Document Description
        </label>
        <textarea name="document_description"
                  class="form-control"
                  rows="2"
                  placeholder="Enter document description"></textarea>
      </div>

      <div class="text-end">
        <button type="submit" class="btn btn-primary">
          Save Gallery
        </button>
      </div>
    </form>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const fromDate = document.getElementById('from_date');
    const toDate = document.getElementById('to_date');
    const imageInput = document.getElementById('gallery_images');
    const descriptionContainer = document.getElementById('image-description-container');

    // ==========================
    // Date Validation
    // ==========================
    if (fromDate && toDate) {

        fromDate.addEventListener('change', function () {
            toDate.min = this.value;

            if (toDate.value && toDate.value < this.value) {
                toDate.value = this.value;
            }
        });

        toDate.addEventListener('change', function () {
            fromDate.max = this.value;

            if (fromDate.value && fromDate.value > this.value) {
                fromDate.value = this.value;
            }
        });
    }

    // ==========================
    // Dynamic Image Descriptions
    // ==========================
    if (imageInput) {

        imageInput.addEventListener('change', function () {

            descriptionContainer.innerHTML = '';

            Array.from(this.files).forEach((file) => {

                const div = document.createElement('div');
                div.classList.add('mb-3');

                div.innerHTML = `
                    <label class="form-label fw-semibold">
                        Description for ${file.name}
                    </label>
                    <textarea
                        name="image_descriptions[]"
                        class="form-control"
                        rows="2"
                        placeholder="Enter description for ${file.name}"
                        required></textarea>
                `;

                descriptionContainer.appendChild(div);
            });
        });
    }

});
</script>