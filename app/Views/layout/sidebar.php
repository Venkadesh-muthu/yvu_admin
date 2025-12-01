<?php $uri = service('uri'); ?>
<nav id="sidebar" class="offcanvas offcanvas-start d-lg-block bg-white border-end shadow-sm" tabindex="-1"
    aria-labelledby="sidebarLabel" data-bs-scroll="true" data-bs-backdrop="false" style="width: 250px; z-index: 1045;">

    <div class="offcanvas-header d-lg-none">
        <h5 class="offcanvas-title" id="sidebarLabel">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body d-flex flex-column p-0">
        <div class="text-center py-4 border-bottom">
            <img src="<?= base_url('admin-template/assets/Profile_pic.png') ?>" class="rounded-circle mb-2" width="80"
                height="80" alt="Admin">
            <h5 class="mb-0"><?= session()->get('adminUser')['username'] ?? 'Admin' ?></h5>
        </div>

        <ul class="nav flex-column px-3 pt-3">
            <!-- Dashboard -->
            <li class="nav-item mb-2">
                <a href="<?= base_url('admin/dashboard') ?>"
                    class="nav-link <?= $uri->getSegment(2) === 'dashboard' ? 'active fw-bold text-primary' : 'text-dark' ?>">
                    Dashboard
                </a>
            </li>

            <!-- Volumes -->
            <li class="nav-item mb-2">
                <a href="<?= base_url('admin/volumes') ?>"
                    class="nav-link <?= $uri->getSegment(2) === 'volumes' ? 'active fw-bold text-primary' : 'text-dark' ?>">
                    Volumes
                </a>
            </li>

            <!-- Issues -->
            <li class="nav-item mb-2">
                <a href="<?= base_url('admin/issues') ?>"
                    class="nav-link <?= $uri->getSegment(2) === 'issues' ? 'active fw-bold text-primary' : 'text-dark' ?>">
                    Issues
                </a>
            </li>

            <!-- Articles -->
            <li class="nav-item mb-2">
                <a href="<?= base_url('admin/articles') ?>"
                    class="nav-link <?= $uri->getSegment(2) === 'articles' ? 'active fw-bold text-primary' : 'text-dark' ?>">
                    Articles
                </a>
            </li>

            <!-- Logout -->
            <li class="nav-item mt-auto mb-3">
                <a href="<?= base_url('admin/logout') ?>" class="nav-link text-dark">
                    Logout
                </a>
            </li>
        </ul>
    </div>
</nav>