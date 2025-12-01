<main class="main-content">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Edit Newspaper</h2>
      <a href="<?= base_url('newspapers') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    <form 
      action="<?= base_url('newspapers/edit/' . $newspaper['id']) ?>"
      method="post" enctype="multipart/form-data" class="card shadow-sm p-4">

      <?= csrf_field() ?>

      <div class="mb-3">
        <label for="attachments" class="form-label fw-semibold">
          Upload New Files (PDF, DOCX, XLSX, etc.)
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

      <div class="text-end">
        <button type="submit" class="btn btn-primary">Update Newspaper</button>
      </div>
    </form>
  </div>
</main>
