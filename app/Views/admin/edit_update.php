<main class="main-content">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Edit Update</h2>
      <a href="<?= base_url('updates') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    <form 
      action="<?= base_url('updates/edit/' . $update['id']) ?>" 
      method="post" 
      enctype="multipart/form-data" 
      class="card shadow-sm p-4">

      <?= csrf_field() ?>

      <!-- Heading -->
      <div class="mb-3">
        <label for="heading" class="form-label fw-semibold">
          Heading <span class="text-danger">*</span>
        </label>
        <input 
          type="text" 
          name="heading" 
          id="heading" 
          class="form-control" 
          required
          value="<?= esc($update['heading']) ?>">
      </div>

      <!-- Type -->
      <div class="mb-3">
        <label for="type" class="form-label fw-semibold">
          Type <span class="text-danger">*</span>
        </label>
        <select name="type" id="type" class="form-select" required>
          <option value="">-- Select Type --</option>
          <option value="Notifications" <?= ($update['type'] == 'Notifications') ? 'selected' : '' ?>>Notifications</option>
          <option value="Forms" <?= ($update['type'] == 'Forms') ? 'selected' : '' ?>>Forms</option>
          <option value="Circulars" <?= ($update['type'] == 'Circulars') ? 'selected' : '' ?>>Circulars</option>
          <option value="Results" <?= ($update['type'] == 'Results') ? 'selected' : '' ?>>Results</option>
          <option value="Latest Info" <?= ($update['type'] == 'Latest Info') ? 'selected' : '' ?>>Latest Info</option>
          <option value="Tenders" <?= ($update['type'] == 'Tenders') ? 'selected' : '' ?>>Tenders</option>
          <option value="Events" <?= ($update['type'] == 'Events') ? 'selected' : '' ?>>Events</option>
          <option value="Downloads" <?= ($update['type'] == 'Downloads') ? 'selected' : '' ?>>Downloads</option>
        </select>
      </div>

      <!-- File Upload -->
      <div class="mb-3">
        <label for="attachments" class="form-label fw-semibold">
          Upload New Files (PDF, DOCX, etc.)
        </label>
        <input 
          type="file" 
          name="documents[]" 
          id="attachments" 
          multiple 
          class="form-control">

        <?php
          $files = json_decode($update['documents'] ?? '[]', true);
      if (!empty($files)):
          ?>
          <div class="mt-3">
            <label class="fw-semibold d-block mb-2">Existing Files:</label>
            <?php foreach ($files as $index => $file): ?>
              <div class="d-flex align-items-center mb-2 justify-content-between border p-2 rounded">
                <a 
                  href="<?= base_url('uploads/updates/' . $file) ?>" 
                  target="_blank" 
                  class="text-decoration-none text-primary">
                  <i class="bi bi-file-earmark-text me-2"></i> <?= esc($file) ?>
                </a>
                <a 
                  href="<?= base_url('updates/deleteFile/' . $update['id'] . '/' . $index) ?>" 
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
          Update Update
        </button>
      </div>
    </form>
  </div>
</main>
