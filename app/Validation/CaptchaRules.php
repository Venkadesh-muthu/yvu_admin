<?php

namespace App\Validation;

use App\Services\CaptchaService;

class CaptchaRules
{
    public function captcha(string $value, ?string $fields = null, array $data = [], ?string &$error = null): bool
    {
        if ((new CaptchaService())->validate($value)) {
            return true;
        }

        $error = 'Invalid CAPTCHA';

        return false;
    }
}
