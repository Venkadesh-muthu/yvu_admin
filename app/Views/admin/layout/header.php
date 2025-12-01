<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'IJACS Admin') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        /* Sidebar width compensation for desktop */
        @media (min-width: 992px) {
            .main-content {
                margin-left: 250px;
                /* Sidebar width */
                padding: 80px 20px 20px 20px;
                /* top padding for fixed navbar */
            }
        }

        /* Full width on mobile */
        @media (max-width: 991.98px) {
            .main-content {
                margin-left: 0;
                padding: 80px 15px 15px 15px;
            }
        }

        @media (min-width: 992px) {
            #sidebar {
                position: fixed !important;
                top: 0;
                left: 0;
                height: 100vh;
                transform: none !important;
                visibility: visible !important;
            }
        }

        .navbar-nav-wrapper {
            width: 100%;
        }

        @media (max-width: 991.98px) {
            .navbar-nav-wrapper {
                background: #0d6efd;
                padding: 1rem;
            }
        }
         .bg-primary {
            background-color: var(--yvu-primary) !important;
        }
        .dataTables_wrapper .row {
            padding: 5px !important;
        }
        .dataTables_info,
        .dataTables_paginate {
            display: none !important;
        }
    </style>
</head>

<body>

    <!-- ✅ Top Navbar -->
    <nav class="navbar navbar-expand-lg bg-primary navbar-dark fixed-top">
        <div class="container-fluid">
            <!-- Sidebar toggle (for mobile only) -->
            <button class="btn btn-light d-lg-none me-2" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#sidebar">
                <i class="bi bi-list"></i>
            </button>

            <!-- Brand -->
            <a class="navbar-brand fw-bold" href="#">YVU Admin</a>

            <!-- Right-aligned content -->
            <div class="ms-auto d-flex align-items-center">
                <!-- Search (optional) -->
                <form class="d-none d-md-flex me-3">
                    <input class="form-control form-control-sm" type="search" placeholder="Search...">
                </form>

                <!-- Notifications -->
                <div class="nav-item dropdown me-3">
                    <a class="nav-link text-white" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-5"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">No new notifications</a></li>
                    </ul>
                </div>

                <!-- Profile Dropdown -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#"
                        id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?= base_url('admin-template/assets/yvu150a.gif') ?>" class="rounded-circle"
                            width="36" height="36" alt="Profile">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#profileModal">Profile</a></li>
                        <li>
                            <a class="dropdown-item text-danger" href="<?= base_url('logout') ?>">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="<?= base_url('admin/profile/update') ?>" enctype="multipart/form-data"
                class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="text-center mb-3">
                        <img src="<?= base_url('assets/Profile_pic.png') ?>" class="rounded-circle mb-2" width="80"
                            height="80">
                        <input type="file" name="profile_photo" class="form-control form-control-sm mt-2">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required
                            value="<?= session('user_name') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required
                            value="<?= session('user_email') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?= session('user_phone') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control"
                            placeholder="Leave blank to keep current">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>