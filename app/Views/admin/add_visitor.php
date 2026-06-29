<main class="main-content">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Add Visitor</h2>
      <a href="<?= base_url('visitors') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    <?php if (isset($validation)): ?>
      <div class="alert alert-danger">
        <?= $validation->listErrors() ?>
      </div>
    <?php endif; ?>

    <form action="<?= base_url('visitors/add') ?>"
          method="post"
          enctype="multipart/form-data"
          class="card shadow-sm p-4">

      <?= csrf_field() ?>

      <!-- Title -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Visitor Title</label>
        <input type="text" name="title" class="form-control" required>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-semibold">From Date</label>
          <input type="date" name="from_date" id="from_date" class="form-control" required>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label fw-semibold">To Date</label>
          <input type="date" name="to_date" id="to_date" class="form-control" required>
        </div>
      </div>

      <!-- Images -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Upload Visitor Images</label>
        <input type="file"
               name="visitor_images[]"
               id="visitor_images"
               multiple
               accept="image/*"
               class="form-control"
               required>
      </div>

      <div id="image-description-container"></div>

      <!-- Document -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Upload Visitor Document</label>
        <input type="file"
               name="visitor_document"
               accept=".pdf,.doc,.docx"
               class="form-control">
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Document Description</label>
        <textarea name="document_description"
                  class="form-control"
                  rows="2"></textarea>
      </div>

      <div class="text-end">
        <button type="submit" class="btn btn-primary">
          Save Visitor
        </button>
      </div>
    </form>
  </div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const fromDate = document.getElementById('from_date');
    const toDate = document.getElementById('to_date');
    const descriptionContainer = document.getElementById('image-description-container');

    const imageInput =
        document.getElementById('event_images') ||
        document.getElementById('visitor_images') ||
        document.getElementById('gallery_images') ||
        document.getElementById('program_images');

    if (fromDate && toDate) {
        fromDate.addEventListener('change', function () {
            toDate.min = this.value;
        });

        toDate.addEventListener('change', function () {
            fromDate.max = this.value;
        });
    }

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