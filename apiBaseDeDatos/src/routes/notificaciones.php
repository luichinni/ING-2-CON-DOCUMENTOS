<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../models/notificacionDb.php';

function enviarNotificacion(string $user,string $titulo,string $contenido,string $url = ""){
    global $notificacionDB,$userDB,$mailer;
    error_log($user);
    $user = (array)($userDB->getFirst(['username'=>$user]))[0];

    if ($user['notificacion']) $mailer->send($user['mail'], $titulo, $contenido, true);

    return $notificacionDB->insert(['user'=>$user['username'],'texto'=>$contenido,'url'=>$url]);
}

function verNotificacion(int $id){
    global $notificacionDB;
    $notificacionDB->update(['id'=>$id,'setvisto'=>true]);
}

$app->group('/public', function (RouteCollectorProxy $group) {
    $group->GET('/listarNotificaciones', function (Request $request, Response $response, $args) {
        $queryParams = $request->getQueryParams();

        global $notificacionHandler;

        $listado = $notificacionHandler->listar($queryParams);

        $listado['Mensaje'] = $notificacionHandler->mensaje;

        $response->getBody()->write(json_encode($listado));
        return $response->withStatus($notificacionHandler->status)->withHeader('Content-Type', 'application/json');
    });
});