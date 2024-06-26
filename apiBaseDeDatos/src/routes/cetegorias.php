<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../utilities/bdController.php';

/*
CREATE TABLE Categoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255)
);
*/

require_once __DIR__ . '/../models/categoriaDb.php';

// LE TUVE QUE SACAR ESTO PORQ DABA ERROR, LUEGO HAY QUE MIRARLO
//funcion de validaciones
/* function validaciones ($data, $response){
    $columna=['nombre'];
        //vemos si los campos no estan vacios
        foreach($columna as $colu){
            if (!isset($data[$colu]) || $data[$colu]===0){
/*                 $errorResponse = ['error' => 'El campo: ' . $colu . 'es necesario'];
                $response->getbody()->write (json_encode($errorResponse));
                return $response->withStatus(400);
            }
        }
        //vemos si el tamaño de los strings es correcto
        if (isset($data['nombre']) && strlen($data['nombre'] > 255)){
/*             $errorResponse = ['error' => 'el campo nombre no puede excedere los 255 caracteres'];
            $response->getBody()->write (json_encode($errorResponse));
            return $response->withStatus(500)->withHeader('Content-Type','application/json');
        }
        return null;
} */


$app->group('/public', function (RouteCollectorProxy $group) use ($pdo) {
    $group->POST('/newCategoria', function ($request, $response, $args){
        global $categoriaDB;
        //traemos los paremetros y los asignamos a las variables
        $data = $request->getParsedBody(); 
        $status = 500;
        $msgReturn = ['Mensaje'=>'Ya existe una categoria con ese nombre'];
        // revisamos si tienen todos los campos y hacemos las revisiones
        //$validar = validaciones ($data,$request);

        //if ($validar != null) return $validar;
        //preparamos la insersion
        
        if ($categoriaDB->exists($data)) {
            $response->getBody()->write(json_encode($msgReturn));
            return $response->withStatus($status)->withHeader('content-type', 'application/json');
        }

        $status = $categoriaDB->insert($data) ? 200 : $status;

        $msgReturn['Mensaje'] = ($status == 200) ? 'Categoria cargada con éxito' : 'Ocurrió un error al cargar la categoria';

        $response->getBody()->write(json_encode($msgReturn));

        return $response->withStatus($status)->withHeader('content-type', 'application/json');
    });

    $group->PUT('/updateCategoria', function($request, Response $response,$args){
        global $categoriaDB;
        $status = 500;
        $msgReturn = ['Mensaje'=>'No existe la publicación que se intentó modificar'];
        // preparamos para ver si existe una categoria con ese id

        $data = $request->getParsedBody();
        
        //revisamos si tiene todos los campos y hacemos validaciones
        //$validar = validaciones($data,$request);
        //if ($validar !== null) return $validar;

        if (!$categoriaDB->exists($data)) {
            $response->getBody()->write(json_encode($msgReturn));
            return $response->withStatus($status)->withHeader('content-type', 'application/json');
        }

        $status = $categoriaDB->update($data) ? 200 : $status;

        $msgReturn['Mensaje'] = ($status == 200) ? 'Publicación modificada con éxito' : 'Ocurrió un erro al modificar la publicación';

        $response->getBody()->write(json_encode($msgReturn));

        return $response->withStatus($status)->withHeader('content-type', 'application/json');
    });

    $group->DELETE('/deleteCategoria', function(Request $request, Response $response,$args){
        global $categoriaDB, $publiDB;
        $status = 500;
        $msgReturn = ['Mensaje' => 'No se pudo eliminar la categoria, verifique el nombre y si no tiene publicaciones dependendientes'];

        $data = $request->getQueryParams();

        if (!$categoriaDB->exists($data) || $publiDB->exists(array('categoria_id' => $data['id']))) {
            $response->getBody()->write(json_encode($msgReturn));
            return $response->withStatus($status)->withHeader('Content-type', 'application/json');
        }

        $status = $categoriaDB->delete($data) ? 200 : $status;

        $msgReturn['Mensaje'] = ($status == 200) ? 'Categoria eliminada con éxito' : 'Ocurrió un error al eliminar la categoria';

        $response->getBody()->write(json_encode($msgReturn));

        return $response->withStatus($status)->withHeader('Content-type','application/json');
    });
    
    $group->GET('/listarCategorias', function($request, Response $response, $args) use ($pdo){
        global $categoriaDB;
        $data = $request->getQueryParams();  
        $status = 500;
        $msgReturn = ['Mensaje' => 'No existen categorias'];

        if (!$categoriaDB->exists($data,true)) {
            $response->getBody()->write(json_encode($msgReturn));
            return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        $retCat = $categoriaDB->getAll($data,true);
        $status = 200;

        $retCat['Mensaje'] = 'Categorias listadas con éxito';

        $response->getBody()->write(json_encode($retCat));
        return $response->withStatus($status)->withHeader('Content-Type','application/json');
    });
});

?>