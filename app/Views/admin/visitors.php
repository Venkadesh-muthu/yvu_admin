<main class="main-content">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Visitors</h2>
      <a href="<?= base_url('visitors/add') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Visitor
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
          <?php if (!empty($visitors)):
              $i = 1;
              foreach ($visitors as $v): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= esc($v['title']) ?></td>

              <!-- Images -->
              <td>
                <?php if (!empty($v['images'])):
                    foreach ($v['images'] as $img): ?>
                    <a href="<?= base_url('uploads/visitors/images/'.$img['image']) ?>" target="_blank">
                      <i class="bi bi-image"></i>
                    </a>
                <?php endforeach;
                else: ?>
                  <em class="text-muted">No images</em>
                <?php endif; ?>
              </td>

              <!-- Document -->
              <td>
                <?php if (!empty($v['visitor_document'])): ?>
                  <a href="<?= base_url('uploads/visitors/documents/'.$v['visitor_document']) ?>" target="_blank">
                    <i class="bi bi-file-earmark"></i>
                  </a>
                <?php else: ?>
                  <em class="text-muted">No document</em>
                <?php endif; ?>
              </td>

              <td><?= date('d M Y', strtotime($v['from_date'])) ?></td>
              <td><?= date('d M Y', strtotime($v['to_date'])) ?></td>
              <td><?= date('d M Y', strtotime($v['updated_at'])) ?></td>

              <td>
                <a href="<?= base_url('visitors/edit/'.$v['id']) ?>" class="btn btn-warning btn-sm">
                  <i class="bi bi-pencil"></i>
                </a>
                <a href="<?= base_url('visitors/delete/'.$v['id']) ?>"
                   onclick="return confirm('Delete this visitor?');"
                   class="btn btn-danger btn-sm">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach;
      else: ?>
            <tr>
              <td colspan="8" class="text-center text-muted">No visitors found</td>
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