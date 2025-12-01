<?php
// phpinfo();exit;
use CodeIgniter\Boot;
use Config\Paths;

// Minimum PHP version
$minPhpVersion = '8.1';
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;
    exit(1);
}

// Path to the front controller
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Load our Paths config file (adjust path to where app/ is)
require FCPATH . 'app/Config/Paths.php';

// Instantiate the Paths class
$paths = new Paths();

// Load the framework bootstrap file
require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));
