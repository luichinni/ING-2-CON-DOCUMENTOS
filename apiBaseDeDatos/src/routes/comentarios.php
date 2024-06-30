<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../utilities/bdController.php';

require_once __DIR__ . '/../models/comentariosDb.php';

$app->group('/public', function (RouteCollectorProxy $group) {

    $group->post('/newComentario', function (Request $req, Response $res, $args){
        $bodyParams = (array) $req->getParsedBody();

        global $comentariosHandler;

        $comentariosHandler->crear($bodyParams);
        
        $res->getBody()->write(json_encode(['Mensaje'=>$comentariosHandler->mensaje]));
        return $res->withStatus($comentariosHandler->status)->withHeader('Content-Type', 'application/json');
    });

    $group->get('/listarComentarios', function (Request $req, Response $res, $args){
        $queryParams = (array) $req->getQueryParams();

        global $comentariosHandler;

        $ret = $comentariosHandler->listar($queryParams);

        $ret['Mensaje'] = $comentariosHandler->mensaje;

        $res->getBody()->write(json_encode($ret));
        return $res->withStatus($comentariosHandler->status)->withHeader('Content-Type', 'application/json');
    });

    $group->delete('/deleteComentario', function (Request $req, Response $res, $args) {
        $queryParams = (array) $req->getQueryParams();

        global $comentariosHandler;

        $comentariosHandler->borrar($queryParams);

        $res->getBody()->write(json_encode(['Mensaje'=>$comentariosHandler->mensaje]));
        return $res->withStatus($comentariosHandler->status)->withHeader('Content-Type', 'application/json');
    });

    $group->put('/updateComentario', function (Request $req, Response $res){
        $bodyParams = (array) $req->getParsedBody();
        
        global $comentariosHandler;

        $comentariosHandler->actualizar($bodyParams);
        
        $res->getBody()->write(json_encode(['Mensaje'=>$comentariosHandler->mensaje]));
        return $res->withStatus($comentariosHandler->status)->withHeader('Content-Type', 'application/json');
    });
});