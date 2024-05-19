<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/models/db.php';
use Slim\Factory\AppFactory;

$app = AppFactory::create();
header("Access-Control-Allow-Origin: *");
header('access-control-allow-Methods: GET, POST, PUT, DELETE');
header('access-control-allow-Headers: Content-Type');

$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

require __DIR__ . '/../src/routes/usuarios.php';
require __DIR__ . '/../src/routes/sesionActiva.php';
require __DIR__ . '/../src/routes/centros.php';
require __DIR__ . '/../src/routes/centroVolun.php';
require __DIR__ . '/../src/routes/publi_centro.php';
require __DIR__ . '/../src/routes/cetegorias.php';
require __DIR__ . '/../src/routes/imagen.php';
require __DIR__ . '/../src/routes/publicacion.php';
require __DIR__ . '/../src/routes/middlewares.php';

$app->run();
?>