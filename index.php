<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Locate Laravel Project Root
|--------------------------------------------------------------------------
| Tunatafuta directory halisi yenye 'vendor' na 'bootstrap/app.php'.
| Kwa kawaida, hii file (index.php) huwa ipo ndani ya 'public_html'
| wakati project yenyewe ipo moja juu yake (mfano: /home/user/rsms).
*/

$candidates = [
    dirname(__DIR__),                     // moja juu ya public_html
    dirname(__DIR__) . '/rsms',           // au kwenye /rsms
    '/home/amoleckc/rsms',                // path kamili kwenye cPanel
];

$project_path = null;

foreach ($candidates as $path) {
    if (file_exists($path . '/vendor/autoload.php') && file_exists($path . '/bootstrap/app.php')) {
        $project_path = $path;
        break;
    }
}

if (!$project_path) {
    http_response_code(500);
    echo "<h2 style='font-family:sans-serif;color:#c00;'>⚠️ Base path not found.</h2>";
    echo "<p>Ensure the Laravel project (with <code>vendor</code> and <code>bootstrap</code>) exists in one of these locations:</p><ul>";
    foreach ($candidates as $p) {
        echo "<li>$p</li>";
    }
    echo "</ul>";
    exit;
}

/*
|--------------------------------------------------------------------------
| Maintenance Mode
|--------------------------------------------------------------------------
*/
if (file_exists($project_path . '/storage/framework/maintenance.php')) {
    require $project_path . '/storage/framework/maintenance.php';
}

/*
|--------------------------------------------------------------------------
| Register Composer Autoloader
|--------------------------------------------------------------------------
*/
require $project_path . '/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Bootstrap Laravel & Handle Request
|--------------------------------------------------------------------------
*/
$app = require_once $project_path . '/bootstrap/app.php';

$app->handleRequest(Request::capture());
