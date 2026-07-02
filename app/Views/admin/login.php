<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>YVU Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-box {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-box h3 {
            color: #004e92;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-control:focus {
            border-color: #004e92;
            box-shadow: none;
        }

        .btn-primary {
            background-color: #004e92;
            border: none;
        }

        .alert {
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="login-box">
        <h3>YVU Admin Login</h3>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger text-center py-2">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <div><?= esc($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= base_url('login') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="email" class="form-label">User Id</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter User Id"
                    value="<?= old('email') ? esc(old('email'), 'attr') : '' ?>" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label d-flex justify-content-between">
                    <span>Password</span>
                </label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter password"
                    required>
            </div>

            <div class="mb-3">
                <label for="captcha_code" class="form-label">Enter CAPTCHA</label>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <?= captcha_image() ?>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="refresh-captcha">
                        Refresh CAPTCHA
                    </button>
                </div>
                <input type="text" name="captcha_code" id="captcha_code" class="form-control"
                    placeholder="Enter CAPTCHA" autocomplete="off" required>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Login
                </button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('refresh-captcha').addEventListener('click', function () {
            const image = document.getElementById('captcha-image');
            image.src = '<?= site_url('captcha/image') ?>?v=' + Date.now();
            document.getElementById('captcha_code').value = '';
        });
    </script>
</body>

</html>
