<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


$camposSesion = [
    "user" => [
        "pk" => true,
        "tipo" => "varchar(50)",
        "comparador" => "like",
        "opcional" => false,
    ],
    "token" => [
        "tipo" => "varchar(50)",
        "comparador" => "like",
        "opcional" => false,
    ]
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
        if (array_key_exists('user',$queryParams) && array_key_exists('clave', $queryParams) && $userDB->exists(array('user' => $queryParams['user'],'clave'=>$queryParams['clave']))){
            $user = (array) $userDB->getFirst(array('user' => $queryParams['user']));
            $userRol = (array) $user[0];
            $userRol = $userRol['rol'];
            $tok = "token" . strtoupper($userRol[0]) . substr($userRol, 1);
            $return = [
                'token' => $tok
            ];
            $status = 200;
        }

        /*if (array_key_exists('user',$queryParams)){
            if (existeUsuario(array('user'=>$queryParams['user']),$pdo,array('user'=>'varchar'))){
                $return = obtenerUsuario(array('user' => $queryParams['user']), $pdo, array('user' => 'varchar'));
            }
        }*/
        $res->getBody()->write(json_encode($return));
        return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
    });
});
