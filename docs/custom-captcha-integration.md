# Custom Image CAPTCHA Integration

This project uses a local CodeIgniter 4 image CAPTCHA. It does not use any third-party CAPTCHA verification API.

## Files

```text
app/Config/Captcha.php
app/Controllers/CaptchaController.php
app/Helpers/captcha_helper.php
app/Services/CaptchaService.php
app/Validation/CaptchaRules.php
```

## Routes

```php
$routes->get('captcha/image', 'CaptchaController::image');
```

## Controller Validation

Add this rule before authenticating any login form:

```php
'captcha_code' => [
    'rules'  => 'required|captcha',
    'errors' => [
        'required' => 'Invalid CAPTCHA',
        'captcha'  => 'Invalid CAPTCHA',
    ],
],
```

## View Snippet

```php
<?= csrf_field() ?>

<label for="captcha_code">Enter CAPTCHA</label>
<?= captcha_image() ?>
<button type="button" id="refresh-captcha">Refresh CAPTCHA</button>
<input type="text" name="captcha_code" id="captcha_code" required>

<script>
    document.getElementById('refresh-captcha').addEventListener('click', function () {
        document.getElementById('captcha-image').src = '<?= site_url('captcha/image') ?>?v=' + Date.now();
        document.getElementById('captcha_code').value = '';
    });
</script>
```

Use the same validation rule and view snippet for Admin, Student, Faculty, and Staff login pages.
