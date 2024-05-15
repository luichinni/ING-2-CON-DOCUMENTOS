<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

function validaDatos($data, $response){
    $columna = ['id', 'nombre', 'direccion', 'hora_abre', 'hora_cierra'];

    foreach ($columna as $colum){
        if (!isset($data[$colum])||$data[$colum]===0){
            $errorResponse = ['error'=> 'el campo: ' . $colum . ' es requerido'];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    }
    //revisamos la cantidad de carcteres
    $revisarCar = ['nombre', 'direccion'];
    foreach ($revisarCar as $revi){
        if (isset($data[$revi]) && strlen($data[$revi]) > 255) {
            $errorResponse=['error'=>'el campo' . $revi . 'excede los 255 caracteres permitidos'];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(400);
        }
    }
    //asignamos variables para las verificaciones de horario, para que sea mas facil manippularlas.
    $hora_abre = $data['hora_abre'];
    $hora_cierra = $data ['hora_cierra'];
    // vemos si esta en el formato correcto HH:MM y entre 00:00 y 23:59
    if (!preg_match('/^(?:2[0-3]|[0-1][0-9]):[0-5][0-9]$/', $hora_abre) || !preg_match('/^(?:2[0-3]|[0-1][0-9]):[0-5][0-9]$/', $hora_cierra)) {
        $errorResponse=['error'=>'los campos de apertura y cierre deben estar en formato HH:MM, y estar entre los valores 00:00hs y 23:59hs'];
        $response->getBody()->write(json_encode($errorResponse));
        return $response->withStatus(400);
    }
    // vemos si la hora de apertura es menor a la de cierre
    if ($hora_abre >= $hora_cierra){
        $errorResponse=['error'=>'El horario de apertura debe ser menor a el horario de cierre'];
        $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(400);
    }
}

$app->group('/public', function (RouteCollectorProxy $group) use ($pdo) {
    // hacemos el POST-Create
    $group->POST('/centros', function ($request, $response, $args) use ($pdo){
        //traemos los valores en parametros y los asignamos a variables
        $body = $request-> getBody() -> getContents();
        $data = json_decode($body, true);
        // revisamos si tiene todos los campos y hacemos validaciones
        $validacion = validaDatos($data,$response);
        if ($validacion !== null){
            return $validacion;
        }
        // preparamos la insersion
        $stmt = $pdo-> prepare('INSERT INTO centros (id, nombre, direccion, hora_abre, hora_cierra) 
                                VALUES (:id, :nombre, :direccion, :hora_abre, :hora_cierra)');
        //asociamos valores
        $stmt->bindparam(':id', $data['id']);
        $stmt->bindparam(':nombre', $data['nombre']);
        $stmt->bindparam(':direccion', $data['direccion']);
        $stmt->bindparam(':hora_abre', $data['hora_abre']);
        $stmt->bindparam(':hora_cierra', $data['hora_cierra']);
        //ejecutamos o tratamos el error
        try{
            $stmt->execute();
        } catch (Exception $e) {
            $errorResponse = ['error' => 'error al ejecutar la consulta: ' . $e->getMessage()];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(500)->withheader('Content-Type','application/json');
        }
        //preparamos el mensaje de retorno
        $centro = [
            'id' => $data ['id'],
            'nombre' => $data ['nombre'],
            'direccion' => $data ['direccion'],
            'hora_abre' => $data ['hora_abre'],
            'hora_cierra' => $data ['hora_cierra']
        ];
        // enviamos mensaje
        $response->getBody()->write(json_encode($centro));
        return $response->withStatus(200)->withHeader('Content-Type','application/json');
    });
    $group->PUT('/centros/{id}', function($request, Response $response, $args) use ($pdo){
        $id = $args['id'];
        //preparamos para ver si existe un centro con ese id
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM centros WHERE id = :id ');
        $stmt->bindParam(':id',$id);
        $stmt->execute();
        $existe = $stmt->fetchColumn();
        //si el fechtcolum da 0, enviamos mensaje de error que no hay centro con ese id
        if ($existe == 0) {
            $errorResponse = ['El codigo seleccionado no corresponde a un ningun centro'];
            $response->getBody()->write (json_encode($errorResponse));
            return $response->withStatus(400)->withHeader('Content-Type','application/json');
        } else {
            $body = $request->getBody()->getContents();
            $data = json_decode($body,true);

        // revisamos si tiene todos los campos y hacemos validaciones
            $validacion = validaDatos($data,$response);
            if ($validacion !== null){
                return $validacion;
            }
        }
        // preparamos la modificacion
        $stmt = $pdo-> prepare('UPDATE centros SET
                                id = :id,
                                nombre = :nombre,
                                direccion = :direccion,
                                hora_abre = :hora_abre,
                                hora_cierra = hora_cierra
                                ');
        //asociamos valores
        $stmt->bindparam(':id', $data['id']);
        $stmt->bindparam(':nombre', $data['nombre']);
        $stmt->bindparam(':direccion', $data['direccion']);
        $stmt->bindparam(':hora_abre', $data['hora_abre']);
        $stmt->bindparam(':hora_cierra', $data['hora_cierra']);
        //ejecutamos
        try{
        $stmt->execute();
        } 
        catch (PDOException $e){
            $errorResponse = ['error'=>'Error de la consulta: '. $e->getMessage()];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(500)->withHeader('Content-Type','application/json');  
        }
        //preparamos mensaje de retorno exitoso y lo enviamos
        $centros =[
            'id' => $id,
            'nombre' => $data['nombre'],
            'direccion'=> $data['direccion'],                                              
            'hora_abre'=> $data['hora_abre'],
            'hora_cierra'=> $data['hora_cierra']
        ];
        $response->getBody()->write(json_encode($centros));
        return $response->withStatus(200)->withHeader('Content-Type','application/json');
    });

    $group->DELETE('/centros/{id}', function (Request $request, Response $response, $args) use ($pdo){
        //
        //
        //---------FALTA VERIFICAR QUE NO HAYA VOLUNTARIOS EN ESTE CENTRO!!!!
        //---------QUE NO HAYA PUBLICACIONES CON ESTE CENTROO?????
        //
        //

        //tomamos id de los parametros, ejecutamos la consulta y contamos las columnas que coinciden con la busqueda
        $id = $args['id'];
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM centros where id = :id');
        $stmt->bindParam(':id',$id);
        $stmt->execute();
        $existeCentro= $stmt->fechtcolum();
        // si es 0 mandamos el error, de que no existe centro con ese id
        if ($existeCentro == 0){
            $errorResponse = ['error' => 'No existe un centro con ese id'];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(400)->withHeader('content-Type', 'application/json');
        // si hay resultados con ese id
        } else {
            //preparamos la eliminacion con ese id
            $stmt = $pdo->prepare('DELETE FROM centros WHERE id = :id');
            $stmt->bindParam(':id',$id);
            //ejecutamos o tratamos el error
            try{
                $stmt->execute();
            } catch (Exception $e) {
                $errorResponse = ['error' => 'error al ejecutar la consulta: ' . $e->getMessage()];
                $response->getBody()->write(json_encode($errorResponse));
                return $response->withStatus(500)->withheader('Content-Type','application/json');
            }
            //enviamos mensaje de exito
            $response->getBody()->write(json_encode(['messaje' => 'centro eliminado con exito']));
            return $response->withStatus(200)->withHeader('Content-type','application/json');
        }
    });

    $group->GET('/centros', function($request, Response $response, $args) use ($pdo){
        //preparamos los where de la consulta
        //si tiene parametros en la columna nombre, le asigna lo que viene, sino null
        $nombre = $request->getQueryParams()['nombre']?? null;
        $direccion = $request->getQueryParams()['direccion']??null;

        //preparamos la consulta sin condiciones y creamos vectores para sumar las condiciones
        $consulta = 'SELECT * FROM centros';
        $condiciones = [];
        $parametros =[];

        // concatenamos resultados de los parametros
        if ($nombre != null){
            $condiciones[]= 'nombre LIKE :nombre';
            $parametros['nombre']= "%{$nombre}%";     
        }
        if ($direccion != null){
            $direccion[]= 'direccion LIKE :direccion';
            $parametros['direccion']= "%{$direccion}%";
        }
        if (!empty($condiciones)){
            $consulta .= ' WHERE ' . implode(' AND ', $condiciones); 
        }
        $stmt = $pdo->prepare($consulta);
        //ejecutamos o tratamos el error
        try{
            $stmt->execute($parametros);
        } catch (Exception $e) {
            $errorResponse = ['error' => 'error al ejecutar la consulta: ' . $e->getMessage()];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(500)->withheader('Content-Type','application/json');
        }
        $centros = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //enviamos respuesta
        $response->getBody()->write(json_encode($centros));
        return $response->withStatus(200)->withHeader('content-Type','application/json');
    });
});
?>