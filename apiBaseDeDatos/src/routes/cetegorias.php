<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../utilities/bdController.php';

require_once __DIR__ . '/../models/categoriaDb.php';

$app->group('/public', function (RouteCollectorProxy $group) use ($pdo) {
    $group->POST('/newCategoria', function ($request, $response, $args){
        $data = $request->getParsedBody(); 
        
        global $categoriasHandler;

        $categoriasHandler->crear($data);

        $response->getBody()->write(json_encode(['Mensaje'=>$categoriasHandler->mensaje]));

        return $response->withStatus($categoriasHandler->status)->withHeader('content-type', 'application/json');
    });

    $group->PUT('/updateCategoria', function($request, Response $response,$args){
        $data = $request->getParsedBody();
        
        global $categoriasHandler;

        $categoriasHandler->actualizar($data);

        $response->getBody()->write(json_encode(['Mensaje'=>$categoriasHandler->mensaje]));

        return $response->withStatus($categoriasHandler->status)->withHeader('content-type', 'application/json');
    });

    $group->DELETE('/deleteCategoria', function(Request $request, Response $response,$args){
        $data = $request->getQueryParams();

        global $categoriasHandler;

        $categoriasHandler->borrar($data);

        $response->getBody()->write(json_encode(['Mensaje'=>$categoriasHandler->mensaje]));

        return $response->withStatus($categoriasHandler->status)->withHeader('Content-type','application/json');
    });
    
    $group->GET('/listarCategorias', function($request, Response $response, $args) use ($pdo){
        $data = $request->getQueryParams();

        $like = (array_key_exists('like', $data)) ? $data['like'] : true;

        global $categoriasHandler;
        
        $retCat = $categoriasHandler->listar($data,$like);

        $retCat['Mensaje'] = $categoriasHandler->mensaje;

        $response->getBody()->write(json_encode($retCat));
        return $response->withStatus($categoriasHandler->status)->withHeader('Content-Type','application/json');
    });
});

?>