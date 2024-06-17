<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../utilities/bdController.php';
// son obligatorios:
// userValorado 
// userValorador
// puntos que es un float ej: 3.3
// es opcional el "respondeA" que es id del comentario al que responde

$camposValoraciones = [
    'id' => '?int',
    'userValorado' => 'varchar',
    'userValorador' => 'varchar',
    'puntos' => 'float',
    'fecha' => '?datetime',
    'fecha_modificado' => '?datetime'
];

$valoracionesDB = new bdController('valoraciones', $pdo, $camposValoraciones);

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
        global $valoracionesDB, $userDB;
        $msgReturn = ['Mensaje' => 'El usuario no ha sido valorado nunca'];
        $msgReturn['Valoracion'] = 0;
        $status = 500;

        $queryParams = $req->getQueryParams();

        if (!array_key_exists('userValorado', $queryParams) || !$userDB->exists(['username'=>$queryParams['userValorado']]) || !$valoracionesDB->exists($queryParams)) {
            $res->getBody()->write(json_encode($msgReturn));
            return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        $valoraciones = (array)json_decode($valoracionesDB->getAll($queryParams));

        $total = 0;

        foreach($valoraciones as $key => $valoracion){
            $valoracion = (array) $valoracion;
            $total += $valoracion['puntos'];
        }

        $total = $total / count($valoraciones);

        $msgReturn['Mensaje'] = 'Valoraciones contadas con éxito';
        $msgReturn['Valoracion'] = $total;

        $res->getBody()->write(json_encode($msgReturn));
        return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
    });

});