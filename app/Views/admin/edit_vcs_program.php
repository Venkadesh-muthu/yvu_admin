<main class="main-content">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Edit VC’s Program</h2>
      <a href="<?= base_url('vcs-programs') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    <form action="<?= base_url('vcs-programs/edit/' . $vcsProgram['id']) ?>"
          method="post"
          enctype="multipart/form-data"
          class="card shadow-sm p-4">

      <?= csrf_field() ?>

      <div class="mb-3">
        <label class="form-label fw-semibold">Program Title</label>
        <input type="text"
               name="title"
               class="form-control"
               value="<?= $vcsProgram['title'] ?>">
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-semibold">From Date</label>
          <input type="date"
                 name="from_date"
                 id="from_date"
                 class="form-control"
                 value="<?= $vcsProgram['from_date'] ?>"
                 required>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label fw-semibold">To Date</label>
          <input type="date"
                 name="to_date"
                 id="to_date"
                 class="form-control"
                 value="<?= $vcsProgram['to_date'] ?>"
                 required>
        </div>
      </div>

      <!-- Existing Images -->
      <?php if (!empty($vcsImages)): ?>
        <div class="mb-4">
          <label class="fw-semibold d-block mb-3">Existing Images:</label>

          <?php foreach ($vcsImages as $img): ?>
            <div class="border rounded p-3 mb-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <a href="<?= base_url('uploads/vcs/images/' . $img['image']) ?>"
                   target="_blank">
                   <?= esc($img['image']) ?>
                </a>

                <a href="<?= base_url('vcs-programs/deleteImage/' . $img['id']) ?>"
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
               name="program_images[]"
               id="program_images"
               multiple
               accept="image/*"
               class="form-control">
      </div>

      <div id="image-description-container"></div>

      <!-- Replace Document -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Replace Program Document</label>
        <input type="file"
               name="program_document"
               accept=".pdf,.doc,.docx"
               class="form-control">

        <textarea name="document_description"
                  class="form-control mt-2"
                  rows="2"><?= esc($vcsProgram['document_description'] ?? '') ?></textarea>
      </div>

      <div class="text-end">
        <button type="submit" class="btn btn-primary">Update Program</button>
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