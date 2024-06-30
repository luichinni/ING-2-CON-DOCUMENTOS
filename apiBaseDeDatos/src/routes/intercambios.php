<?php
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/notificaciones.php';

require_once __DIR__ . '/../models/intercambioDb.php';

$app->group('/public', function (RouteCollectorProxy $group) {

    $group->POST('/newIntercambio', function (Request $request, Response $response, $args) { //necesita en el body publicacionOferta, publicacionOfertada, centro, horario
        $bodyParams = (array) $request->getParsedBody();

        global $intercambioHandler;

        error_log(json_encode($bodyParams));

        $intercambioHandler->crear($bodyParams);

        $msgReturn['Mensaje'] = $intercambioHandler->mensaje;

        $response->getBody()->write(json_encode($msgReturn));
        return $response->withStatus($intercambioHandler->status)->withHeader('Content-Type', 'application/json');
    });

    $group->GET('/listarIntercambios', function (Request $request,Response $response, $args) {
        $queryParams = $request->getQueryParams();

        $like = (array_key_exists('like', $queryParams)) ? $queryParams['like'] : true;

        global $intercambioHandler;

        $listado = $intercambioHandler->listar($queryParams,$like);

        $listado['Mensaje'] = $intercambioHandler->mensaje;

        $response->getBody()->write(json_encode($listado));
        return $response->withStatus($intercambioHandler->status)->withHeader('Content-Type', 'application/json');
    });

    $group->put('/updateIntercambio', function (Request $req, Response $res){
        $bodyParams = (array) $req->getParsedBody();
        
        global $intercambioHandler;

        $intercambioHandler->actualizar($bodyParams);

        $res->getBody()->write(json_encode(['Mensaje'=>$intercambioHandler->mensaje]));
        return $res->withStatus($intercambioHandler->status)->withHeader('Content-Type', 'application/json');
    });

    $group->put('/validarIntercambio', function (Request $req, Response $res) {
        $bodyParams = (array) $req->getParsedBody();

        global $intercambioHandler;

        if (array_key_exists('setestado',$bodyParams)){
            if ($bodyParams['setestado']=='concretado') $intercambioHandler->validar($bodyParams);
            else if ($bodyParams['setestado']=='cancelado' && array_key_exists('motivo',$bodyParams)) $intercambioHandler->cancelar($bodyParams,$bodyParams['motivo']);
        }

        $res->getBody()->write(json_encode(['Mensaje' => $intercambioHandler->mensaje]));
        return $res->withStatus($intercambioHandler->status)->withHeader('Content-Type', 'application/json');
    });

    $group->put('/rechazarIntercambio', function (Request $req, Response $res) {
        $bodyParams = (array) $req->getParsedBody();

        global $intercambioHandler;

        $intercambioHandler->actualizar($bodyParams);

        $res->getBody()->write(json_encode(['Mensaje' => $intercambioHandler->mensaje]));
        return $res->withStatus($intercambioHandler->status)->withHeader('Content-Type', 'application/json');
    });
});

?>