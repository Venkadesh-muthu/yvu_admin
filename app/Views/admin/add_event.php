<main class="main-content">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Add Event</h2>
      <a href="<?= base_url('events') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    <!-- 🔴 Validation Error Messages -->
    <?php if (isset($validation)): ?>
      <div class="alert alert-danger">
        <?= $validation->listErrors() ?>
      </div>
    <?php endif; ?>

    <!-- ✅ Success Message -->
    <?php if (session()->has('success')): ?>
      <div class="alert alert-success">
        <?= esc(session('success')) ?>
      </div>
    <?php endif; ?>

    <form
      action="<?= base_url('events/add') ?>"
      method="post"
      enctype="multipart/form-data"
      class="card shadow-sm p-4">

      <?= csrf_field() ?>

      <!-- 🔹 Event Title -->
      <div class="mb-3">
        <label for="title" class="form-label fw-semibold">
          Event Title
        </label>
        <input
          type="text"
          name="title"
          id="title"
          class="form-control"
          value="<?= set_value('title') ?>"
          placeholder="Enter event title"
          required>
      </div>

      <div class="row">
        <!-- 🔹 From Date -->
        <div class="col-md-6 mb-3">
          <label for="from_date" class="form-label fw-semibold">
            From Date <span class="text-danger">*</span>
          </label>
          <input
            type="date"
            name="from_date"
            id="from_date"
            class="form-control"
            required>
        </div>

        <!-- 🔹 To Date -->
        <div class="col-md-6 mb-3">
          <label for="to_date" class="form-label fw-semibold">
            To Date <span class="text-danger">*</span>
          </label>
          <input
            type="date"
            name="to_date"
            id="to_date"
            class="form-control"
            value="<?= set_value('to_date') ?>"
            required>
        </div>
      </div>

      <!-- 🔹 Event Description -->
      <!-- <div class="mb-3">
        <label for="description" class="form-label fw-semibold">
          Description
        </label>
        <textarea
          name="description"
          id="description"
          class="form-control"
          rows="4"
          placeholder="Enter event description"><?= set_value('description') ?></textarea>
      </div> -->

      <!-- 🔹 Upload Event Images -->
      <div class="mb-3">
        <label for="event_images" class="form-label fw-semibold">
          Upload Event Images <span class="text-danger">*</span>
        </label>
        <input
          type="file"
          name="event_images[]"
          id="event_images"
          accept="image/*"
          multiple
          class="form-control"
          required>
      </div>

      <!-- 🔹 Image Description Fields (Dynamic) -->
      <div id="image-description-container"></div>

      <!-- 🔹 Upload Event Document -->
      <div class="mb-3">
        <label for="event_document" class="form-label fw-semibold">
          Upload Event Document
        </label>
        <input
          type="file"
          name="event_document"
          id="event_document"
          accept=".pdf,.doc,.docx"
          class="form-control">
      </div>

      <!-- 🔹 Document Description -->
      <div class="mb-3">
        <label for="document_description" class="form-label fw-semibold">
          Document Description
        </label>
        <textarea
          name="document_description"
          id="document_description"
          class="form-control"
          rows="2"
          placeholder="Enter document description"></textarea>
      </div>


      <div class="text-end">
        <button type="submit" class="btn btn-primary">
          Save Event
        </button>
      </div>
    </form>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const fromDate = document.getElementById('from_date');
    const toDate = document.getElementById('to_date');
    const imageInput = document.getElementById('event_images');
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
