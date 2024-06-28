<?php
require_once __DIR__ . '/src/utilities/bdController.php';
require __DIR__ . '/src/models/db.php';
require __DIR__ . '/src/models/userDb.php';
require __DIR__ . '/src/models/categoriaDb.php';
require __DIR__ . '/src/models/centroDb.php';
require __DIR__ . '/src/models/publiDb.php';
require __DIR__ . '/src/models/publiCentroDb.php';
require __DIR__ . '/src/models/imgDb.php';
require __DIR__ . '/src/models/centroVolunDb.php';
require __DIR__ . '/src/models/comentariosDb.php';
require __DIR__ . '/src/models/intercambioDb.php';
require __DIR__ . '/src/models/notificacionDb.php';
require __DIR__ . '/src/models/valoracionDb.php';

try{
    $userDB->insert(['username' => 'admin', 'clave' => 'admin', 'nombre' => 'admin', 'apellido' => 'admin', 'dni' => '0', 'mail' => 'admin@admin.com', 'telefono' => '0', 'rol' => 'admin']);
}catch(Exception $e){
    error_log('Ya hay admin');
}
$categoriaDB;
$centroDB;
$publiDB;
$publiCentroDB;
$imgDB;
$centroVolunDB;
$comentariosDB;
$intercambioDB;
$notificacionDB;
$valoracionesDB;

error_log('Tablas iniciadas');