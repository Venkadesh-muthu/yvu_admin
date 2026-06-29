<main class="main-content">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Events</h2>
      <a href="<?= base_url('events/add') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Event
      </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php elseif (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="card shadow-sm p-4">
      <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
            <tr>
                <th width="5%">#</th>
                <th>Title</th>
                <th>Description</th>
                <th>Images</th>
                <th>Document</th>
                <th>From Date</th>
                <th>To Date</th>
                <th>Created / Updated</th>
                <th width="15%">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($events)): ?>

                <?php
                $currentPage = $pager->getCurrentPage('default');
                $perPage     = $pager->getPerPage('default');
                $start       = ($currentPage - 1) * $perPage + 1;
                $i = $start;
                ?>

                <?php foreach ($events as $e): ?>
                <tr>
                    <td><?= $i++ ?></td>

                    <!-- 🔹 Title -->
                    <td class="fw-semibold">
                    <?= esc($e['title'] ?? '-') ?>
                    </td>

                    <!-- 🔹 Description (truncate if long) -->
                    <td>
                    <?php
                        $desc = $e['description'] ?? '';
                    if (strlen($desc) > 50) {
                        echo esc(substr($desc, 0, 50)) . '...';
                    } else {
                        echo esc($desc);
                    }
                    ?>
                    </td>

                    <!-- 🔹 Images -->
                    <td>
                    <?php
                        $images = json_decode($e['event_images'] ?? '[]', true);
                    if (!empty($images)):
                        foreach ($images as $img):
                            ?>
                            <a href="<?= base_url('uploads/events/images/' . $img) ?>"
                            target="_blank"
                            class="d-block text-decoration-none">
                            <i class="bi bi-image"></i>
                            <?= esc($img) ?>
                            </a>
                    <?php
                        endforeach;
                    else:
                        ?>
                            <em class="text-muted">No images</em>
                    <?php endif; ?>
                    </td>

                    <!-- 🔹 Document -->
                    <td>
                    <?php if (!empty($e['event_document'])): ?>
                        <a href="<?= base_url('uploads/events/documents/' . $e['event_document']) ?>"
                        target="_blank"
                        class="text-decoration-none">
                        <i class="bi bi-file-earmark-pdf"></i>
                        <?= esc($e['event_document']) ?>
                        </a>
                    <?php else: ?>
                        <em class="text-muted">No document</em>
                    <?php endif; ?>
                    </td>

                    <!-- 🔹 From Date -->
                    <td>
                    <?= !empty($e['from_date'])
                                ? date('d M Y', strtotime($e['from_date']))
                                : '<span class="text-muted">—</span>' ?>
                    </td>

                    <!-- 🔹 To Date -->
                    <td>
                    <?= !empty($e['to_date'])
                                ? date('d M Y', strtotime($e['to_date']))
                                : '<span class="text-muted">—</span>' ?>
                    </td>

                    <!-- 🔹 Created / Updated -->
                    <td>
                    <?= !empty($e['updated_at'])
                                ? date('d M Y', strtotime($e['updated_at']))
                                : date('d M Y', strtotime($e['created_at'])) ?>
                    </td>

                    <!-- 🔹 Actions -->
                    <td>
                    <a href="<?= base_url('events/edit/' . $e['id']) ?>"
                        class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i>
                    </a>

                    <a href="<?= base_url('events/delete/' . $e['id']) ?>"
                        class="btn btn-sm btn-danger"
                        onclick="return confirm('Are you sure to delete this event?');">
                        <i class="bi bi-trash"></i>
                    </a>
                    </td>
                </tr>
                <?php endforeach; ?>

            <?php else: ?>
                <tr>
                <td colspan="8" class="text-center text-muted">
                    No events found.
                </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
      </div>

      <div class="mt-4 ps-2">
        <?= $pager->links('default', 'bootstrap') ?>
      </div>
    </div>
  </div>
</main>
