<main class="main-content">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Edit Newspaper</h2>
      <a href="<?= base_url('newspapers') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    <!-- 🔴 Validation Errors -->
    <?php if (isset($validation)): ?>
      <div class="alert alert-danger">
        <?= $validation->listErrors() ?>
      </div>
    <?php endif; ?>

    <form 
      action="<?= base_url('newspapers/edit/' . $newspaper['id']) ?>"
      method="post" 
      enctype="multipart/form-data" 
      class="card shadow-sm p-4">

      <?= csrf_field() ?>

      <!-- 🔹 Newspaper Title -->
      <div class="mb-3">
        <label for="title" class="form-label fw-semibold">
          Newspaper Title
        </label>
        <input 
          type="text" 
          name="title" 
          id="title"
          class="form-control"
          value="<?= set_value('title', $newspaper['title'] ?? '') ?>"
          placeholder="Enter newspaper title">
      </div>

      <!-- 🔹 Publish Date -->
      <div class="mb-3">
        <label for="publish_date" class="form-label fw-semibold">
          Publish Date <span class="text-danger">*</span>
        </label>
        <input 
          type="date" 
          name="publish_date" 
          id="publish_date"
          class="form-control"
          value="<?= set_value('publish_date', $newspaper['publish_date'] ?? '') ?>"
          required>
      </div>

      <!-- 🔹 File Upload -->
      <div class="mb-3">
        <label for="attachments" class="form-label fw-semibold">
          Upload New Files
        </label>
        <input 
          type="file" 
          name="documents[]" 
          id="attachments" 
          multiple 
          class="form-control">

        <?php
          $files = json_decode($newspaper['documents'] ?? '[]', true);
      if (!empty($files)):
          ?>
          <div class="mt-3">
            <label class="fw-semibold d-block mb-2">Existing Files:</label>

            <?php foreach ($files as $index => $file): ?>
              <div class="d-flex align-items-center justify-content-between border p-2 rounded mb-2">
                <a href="<?= base_url('uploads/newspapers/' . $file) ?>" 
                   target="_blank" 
                   class="text-decoration-none text-primary">
                  <i class="bi bi-file-earmark-text me-2"></i>
                  <?= esc($file) ?>
                </a>

                <a href="<?= base_url('newspapers/deleteFile/' . $newspaper['id'] . '/' . $index) ?>"
                   class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('Are you sure you want to delete this file?');">
                  <i class="bi bi-trash"></i>
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="text-end">
        <button type="submit" class="btn btn-primary">
          Update Newspaper
        </button>
      </div>
    </form>
  </div>
</main>
