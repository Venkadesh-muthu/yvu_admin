<main class="main-content">
  <div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Edit Gallery</h2>
      <a href="<?= base_url('gallery') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    <!-- Validation Errors -->
    <?php if (isset($validation)): ?>
      <div class="alert alert-danger">
        <?= $validation->listErrors() ?>
      </div>
    <?php endif; ?>

    <form action="<?= base_url('gallery/edit/' . $gallery['id']) ?>"
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
               value="<?= set_value('title', $gallery['title']) ?>">
      </div>

      <!-- 🔹 Dates -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-semibold">From Date</label>
          <input type="date"
                 name="from_date"
                 id="from_date"
                 class="form-control"
                 value="<?= set_value('from_date', $gallery['from_date']) ?>"
                 required>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label fw-semibold">To Date</label>
          <input type="date"
                 name="to_date"
                 id="to_date"
                 class="form-control"
                 value="<?= set_value('to_date', $gallery['to_date']) ?>"
                 required>
        </div>
      </div>

      <!-- ============================= -->
      <!-- 🔹 Existing Images -->
      <!-- ============================= -->
      <?php if (!empty($galleryImages)): ?>
        <div class="mb-4">
          <label class="fw-semibold d-block mb-3">Existing Images:</label>

          <?php foreach ($galleryImages as $img): ?>
            <div class="border rounded p-3 mb-3">

              <div class="d-flex justify-content-between align-items-center mb-2">
                <a href="<?= base_url('uploads/gallery/images/' . $img['image']) ?>"
                   target="_blank"
                   class="text-decoration-none text-primary">
                   <?= esc($img['image']) ?>
                </a>

                <a href="<?= base_url('gallery/deleteImage/' . $img['id']) ?>"
                   class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('Delete this image?')">
                   <i class="bi bi-trash"></i>
                </a>
              </div>

              <textarea
                name="existing_descriptions[<?= $img['id'] ?>]"
                class="form-control"
                rows="2"><?= esc($img['image_description']) ?></textarea>

            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- ============================= -->
      <!-- 🔹 Upload New Images -->
      <!-- ============================= -->
      <div class="mb-3">
        <label class="form-label fw-semibold">
          Upload New Images
        </label>
        <input type="file"
               name="gallery_images[]"
               id="gallery_images"
               multiple
               accept="image/*"
               class="form-control">
      </div>

      <!-- Dynamic Description Container -->
      <div id="image-description-container"></div>

      <!-- ============================= -->
      <!-- 🔹 Replace Document -->
      <!-- ============================= -->
      <div class="mb-3">
        <label class="form-label fw-semibold">
          Replace Gallery Document
        </label>
        <input type="file"
               name="gallery_document"
               accept=".pdf,.doc,.docx"
               class="form-control">

        <textarea
          name="document_description"
          class="form-control mt-2"
          rows="2"
          placeholder="Enter document description"><?= esc($gallery['document_description'] ?? '') ?></textarea>
      </div>

      <div class="text-end">
        <button type="submit" class="btn btn-primary">
          Update Gallery
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
    if (fromDate.value) toDate.min = fromDate.value;
    if (toDate.value) fromDate.max = toDate.value;

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

    // ==========================
    // New Image Descriptions
    // ==========================
    if (imageInput) {

        imageInput.addEventListener('change', function () {

            descriptionContainer.innerHTML = '';

            Array.from(this.files).forEach(file => {

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
                        required></textarea>
                `;

                descriptionContainer.appendChild(div);
            });
        });
    }

});
</script>