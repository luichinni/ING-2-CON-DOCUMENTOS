<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$camposNotificacion = [
    'user'=> [
        "tipo" => "varchar(50)",
        "comparador" => "like",
        "opcional" => false,
        "fk" => [
            "tabla" => "usuarios",
            "campo" => "user"
        ]
    ],
    'texto'=> [
        "tipo" => "text",
        "comparador" => "like",
        "opcional" => false
    ],
];

$notificacionDB = new bdController('notificacion',$pdo,$camposNotificacion);

function enviarNotificacion(string $user,string $contenido){
    global $notificacionDB;
    return $notificacionDB->insert(['user'=>$user,'texto'=>$contenido]);
}

$app->group('/public', function (RouteCollectorProxy $group) {
    $group->GET('/listarNotificaciones', function (Request $request, Response $response, $args) {
        global $notificacionDB;
        $status = 404;
        $msgReturn = ['Mensaje' => 'No hay notificaciones disponibles'];

        $queryParams = $request->getQueryParams();

        $listado = (array) json_decode($notificacionDB->getAll($queryParams));

        $listado['Mensaje'] = (!empty($listado)) ? 'Notificaciones listadas con exito' : $msgReturn['Mensaje'];

        $status = (!empty($listado)) ? 200 : 404;

        $response->getBody()->write(json_encode($listado));
        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    });
});