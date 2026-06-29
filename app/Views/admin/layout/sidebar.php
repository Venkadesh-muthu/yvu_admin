<?php $uri = service('uri'); ?>
<?php $role = session()->get('user_type'); // admin | newsadmin | super_admin?>

<nav id="sidebar"
    class="offcanvas offcanvas-start d-lg-block border-end shadow-sm yvu-sidebar"
    tabindex="-1" aria-labelledby="sidebarLabel"
    data-bs-scroll="true" data-bs-backdrop="false"
    style="width: 260px; z-index: 1045;">

    <!-- 🔹 Mobile Header -->
    <div class="offcanvas-header d-lg-none bg-primary text-white">
        <h5 class="offcanvas-title fw-semibold" id="sidebarLabel">YVU Admin</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>

    <!-- 🔹 Sidebar Body -->
    <div class="offcanvas-body d-flex flex-column p-0 bg-white">

        <!-- 👤 Admin Profile -->
        <div class="text-center py-4 border-bottom bg-primary text-white">
            <img src="<?= base_url('admin-template/assets/yvu150-150.png') ?>"
                class="rounded-circle border border-3 border-light mb-2 shadow-sm"
                width="80" height="80" alt="Admin">

            <h5 class="fw-bold mb-0">
                <?= session()->get('username') ?? 'Admin' ?>
            </h5>
            <small class="text-light opacity-75">
                <?php
                    if ($role === 'super_admin') {
                        echo 'Super Admin';
                    } elseif ($role === 'admin') {
                        echo 'Administrator';
                    } elseif ($role === 'news_admin') {
                        echo 'News Admin';
                    } elseif ($role === 'gallery_admin') {
                        echo 'Gallery Admin';
                    } elseif ($role === 'visitors_admin') {
                        echo 'Visitors Admin';
                    } elseif ($role === 'vcs_programs_admin') {
                        echo 'VC’s Programs Admin';
                    }
?>
            </small>
        </div>

        <!-- 📋 Sidebar Menu -->
        <ul class="nav flex-column mt-3">

            <!-- Dashboard (ALL) -->
            <li class="nav-item p-2">
                <a href="<?= base_url('dashboard') ?>"
                   class="nav-link d-flex align-items-center px-4 py-2
                   <?= $uri->getSegment(1) === 'dashboard' ? 'active-link' : '' ?>">
                    <i class="bi bi-speedometer2 me-2 fs-5"></i> Dashboard
                </a>
            </li>

            <!-- Updates (ADMIN ONLY) -->
            <?php if ($role === 'admin' || $role === 'super_admin'): ?>
                <li class="nav-item p-2">
                    <a href="<?= base_url('updates') ?>"
                       class="nav-link d-flex align-items-center px-4 py-2
                       <?= $uri->getSegment(1) === 'updates' ? 'active-link' : '' ?>">
                        <i class="bi bi-arrow-repeat me-2 fs-5"></i> Updates
                    </a>
                </li>
            <?php endif; ?>

            <!-- Newspapers (NEWS ADMIN ONLY) -->
            <?php if ($role === 'news_admin'): ?>
                <li class="nav-item p-2">
                    <a href="<?= base_url('newspapers') ?>"
                       class="nav-link d-flex align-items-center px-4 py-2
                       <?= $uri->getSegment(1) === 'newspapers' ? 'active-link' : '' ?>">
                        <i class="bi bi-file-earmark-text me-2 fs-5"></i> Newspapers
                    </a>
                </li>
            <?php endif; ?>

             <!-- Events (EVENT ADMIN ONLY) -->
            <?php if ($role === 'news_admin'): ?>
                <li class="nav-item p-2">
                    <a href="<?= base_url('events') ?>"
                    class="nav-link d-flex align-items-center px-4 py-2
                    <?= $uri->getSegment(1) === 'events' ? 'active-link' : '' ?>">
                        <i class="bi bi-calendar-event me-2 fs-5"></i> Events
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($role === 'gallery_admin' || $role === 'super_admin'): ?>
                <li class="nav-item p-2">
                    <a href="<?= base_url('gallery') ?>"
                    class="nav-link d-flex align-items-center px-4 py-2
                    <?= $uri->getSegment(1) === 'gallery' ? 'active-link' : '' ?>">
                        <i class="bi bi-images me-2 fs-5"></i> Gallery
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($role === 'visitors_admin' || $role === 'super_admin'): ?>
                <li class="nav-item p-2">
                    <a href="<?= base_url('visitors') ?>"
                    class="nav-link d-flex align-items-center px-4 py-2
                    <?= $uri->getSegment(1) === 'visitors' ? 'active-link' : '' ?>">
                        <i class="bi bi-people me-2 fs-5"></i> Visitors
                    </a>
                </li> 
            <?php endif; ?>
            <?php if ($role === 'vcs_programs_admin' || $role === 'super_admin'): ?>
                <li class="nav-item p-2">
                    <a href="<?= base_url('vcs-programs') ?>"
                    class="nav-link d-flex align-items-center px-4 py-2
                    <?= $uri->getSegment(1) === 'vcs_programs' ? 'active-link' : '' ?>">
                        <i class="bi bi-journal-album me-2 fs-5"></i> VC’s Programs
                    </a>
                </li>
            <?php endif; ?>  
        </ul>

        <!-- 🔒 Logout -->
        <div class="mt-auto border-top py-3 px-4">
            <a href="<?= base_url('logout') ?>"
               class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </div>

    </div>
</nav>


<!-- 🎨 Sidebar Styling -->
<style>
    /* Base Colors */
    :root {
        --yvu-primary: #315377;
        --yvu-hover: #3e6999;
    }

    .yvu-sidebar {
        min-height: 100vh;
        background-color: #f8fafc;
    }

    /* Nav Links */
    .yvu-sidebar .nav-link {
        color: #333;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.25s ease;
    }

    .yvu-sidebar .nav-link i {
        color: var(--yvu-primary);
    }

    .yvu-sidebar .nav-link:hover {
        background-color: rgba(49, 83, 119, 0.08);
        color: var(--yvu-primary);
        transform: translateX(5px);
    }

    /* Active Link */
    .yvu-sidebar .active-link {
        background-color: var(--yvu-primary);
        color: #fff !important;
        font-weight: 600;
        box-shadow: 0 3px 10px rgba(49, 83, 119, 0.3);
    }

    .yvu-sidebar .active-link i {
        color: #fff;
    }

    /* Logout Button */
    .yvu-sidebar .btn-outline-primary {
        border-color: var(--yvu-primary);
        color: var(--yvu-primary);
        font-weight: 500;
    }

    .yvu-sidebar .btn-outline-primary:hover {
        background-color: var(--yvu-primary);
        color: #fff;
    }

    /* Mobile Header */
    .bg-primary {
        background-color: var(--yvu-primary) !important;
    }
</style>
