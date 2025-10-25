<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// The path to your Laravel project directory (one level up from public_html)
$project_path = __DIR__ . '/../rsms';

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
