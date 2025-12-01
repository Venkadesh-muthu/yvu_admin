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
      method="post" enctype="multipart/form-data" class="card shadow-sm p-4">

      <?= csrf_field() ?>

      <!-- 🔹 File Upload -->
      <div class="mb-3">
        <label for="attachments" class="form-label fw-semibold">
          Upload New Files (PDF, DOCX, XLSX, Images, etc.)
        </label>
        <input type="file" name="documents[]" id="attachments" multiple class="form-control">

        <?php
            $files = json_decode($newspaper['documents'] ?? '[]', true);
      if (!empty($files)): ?>
            <div class="mt-3">
                <label class="fw-semibold d-block mb-2">Existing Files:</label>
                <?php foreach ($files as $index => $file): ?>
                <div class="d-flex align-items-center mb-2 justify-content-between border p-2 rounded">
                    <a href="<?= base_url('uploads/newspapers/' . $file) ?>" 
                       target="_blank" class="text-decoration-none text-primary">
                       <i class="bi bi-file-earmark-text me-2"></i> <?= esc($file) ?>
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

      <!-- 🔹 Start Date -->
      <div class="mb-3">
        <label for="start_date" class="form-label fw-semibold">
          Start Date <span class="text-danger">*</span>
        </label>
        <input type="date" name="start_date" id="start_date" 
               value="<?= set_value('start_date', $newspaper['start_date'] ?? '') ?>"
               class="form-control" required>
      </div>

      <!-- 🔹 End Date -->
      <div class="mb-3">
        <label for="end_date" class="form-label fw-semibold">
          End Date <span class="text-danger">*</span>
        </label>
        <input type="date" name="end_date" id="end_date" 
               value="<?= set_value('end_date', $newspaper['end_date'] ?? '') ?>"
               class="form-control" required>
      </div>

      <div class="text-end">
        <button type="submit" class="btn btn-primary">Update Newspaper</button>
      </div>
    </form>
  </div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');

    startInput.addEventListener('change', function() {
        const startDate = this.value;
        endInput.min = startDate; // End date cannot be before start date
        if (endInput.value < startDate) {
            endInput.value = startDate; // Auto-update end date if invalid
        }
    });
});
</script>