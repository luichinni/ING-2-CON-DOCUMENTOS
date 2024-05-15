<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//funcion de validaciones
function validaciones ($data, $response){
    $columna=['id','nombre'];
        //vemos si los campos no estan vacios
        foreach($columna as $colu){
            if (!isset($data[$colu]) || $data[$colu]===0){
                $errorResponse = ['error' => 'El campo: ' . $colu . 'es necesario'];
                $response->getbody()->write (json_encode($errorResponse));
                return $response->withStatus(400);
            }
        }
        //vemos si el tamaño de los strings es correcto
        if (isset($data['nombre']) && strlen($data['nombre'] > 255)){
            $errorResponse = ['error' => 'el campo nombre no puede excedere los 255 caracteres'];
            $response->getBody()->write (json_encode($errorResponse));
            return $response->withStatus(400)->withHeader('Content-Type','application/json');
        }
}


$app->group('/public', function (RouteCollectorProxy $group) use ($pdo) {
    $group->POST('/categorias', function ($request, $response, $args) use ($pdo){
        //traemos los paremetros y los asignamos a las variables
        $body = $request-> getBody() -> getContents();
        $data = json_decode($body, true);  
        // revisamos si tienen todos los campos y hacemos las revisiones
        $validar = validaciones ($data,$request);
        if($validar != null){
            return $validar;
        }
        //preparamos la insersion
        
        $stmt= $pdo->prepare('INSERT INTO centros (id, nombre) VALUES (:id, :nombre)');

        //asociamos valores
        $stmt->bindParam (':id',$data['id']);
        $stmt->bindParam (':nombre',$data['nombre']);

        //Ejecutamos y devolvemos mensaje de exito o error
        try{
            $stmt->execute();
        } catch (Exception $e) {
            $errorResponse = ['error' => 'El error en la ejecucion fue: '. $e->getMessage()];
            $response->getBody()->write  (json_encode($errorResponse));
            return $response->withStatus(500)->withHeader('Content-Type','application/json');
        }
        //preparamos el mensaje

        $categorias =[
            'id' => $data['id'],
            'nombre' => $data['nombre']
        ];
        //enviamos mensaje
        $response ->getBody()->write(json_encode($categorias));
        return $response->withStatus(200)->withHeader('content-type', 'application/json');
    });

    $group->PUT('/categoria/{id}', function($request, Response $response,$args) use ($pdo){
        $id = $args['id'];
        // preparamos para ver si existe una categoria con ese id
        $stmt = $pdo-> prepare('SELECT COUNT(+) FROM categorias WHERE id = :id');
        $stmt->bindParam(':id',$id);
        $stmt->execute();
        $existe = $stmt->fetchColumn();
        // si el fetchColum es 0 informar error de que no hay categoria con ese id
        if ($existe == 0){
            $errorResponse= ['error'=>'No existen centros con ese ID'];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(400)->withHeader('Content-Type','application/json');
        }
        $body = $request->getBody()->getContents();
        $data = json_decode($body,true);
        
        //revisamos si tiene todos los campos y hacemos validaciones
        $validar = validaciones($data,$request);
        if ($validar !== null){
            return $validar;
        }
        //preparamos la modificacion
        $stmt = $pdo->prepare('UPDATE categorias SET
                                id = :id,
                                nombre = :nombre
                            ');
        // asociamos valores
        $stmt->bindParam(':id',$data['id']);
        $stmt->bindParam(':nombre',$data['nombre']);
        //ejecutamos y manejamos errores
        try{
            $stmt->execute();
        } catch (Exception $e) {
            $errorResponse = ['error' => 'El error en la ejecucion fue: '. $e->getMessage()];
            $response->getBody()->write  (json_encode($errorResponse));
            return $response->withStatus(500)->withHeader('Content-Type','application/json');
        }
        //preparamos el mensaje de exito
        $categorias =[
            'id' => $data['id'],
            'nombre' => $data['nombre']
        ];
        //enviamos el mensaje
        $response ->getBody()->write(json_encode($categorias));
        return $response->withStatus(200)->withHeader('content-type', 'application/json');
    });

    $group->DELETE('/categoria/{id}', function(Request $request, Response $response,$args) use ($pdo){
        //
        //
        //---------FALTA VERIFICAR QUE NO HAYA PUBLICACIONES CON ESTA CATEGORIA!!!!
        //
        //
        $id = $args['id'];
        //nos fijamos si existe una categoria con el id enviado
        $stmt = $pdo->prepare('SELECT COUNT(+) FROM categorias where id = :id');
        $stmt->bindParam(':id',$id);
        $stmt->execute();
        $existe = $stmt->fetchColum();
        //si el conteo de columnas de 0 enviamos el error
        if ($existe == 0){
            $errorResponse = ['error'=>'No existe ninguna categoria con esa ID'];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        //preparamos la consulta
        $stmt = $pdo->prepare('DELETE FROM centros where id =:id');
        $stmt->bindParam(':id',$id);
        //ejecutamos o enviamos el error
        try{
            $stmt->execute();
        } catch (PDOException $e){
            $errorResponse = ['error'=>'El error en la consulta fue: ' . $e->getMessage() ];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(500)->withHeader('Content-type','application/json');
        }

        $response->getBody()->write(json_encode(['messaje'=>'centro eliminado con exito']));
        return $response->withStatus(200)->withHeader('Content-type','application/json');
        });
    
    $group->GET('/categoria', function($request, Response $response, $args) use ($pdo){
        $nombre = $request->getQueryParams()['nombre']??null;

        //preparamos la consulta sin parametros y para agregar parametros
        $consulta = 'SELECT * FROM categorias';
        $parametro = [];
        //si busca pos nombre lo concatenamos
        if ($nombre != null){
            $consulta .= 'where nombre LIKE :nombre';
            $parametro ['nombre'] = "%{$nombre}%";
        }
        $stmt = $pdo->prepare($consulta);
        //intentamos ejecutar o mandamos el error
        try{
            $stmt->execute($parametro);
        } catch (Exception $e) {
            $errorResponse = ['error' => 'error al ejecutar la consulta: ' . $e->getMessage()];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(500)->withheader('Content-Type','application/json');
        }
        $centros = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //enviamos respuesta
        $response->getBody()->write(json_encode($centros));
        return $response->withStatus(200)->withHeader('Content-Type','application/json');
    });

});

?>