<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\CaptchaService;

class CaptchaController extends BaseController
{
    public function image()
    {
        return (new CaptchaService())->imageResponse();
    }
}
