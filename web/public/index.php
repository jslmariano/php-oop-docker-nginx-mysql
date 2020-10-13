<?php

include '../app/vendor/autoload.php';
include '../app/src/functions.php';

define('APP_ROOT', __DIR__ . '/../app/src');

$dispatcher = new App\Josel\Core\Dispatcher;
$dispatcher->dispatch();
$dispatcher->renderReponse();

?>