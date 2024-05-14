<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


$app->group('/public' function (RouteCollectorProxy $group) use ($pdo) {
    $group->POST('/categorias', function ($request, $response, $args) use ($pdo){
        //traemos los paremetros y los asignamos a las variables
        $body = $request-> getBody() -> getContents();
        $data = json_decode($body, true);  

        $columna=['id','nombre'];
        
        foreach($columna as $colu){
            if (!isset($data[$colu] || $data[$colu]===0)){
                $errorResponse = ['error' => 'El campo: ' . $colu . 'es necesario'];
                $response->getbody()->write ($errorResponse);
                return $response->withStatus(400);
            }
        }
        if (isset($data['nombre']) && )



    });

});

?>