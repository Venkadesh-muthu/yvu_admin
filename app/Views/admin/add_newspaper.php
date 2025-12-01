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

      <!-- 🔹 Upload Images -->
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

      <!-- 🔹 Start Date -->
      <div class="mb-3">
        <label for="start_date" class="form-label fw-semibold">
          Start Date<span class="text-danger">*</span>
        </label>
        <input 
          type="date" 
          name="start_date" 
          id="start_date" 
          class="form-control"
          value="<?= set_value('start_date') ?>" 
          required>
      </div>

      <!-- 🔹 End Date -->
      <div class="mb-3">
        <label for="end_date" class="form-label fw-semibold">
          End Date<span class="text-danger">*</span>
        </label>
        <input 
          type="date" 
          name="end_date" 
          id="end_date" 
          class="form-control"
          value="<?= set_value('end_date') ?>" 
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