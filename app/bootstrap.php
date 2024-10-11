<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helpers.php';

use Illuminate\Database\Capsule\Manager as Capsule;

error_reporting(E_ALL | E_STRICT);
$callback = function () {
    if (php_sapi_name() !== 'cli') {
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();
    }

    $capsule = new Capsule;
    $appConfig = require(__DIR__ . '/../config/app.php');
    date_default_timezone_set($appConfig['timezone']);

    $databaseConfig = require(__DIR__ . '/../config/database.php');
    $capsule->addConnection($databaseConfig['connections'][$databaseConfig['default']]);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();
};
$callback();
