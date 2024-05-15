<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response as Psr7Response;

$camposSesion = [
    "user" => "varchar",
    "token" => "varchar",
    "nombre" => "varchar",
    "ultimaAccion" => "timestamp",
    "fechaInicio" => "timestamp"
];

$app->group('/public', function (RouteCollectorProxy $group) use ($pdo, $camposSesion) {
    //obtener usuario
    $group->post('/crearSesion', function (Request $req, Response $res, $args) use ($pdo, $camposSesion) {
        $queryParams = $req->getParsedBody();
        $queryParams = $queryParams == null ? [] : $queryParams;
        $return = [
            'Error' => 'user o clave invalido'
        ];
        $status = 404;
        if (array_key_exists('username',$queryParams) && array_key_exists('clave', $queryParams)){
            $return = [
                'token' => 'est035Un7ok3nDeV34dadCreeme'
            ];
            $status = 200;
        }

        /*if (array_key_exists('user',$queryParams)){
            if (existeUsuario(array('username'=>$queryParams['user']),$pdo,array('username'=>'varchar'))){
                $return = obtenerUsuario(array('username' => $queryParams['user']), $pdo, array('username' => 'varchar'));
            }
        }*/
        $res->getBody()->write(json_encode($return));
        return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
    });
});
