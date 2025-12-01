<main class="main-content">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Add Update</h2>
      <a href="<?= base_url('updates') ?>" class="btn btn-outline-secondary">
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
      action="<?= base_url('updates/add') ?>" 
      method="post" 
      enctype="multipart/form-data" 
      class="card shadow-sm p-4">

      <?= csrf_field() ?>

      <!-- Heading -->
      <div class="mb-3">
        <label for="title" class="form-label fw-semibold">
          Heading <span class="text-danger">*</span>
        </label>
        <input 
          type="text" 
          name="heading" 
          id="title" 
          class="form-control" 
          required
          placeholder="Enter update heading">
      </div>

      <!-- Type -->
      <div class="mb-3">
        <label for="type" class="form-label fw-semibold">
          Type <span class="text-danger">*</span>
        </label>
        <select name="type" id="type" class="form-select" required>
          <option value="">-- Select Type --</option>
          <option value="Notifications">Notifications</option>
          <option value="Forms">Forms</option>
          <option value="Circulars">Circulars</option>
          <option value="Results">Results</option>
          <option value="Latest Info">Latest Info</option>
          <option value="Tenders">Tenders</option>
          <option value="Events">Events</option>
          <option value="Downloads">Downloads</option>
        </select>
      </div>

      <!-- Documents -->
      <div class="mb-3">
        <label for="attachments" class="form-label fw-semibold">
          Upload Files (PDF, DOCX, etc.)
        </label>
        <input 
          type="file" 
          name="documents[]" 
          id="attachments" 
          multiple 
          class="form-control">
      </div>

      <div class="text-end">
        <button type="submit" class="btn btn-primary">
          Save Update
        </button>
      </div>
    </form>
  </div>
</main>
