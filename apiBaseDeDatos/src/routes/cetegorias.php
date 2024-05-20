<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$camposCategorias = [
    'id' => 'int',
    'nombre' => 'varchar'
];

$categoriaDB = new bdController('categorias',$pdo,$camposCategorias);

//funcion de validaciones
function validaciones ($data, $response){
    $columna=['nombre'];
        //vemos si los campos no estan vacios
        foreach($columna as $colu){
            if (!isset($data[$colu]) || $data[$colu]===0){
/*                 $errorResponse = ['error' => 'El campo: ' . $colu . 'es necesario'];
                $response->getbody()->write (json_encode($errorResponse)); */
                return $response->withStatus(400);
            }
        }
        //vemos si el tamaño de los strings es correcto
        if (isset($data['nombre']) && strlen($data['nombre'] > 255)){
/*             $errorResponse = ['error' => 'el campo nombre no puede excedere los 255 caracteres'];
            $response->getBody()->write (json_encode($errorResponse)); */
            return $response->withStatus(500)->withHeader('Content-Type','application/json');
        }
        return null;
}


$app->group('/public', function (RouteCollectorProxy $group) use ($pdo) {
    $group->POST('/categorias', function ($request, $response, $args){
        global $categoriaDB;
        //traemos los paremetros y los asignamos a las variables
        $data = $request->getParsedBody(); 
        $status = 500;
        // revisamos si tienen todos los campos y hacemos las revisiones
        $validar = validaciones ($data,$request);

        if ($validar != null) return $validar;
        //preparamos la insersion
        
        if ($categoriaDB->exists($data)) return $response->withStatus($status)->withHeader('content-type', 'application/json');
        
        $status = $categoriaDB->insert($data) ? 200 : $status;

        return $response->withStatus($status)->withHeader('content-type', 'application/json');
    });

    $group->PUT('/categoria', function($request, Response $response,$args){
        global $categoriaDB;
        $status = 500;
        // preparamos para ver si existe una categoria con ese id

        $data = $request->getParsedBody();
        
        //revisamos si tiene todos los campos y hacemos validaciones
        $validar = validaciones($data,$request);
        if ($validar !== null) return $validar;

        if (!$categoriaDB->exists($data)) return $response->withStatus($status)->withHeader('content-type', 'application/json');
        
        $status = $categoriaDB->update($data) ? 200 : $status;

        return $response->withStatus($status)->withHeader('content-type', 'application/json');
    });

    $group->DELETE('/centros', function(Request $request, Response $response,$args){
        //
        //
        //---------FALTA VERIFICAR QUE NO HAYA PUBLICACIONES CON ESTA CATEGORIA!!!!
        //
        //
        global $categoriaDB, $publiDB;
        $status = 500;

        $data = $request->getQueryParams();

        if (!$categoriaDB->exists($data) || $publiDB->exists(array('categoria_id' => $data['id']))) return $response->withStatus($status)->withHeader('Content-type', 'application/json');

        $status = $categoriaDB->delete($data) ? 200 : $status;

        return $response->withStatus($status)->withHeader('Content-type','application/json');
    });
    
    $group->GET('/categoria', function($request, Response $response, $args) use ($pdo){
        global $categoriaDB;
        $data = $request->getQueryParams();  
        $status = 500;

        if (!$categoriaDB->exists($data)) return $response->withStatus($status)->withHeader('Content-Type', 'application/json');

        $retCat = $categoriaDB->getAll($data,true);
        $status = 200;

        $response->getBody()->write($retCat);
        return $response->withStatus($status)->withHeader('Content-Type','application/json');
    });

});

?>