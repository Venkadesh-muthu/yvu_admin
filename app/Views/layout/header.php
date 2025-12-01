<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>IJACS | Indian Journal of Advances in Chemical Science</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>

<body>

    <!-- Header / Navigation -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold text-primary" href="index">IJACS</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav ms-auto fw-semibold">
                        <li class="nav-item"><a
                                class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index' ? 'active text-primary' : '' ?>"
                                href="/">Home</a></li>
                        <li class="nav-item"><a
                                class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'about' ? 'active text-primary' : '' ?>"
                                href="about">About the Journal</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= in_array(uri_string(), ['current-issue', 'issues', 'special-issues']) ? 'active text-primary' : '' ?>"
                                href="#" role="button" data-bs-toggle="dropdown">
                                Issues
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= base_url('current-issue') ?>">Current Issue</a>
                                </li>
                                <li><a class="dropdown-item" href="<?= base_url('issues') ?>">Archives</a></li>
                                <li><a class="dropdown-item" href="<?= base_url('special-issues') ?>">Special Issues</a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item"><a
                                class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'aimscope' ? 'active text-primary' : '' ?>"
                                href="aimscope">Aim & Scope</a></li>
                        <li class="nav-item"><a
                                class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'editorial-board' ? 'active text-primary' : '' ?>"
                                href="editorial-board">Editorial Board</a></li>
                        <li class="nav-item"><a
                                class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'special-issues' ? 'active text-primary' : '' ?>"
                                href="special-issues">Special Issues</a></li>
                        <li class="nav-item"><a
                                class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'contact' ? 'active text-primary' : '' ?>"
                                href="contact">Contact Us</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>