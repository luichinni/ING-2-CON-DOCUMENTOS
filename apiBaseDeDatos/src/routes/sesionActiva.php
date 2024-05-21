<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../utilities/bdController.php';

$camposSesion = [
    "user" => "varchar",
    "token" => "?varchar",
    "nombre" => "varchar",
    "ultimaAccion" => "timestamp",
    "fechaInicio" => "timestamp"
];

$app->group('/public', function (RouteCollectorProxy $group) use ($pdo, $camposSesion) {
    //obtener usuario
    $group->post('/crearSesion', function (Request $req, Response $res, $args) {
        global $userDB;
        $queryParams = $req->getParsedBody();
        $queryParams = $queryParams == null ? [] : $queryParams;
        $return = [
            'Mensaje' => 'user o clave invalido'
        ];
        $status = 404;
        if (array_key_exists('username',$queryParams) && array_key_exists('clave', $queryParams) && $userDB->exists(array('username' => $queryParams['username']))){
            $user = json_decode($userDB->getFirst(array('username' => $queryParams['username'])));
            $tok = "token" . strtoupper($user['rol'][0]) . substr($user['rol'], 1);
            $return = [
                'token' => $tok
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
