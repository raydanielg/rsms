<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// The path to your Laravel project directory (one level up from public_html)
$candidates = [
    __DIR__,
    dirname(__DIR__),
    __DIR__ . '/../rsms',
    dirname(__DIR__) . '/rsms',
    '/home/amoleckc/rsms',
];

$project_path = null;
foreach ($candidates as $path) {
    if (is_file($path . '/vendor/autoload.php') && is_file($path . '/bootstrap/app.php')) {
        $project_path = $path;
        break;
    }
}

if (!$project_path) {
    http_response_code(500);
    echo 'Base path not found. Ensure the Laravel project (with vendor and bootstrap) exists.';
    exit;
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $project_path . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $project_path . '/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $project_path . '/bootstrap/app.php';

$app->handleRequest(Request::capture());
