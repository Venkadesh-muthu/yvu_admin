<main class="main-content">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Galleries</h2>
      <a href="<?= base_url('gallery/add') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Gallery
      </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="card shadow-sm p-4">
      <div class="table-responsive">
        <table class="table table-bordered align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Title</th>
              <th>Images</th>
              <th>Document</th>
              <th>From</th>
              <th>To</th>
              <th>Updated</th>
              <th width="15%">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php if (!empty($galleries)):
              $i = 1;
              foreach ($galleries as $g): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= esc($g['title']) ?></td>

              <!-- Images -->
              <td>
                <?php if (!empty($g['images'])):
                    foreach ($g['images'] as $img): ?>
                    <a href="<?= base_url('uploads/gallery/images/'.$img['image']) ?>" target="_blank">
                      <i class="bi bi-image"></i>
                    </a>
                <?php endforeach;
                else: ?>
                  <em class="text-muted">No images</em>
                <?php endif; ?>
              </td>

              <!-- Document -->
              <td>
                <?php if (!empty($g['gallery_document'])): ?>
                  <a href="<?= base_url('uploads/gallery/documents/'.$g['gallery_document']) ?>" target="_blank">
                    <i class="bi bi-file-earmark"></i>
                  </a>
                <?php else: ?>
                  <em class="text-muted">No document</em>
                <?php endif; ?>
              </td>

              <td><?= date('d M Y', strtotime($g['from_date'])) ?></td>
              <td><?= date('d M Y', strtotime($g['to_date'])) ?></td>
              <td><?= date('d M Y', strtotime($g['updated_at'])) ?></td>

              <td>
                <a href="<?= base_url('gallery/edit/'.$g['id']) ?>" class="btn btn-warning btn-sm">
                  <i class="bi bi-pencil"></i>
                </a>
                <a href="<?= base_url('gallery/delete/'.$g['id']) ?>"
                   onclick="return confirm('Delete this gallery?');"
                   class="btn btn-danger btn-sm">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach;
      else: ?>
            <tr>
              <td colspan="8" class="text-center text-muted">No galleries found</td>
            </tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="mt-4">
        <?= $pager->links('default', 'bootstrap') ?>
      </div>
    </div>
  </div>
</main>