<main class="main-content">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Newspapers</h2>
      <a href="<?= base_url('newspapers/add') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Newspaper
      </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php elseif (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="card shadow-sm p-4">
      <table id="articlesTable" class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th width="5%">#</th>
            <th>Title</th>
            <th>Attachments</th>
            <th>Publish Date</th>
            <th>Created / Updated</th>
            <th width="15%">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($newspapers)): ?>

            <?php
              $currentPage = $pager->getCurrentPage('default');
              $perPage     = $pager->getPerPage('default');
              $start       = ($currentPage - 1) * $perPage + 1;
              $i = $start;
              ?>

            <?php foreach ($newspapers as $n): ?>
              <tr>
                <td><?= $i++ ?></td>

                <!-- 🔹 Title -->
                <td class="fw-semibold">
                  <?= esc($n['title'] ?? '-') ?>
                </td>

                <!-- 🔹 Attachments -->
                <td>
                  <?php
                      $files = json_decode($n['documents'] ?? '[]', true);
                if (!empty($files)):
                    foreach ($files as $file):
                        ?>
                        <a href="<?= base_url('uploads/newspapers/' . $file) ?>"
                           target="_blank"
                           class="d-block text-decoration-none">
                          <i class="bi bi-file-earmark-text"></i>
                          <?= esc($file) ?>
                        </a>
                  <?php
                    endforeach;
                else:
                    ?>
                    <em class="text-muted">No files</em>
                  <?php endif; ?>
                </td>
                <!-- 🔹 Publish Date -->
                <td>
                  <?= !empty($n['publish_date'])
                        ? date('d M Y', strtotime($n['publish_date']))
                        : '<span class="text-muted">—</span>' ?>
                </td>
                <!-- 🔹 Created / Updated -->
                <td>
                  <?= !empty($n['updated_at'])
                        ? date('d M Y', strtotime($n['updated_at']))
                        : date('d M Y', strtotime($n['created_at'])) ?>
                </td>

                <!-- 🔹 Actions -->
                <td>
                  <a href="<?= base_url('newspapers/edit/' . $n['id']) ?>"
                     class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil"></i>
                  </a>

                  <a href="<?= base_url('newspapers/delete/' . $n['id']) ?>"
                     class="btn btn-sm btn-danger"
                     onclick="return confirm('Are you sure to delete this newspaper?');">
                    <i class="bi bi-trash"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>

          <?php else: ?>
            <tr>
              <td colspan="5" class="text-center text-muted">
                No newspapers found.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

      <div class="mt-4 ps-2">
        <?= $pager->links('default', 'bootstrap') ?>
      </div>
    </div>
  </div>
</main>
