<?php

use App\Core\Router;
use App\Core\Template;

require_once __DIR__ . '/app/bootstrap.php';
Template::setDefaultViewDirectory(__DIR__ . '/views');

$router = new Router();

$router->addRoute('GET', '/', function () {
    echo Template::make('hello.php')->render();
});

if (!$router->dispatch()) {
    http_response_code(404);
    echo '404 Not Found';
}
