<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Captcha extends BaseConfig
{
    public int $length = 6;

    public int $width = 160;

    public int $height = 50;

    public int $ttl = 300;

    public string $sessionHashKey = 'captcha_hash';

    public string $sessionGeneratedKey = 'captcha_generated_at';

    public string $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
}
