<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/models/db.php';
use Slim\Factory\AppFactory;

$app = AppFactory::create();
header("Access-Control-Allow-Origin: *");
header('access-control-allow-Methods: GET, POST, PUT, DELETE');
header('access-control-allow-Headers: Content-Type');

$app->addErrorMiddleware(true, true, true);

require __DIR__ . '/../src/routes/usuarios.php';

$app->run();

?>