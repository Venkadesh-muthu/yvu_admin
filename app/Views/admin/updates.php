<?php $uri = service('uri'); ?>
<?php $role = session()->get('user_type'); ?>
<main class="main-content">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Updates</h2>
      <?php if (in_array($role, ['admin'])): ?> 
        <a href="<?= base_url('updates/add') ?>" class="btn btn-primary">
          <i class="bi bi-plus-circle"></i> Add Update
        </a>
      <?php endif; ?>

    </div>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php elseif (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="card shadow-sm p-4">
      <!-- 🔽 Top-left button -->
      <div class="d-flex justify-content-start mb-3">
          <?php if (in_array($role, ['super_admin'])): ?>
              <a href="<?= base_url('updates/download') ?>"
                class="btn btn-sm btn-success">
                  <i class="bi bi-download"></i> Download
              </a>
          <?php endif; ?>
      </div>


      <table id="articlesTable" class="table table-bordered table-sm align-middle">

        <thead class="table-light">
          <tr>
            <th width="5%">#</th>
            <th>Title</th>
            <th>Type</th>
            <th>Attachments</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Created At</th>
            <th style="<?= in_array($role, ['super_admin']) ? 'display:none;' : '' ?>">
                Actions
            </th>

          </tr>
        </thead>
        <tbody>
          <?php if (!empty($updates)): ?>
            <?php
              $currentPage = $pager->getCurrentPage('default');
              $perPage = $pager->getPerPage('default');
              $start = ($currentPage - 1) * $perPage + 1;

              $i = $start;
              ?>
            <?php foreach ($updates as $index => $u): ?>
              <tr>
                <td><?= $i++ ?></td>
                <td><?= esc($u['heading']) ?></td>
                <td>
                  <?php if (!empty($u['type'])): ?>
                    <span class="badge bg-info text-dark">
                      <?= esc($u['type']) ?>
                    </span>
                  <?php else: ?>
                    <span class="badge bg-secondary">
                      Not Specified
                    </span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php
                    $files = json_decode($u['documents'], true);
                if (!empty($files)):
                    foreach ($files as $file): ?>
                          <a href="<?= base_url('uploads/updates/' . $file) ?>" 
                             target="_blank" 
                             class="d-block text-decoration-none text-primary">
                            <i class="bi bi-file-earmark-text"></i> <?= esc($file) ?>
                          </a>
                  <?php endforeach;
                else: ?>
                        <em>No files</em>
                  <?php endif; ?>
                </td>
                 <td>
                  <?= (!empty($u['start_date']))
                ? date('d M Y', strtotime($u['start_date']))
                : '<em>Not set</em>' ?>
                </td>

                <!-- ✅ END DATE -->
                <td>
                  <?= (!empty($u['end_date']))
                ? date('d M Y', strtotime($u['end_date']))
                : '<em>Not set</em>' ?>
                </td>
                <td>
                    <?php if (!empty($u['updated_at'])): ?>
                        <span class="text-primary">
                            Updated: <?= date('d M Y', strtotime($u['updated_at'])) ?>
                        </span>
                    <?php else: ?>
                        <?= date('d M Y', strtotime($u['created_at'])) ?>
                    <?php endif; ?>
                </td>
                <td style="<?= in_array($role, ['super_admin']) ? 'display:none;' : '' ?>">
                    <a href="<?= base_url('updates/edit/' . $u['id']) ?>" class="btn btn-sm btn-warning">
                      <i class="bi bi-pencil"></i>
                    </a>

                    <a href="<?= base_url('updates/delete/' . $u['id']) ?>" 
                      class="btn btn-sm btn-danger"
                      onclick="return confirm('Are you sure?');">
                      <i class="bi bi-trash"></i>
                    </a>
                </td>

              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center text-muted">No updates found.</td>
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
