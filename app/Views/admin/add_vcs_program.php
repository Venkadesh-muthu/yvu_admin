<main class="main-content">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Add VC’s Program</h2>
      <a href="<?= base_url('vcs-programs') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    <!-- 🔴 Validation Errors -->
    <?php if (isset($validation)): ?>
      <div class="alert alert-danger">
        <?= $validation->listErrors() ?>
      </div>
    <?php endif; ?>

    <!-- ✅ Success Message -->
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success">
        <?= session()->getFlashdata('success') ?>
      </div>
    <?php endif; ?>

    <form action="<?= base_url('vcs-programs/add') ?>"
          method="post"
          enctype="multipart/form-data"
          class="card shadow-sm p-4">

      <?= csrf_field() ?>

      <!-- 🔹 Program Title -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Program Title</label>
        <input type="text"
               name="title"
               class="form-control"
               value="<?= set_value('title') ?>"
               required>
      </div>

      <!-- 🔹 Dates -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-semibold">From Date</label>
          <input type="date"
                 name="from_date"
                 id="from_date"
                 class="form-control"
                 value="<?= set_value('from_date') ?>"
                 required>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label fw-semibold">To Date</label>
          <input type="date"
                 name="to_date"
                 id="to_date"
                 class="form-control"
                 value="<?= set_value('to_date') ?>"
                 required>
        </div>
      </div>

      <!-- 🔹 Upload Program Images -->
      <div class="mb-3">
        <label class="form-label fw-semibold">
          Upload Program Images <span class="text-danger">*</span>
        </label>
        <input type="file"
               name="program_images[]"
               id="program_images"
               multiple
               accept="image/*"
               class="form-control"
               required>
      </div>

      <!-- 🔹 Dynamic Image Descriptions -->
      <div id="image-description-container"></div>

      <!-- 🔹 Upload Program Document -->
      <div class="mb-3">
        <label class="form-label fw-semibold">
          Upload Program Document
        </label>
        <input type="file"
               name="program_document"
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
                  rows="2"><?= set_value('document_description') ?></textarea>
      </div>

      <div class="text-end">
        <button type="submit" class="btn btn-primary">
          Save Program
        </button>
      </div>

    </form>
  </div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const fromDate = document.getElementById('from_date');
    const toDate = document.getElementById('to_date');
    const imageInput = document.getElementById('program_images');
    const descriptionContainer = document.getElementById('image-description-container');

    // Date validation
    if (fromDate && toDate) {
        fromDate.addEventListener('change', function () {
            toDate.min = this.value;
            if (toDate.value < this.value) {
                toDate.value = this.value;
            }
        });

        toDate.addEventListener('change', function () {
            fromDate.max = this.value;
            if (fromDate.value > this.value) {
                fromDate.value = this.value;
            }
        });
    }

    // Dynamic image descriptions
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