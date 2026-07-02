<?php

if (! function_exists('captcha_image_url')) {
    function captcha_image_url(): string
    {
        return site_url('captcha/image') . '?v=' . rawurlencode((string) time());
    }
}

if (! function_exists('captcha_image')) {
    function captcha_image(array $attributes = []): string
    {
        $attributes = array_merge([
            'id' => 'captcha-image',
            'src' => captcha_image_url(),
            'alt' => 'CAPTCHA image',
            'class' => 'border rounded',
            'width' => '160',
            'height' => '50',
        ], $attributes);

        $html = '<img';

        foreach ($attributes as $key => $value) {
            $html .= ' ' . esc($key, 'attr') . '="' . esc((string) $value, 'attr') . '"';
        }

        return $html . '>';
    }
}
