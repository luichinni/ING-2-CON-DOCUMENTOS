<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../utilities/bdController.php';

require_once __DIR__ . '/../models/valoracionDb.php';

$app->group('/public', function (RouteCollectorProxy $group) {

    $group->post('/newValoracion', function (Request $req, Response $res, $args) {
        global $valoracionesDB,$userDB;
        $msgReturn = ['Mensaje'=>'No se pudo valorar al usuario correctamente'];
        $status = 500;

        $bodyParams = $req->getParsedBody();

        if((!array_key_exists('userValorado',$bodyParams)) || (!array_key_exists('userValorador', $bodyParams))||(array_key_exists('userValorado',$bodyParams)&&!$userDB->exists(['username'=>$bodyParams['userValorado']])) || (array_key_exists('userValorador',$bodyParams)&& !$userDB->exists(['username' => $bodyParams['userValorador']]))){
            $res->getBody()->write(json_encode($msgReturn));
            return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        if (array_key_exists('puntos', $bodyParams) && !($bodyParams['puntos'] < 5 && $bodyParams['puntos'] >= 0)) {
            $msgReturn['Mensaje'] = 'Hubo un problema al procesar la valoracion';
            $res->getBody()->write(json_encode($msgReturn));
            return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        $pudo = $valoracionesDB->insert($bodyParams);

        if ($pudo){
            $status = 200;
            $msgReturn['Mensaje'] = 'Valorado correctamente';
        }

        $res->getBody()->write(json_encode($msgReturn));
        return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
    });

    $group->get('/getValoracion', function (Request $req, Response $res, $args) {
        $queryParams = $req->getQueryParams();

        global $valoracionesHandler;

        $msgReturn['Valoracion'] = $valoracionesHandler->valoracion($queryParams['userValorado']);

        $msgReturn['Mensaje'] = $valoracionesHandler->mensaje;

        $res->getBody()->write(json_encode($msgReturn));
        return $res->withStatus($valoracionesHandler->status)->withHeader('Content-Type', 'application/json');
    });

    $group->get('/estadisticas',function (Request $req,Response $res) {

    });
});