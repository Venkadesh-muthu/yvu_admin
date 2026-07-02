<?php

namespace App\Services;

use Config\Captcha;
use Config\Services;
use CodeIgniter\HTTP\ResponseInterface;

class CaptchaService
{
    public function __construct(private ?Captcha $config = null)
    {
        $this->config ??= config('Captcha');
    }

    public function generateCode(): string
    {
        $characters = $this->config->characters;
        $length = random_int(5, max(5, $this->config->length));
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }

        $this->store($code);

        return $code;
    }

    public function validate(?string $answer): bool
    {
        $session = session();
        $hash = $session->get($this->config->sessionHashKey);
        $generatedAt = (int) $session->get($this->config->sessionGeneratedKey);

        $this->clear();

        if (empty($answer) || empty($hash) || empty($generatedAt)) {
            return false;
        }

        if ($generatedAt + $this->config->ttl < time()) {
            return false;
        }

        return password_verify($this->normalize($answer), $hash);
    }

    public function imageResponse(): ResponseInterface
    {
        $code = $this->generateCode();

        if (extension_loaded('gd') && function_exists('imagecreatetruecolor')) {
            return $this->pngResponse($code);
        }

        return $this->svgResponse($code);
    }

    public function clear(): void
    {
        session()->remove([
            $this->config->sessionHashKey,
            $this->config->sessionGeneratedKey,
        ]);
    }

    private function store(string $code): void
    {
        session()->set([
            $this->config->sessionHashKey => password_hash($this->normalize($code), PASSWORD_DEFAULT),
            $this->config->sessionGeneratedKey => time(),
        ]);
    }

    private function normalize(string $value): string
    {
        return strtoupper(trim($value));
    }

    private function pngResponse(string $code): ResponseInterface
    {
        $width = $this->config->width;
        $height = $this->config->height;
        $image = imagecreatetruecolor($width, $height);

        $background = imagecolorallocate($image, 245, 248, 252);
        $text = imagecolorallocate($image, 28, 62, 110);
        $noise = imagecolorallocate($image, 130, 150, 180);
        $line = imagecolorallocate($image, 185, 198, 215);

        imagefilledrectangle($image, 0, 0, $width, $height, $background);

        for ($i = 0; $i < 6; $i++) {
            imageline(
                $image,
                random_int(0, $width),
                random_int(0, $height),
                random_int(0, $width),
                random_int(0, $height),
                $line
            );
        }

        for ($i = 0; $i < 90; $i++) {
            imagesetpixel($image, random_int(0, $width - 1), random_int(0, $height - 1), $noise);
        }

        $font = 5;
        $x = 18;
        $y = (int) (($height - imagefontheight($font)) / 2);

        for ($i = 0, $count = strlen($code); $i < $count; $i++) {
            imagestring($image, $font, $x + ($i * 22), $y + random_int(-3, 3), $code[$i], $text);
        }

        ob_start();
        imagepng($image);
        $contents = ob_get_clean();
        imagedestroy($image);

        return Services::response()
            ->setHeader('Content-Type', 'image/png')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setBody($contents);
    }

    private function svgResponse(string $code): ResponseInterface
    {
        $escapedCode = esc($code, 'html');
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $this->config->width . '" height="' . $this->config->height . '" viewBox="0 0 ' . $this->config->width . ' ' . $this->config->height . '">'
            . '<rect width="100%" height="100%" fill="#f5f8fc"/>'
            . '<line x1="10" y1="12" x2="150" y2="42" stroke="#b9c6d7" stroke-width="2"/>'
            . '<line x1="5" y1="40" x2="155" y2="9" stroke="#b9c6d7" stroke-width="2"/>'
            . '<text x="20" y="34" font-family="monospace" font-size="26" font-weight="700" fill="#1c3e6e" letter-spacing="6">' . $escapedCode . '</text>'
            . '</svg>';

        return Services::response()
            ->setHeader('Content-Type', 'image/svg+xml')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setBody($svg);
    }
}
