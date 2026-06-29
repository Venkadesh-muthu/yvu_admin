<main class="main-content">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Edit Visitor</h2>
      <a href="<?= base_url('visitors') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    <?php if (isset($validation)): ?>
      <div class="alert alert-danger">
        <?= $validation->listErrors() ?>
      </div>
    <?php endif; ?>

    <form action="<?= base_url('visitors/edit/' . $visitor['id']) ?>"
          method="post"
          enctype="multipart/form-data"
          class="card shadow-sm p-4">

      <?= csrf_field() ?>

      <!-- Title -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Visitor Title</label>
        <input type="text"
               name="title"
               class="form-control"
               value="<?= set_value('title', $visitor['title']) ?>">
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-semibold">From Date</label>
          <input type="date"
                 name="from_date"
                 id="from_date"
                 class="form-control"
                 value="<?= $visitor['from_date'] ?>"
                 required>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label fw-semibold">To Date</label>
          <input type="date"
                 name="to_date"
                 id="to_date"
                 class="form-control"
                 value="<?= $visitor['to_date'] ?>"
                 required>
        </div>
      </div>

      <!-- Existing Images -->
      <?php if (!empty($visitorImages)): ?>
        <div class="mb-4">
          <label class="fw-semibold d-block mb-3">Existing Images:</label>

          <?php foreach ($visitorImages as $img): ?>
            <div class="border rounded p-3 mb-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <a href="<?= base_url('uploads/visitors/images/' . $img['image']) ?>"
                   target="_blank">
                   <?= esc($img['image']) ?>
                </a>

                <a href="<?= base_url('visitors/deleteImage/' . $img['id']) ?>"
                   class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('Delete this image?')">
                   <i class="bi bi-trash"></i>
                </a>
              </div>

              <textarea name="existing_descriptions[<?= $img['id'] ?>]"
                        class="form-control"
                        rows="2"><?= esc($img['image_description']) ?></textarea>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Upload New Images -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Upload New Images</label>
        <input type="file"
               name="visitor_images[]"
               id="visitor_images"
               multiple
               accept="image/*"
               class="form-control">
      </div>

      <div id="image-description-container"></div>

      <!-- Replace Document -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Replace Visitor Document</label>
        <input type="file"
               name="visitor_document"
               accept=".pdf,.doc,.docx"
               class="form-control">

        <textarea name="document_description"
                  class="form-control mt-2"
                  rows="2"><?= esc($visitor['document_description'] ?? '') ?></textarea>
      </div>

      <div class="text-end">
        <button type="submit" class="btn btn-primary">Update Visitor</button>
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