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
        /*
        'ausencia ambas partes',
        'ausencia anunciante',
        'ausencia ofertante',
        'producto anunciado no es lo esperado',
        'producto ofertado no es lo esperado',
        'se eligió una oferta superadora',
        'el producto no es de interes',
        'fecha y hora no convenientes'
        */
        // Nota para el futuro, esta es la forma mas ineficiente de hacerlo, lo mejor seria desde las querys sql
        $queryParams = $req->getQueryParams();

        $desde = '2024-01-01 00:00:00';
        $hasta = date('Y-m-d h:i:s');

        if (array_key_exists('desde', $queryParams)) $desde = $queryParams['desde'];
        if (array_key_exists('hasta', $queryParams)) $hasta = $queryParams['hasta'];

        global $estadisticador;
        $retorno = [];

        $retorno['ausencia ambas partes'] = $estadisticador->totalDe('ausencia ambas partes', $queryParams, 'cancelado', $desde, $hasta);

        $retorno['ausencia anunciante'] = $estadisticador->totalDe('ausencia anunciante', $queryParams, 'cancelado', $desde, $hasta);

        $retorno['ausencia ofertante'] = $estadisticador->totalDe('ausencia ofertante', $queryParams, 'cancelado', $desde, $hasta);

        $retorno['producto anunciado no es lo esperado'] = $estadisticador->totalDe('producto anunciado no es lo esperado', $queryParams, 'rechazado', $desde, $hasta);

        $retorno['producto ofertado no es lo esperado'] = $estadisticador->totalDe('producto ofertado no es lo esperado', $queryParams, 'rechazado', $desde, $hasta);

        $retorno['el producto no es de interes'] = $estadisticador->totalDe('el producto no es de interes', $queryParams, 'rechazado', $desde, $hasta);

        $retorno['fecha y hora no convenientes'] = $estadisticador->totalDe('fecha y hora no convenientes', $queryParams, 'rechazado', $desde, $hasta);

        $retorno['se eligió una oferta superadora'] = $estadisticador->totalDe('se eligió una oferta superadora', $queryParams, 'cancelado', $desde, $hasta);

        $retorno['concretado'] = $estadisticador->totalDe('', $queryParams, 'concretado', $desde, $hasta);

        $queryParams['donacion'] = 1;
        $retorno['concretado con donacion'] = $estadisticador->totalDe('', $queryParams, 'concretado', $desde, $hasta);

        error_log('Estadisticas: '.json_encode($retorno));

        $res->getBody()->write(json_encode($retorno));
        return $res->withStatus(200)->withHeader('Content-Type', 'application/json');
    });
});