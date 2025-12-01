<main class="main-content">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Add Newspaper</h2>
      <a href="<?= base_url('newspapers') ?>" class="btn btn-outline-secondary">
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
      action="<?= base_url('newspapers/add') ?>" 
      method="post" 
      enctype="multipart/form-data" 
      class="card shadow-sm p-4">

      <?= csrf_field() ?>

      <div class="mb-3">
        <label for="attachments" class="form-label fw-semibold">
          Upload Images<span class="text-danger">*</span>
        </label>
        <input 
          type="file" 
          name="documents[]" 
          id="attachments" 
          accept="image/*"
          multiple 
          class="form-control" 
          required>
      </div>

      <div class="text-end">
        <button type="submit" class="btn btn-primary">
          Save Newspaper
        </button>
      </div>
    </form>
  </div>
</main>
