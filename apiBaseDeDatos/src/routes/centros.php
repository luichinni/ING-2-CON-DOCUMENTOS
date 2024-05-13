<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->group('/public', function (RouteCollectorProxy $group) use ($pdo) {
    $group->post('/centros', function ($request, $response, $args) use ($pdo){
        $body = $request-> getBody() -> getContents();
        $data = json_decode($body, true);

        $columnas = ['id','nombre','direccion','hora_abre','hora_cierra'];

        foreach ($columnas as $colum) {
            if (!isset($data[$colum]) || $data[$colum]===""){
                $errorResponse = ['error' => 'el campo "'. $colum . '" es requerido'];
                $response-> getBody()->write(json_encode($errorResponse));
                return $response->withStatus(400);
            }
        }

        $stmt = $pdo-> prepare('INSERT INTO centros (id, nombre, direccion, hora_abre, hora_cierra) 
                                VALUES (:id, :nombre, :direccion, :hora_abre, :hora_cierra)');
        $stmt->bindparam(':id', $data['id']);
        $stmt->bindparam(':nombre', $data['nombre']);
        $stmt->bindparam(':direccion', $data['direccion']);
        $stmt->bindparam(':hora_abre', $data['hora_abre']);
        $stmt->bindparam(':hora_cierra', $data['hora_cierra']);

        try{
            $stmt->execute();
        } catch (Exception $e) {
            $errorResponse = ['error' => 'error al ejecutar la consulta: ' . $e->getMessage()];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(500)->withheader('Content-Type','application/json');
        }

        $centro = [
            'id' => $data ['id'],
            'nombre' => $data ['nombre'],
            'direccion' => $data ['direccion'],
            'hora_abre' => $data ['hora_abre'],
            'hora_cierra' => $data ['hora_cierra'],
        ];

        $response->getBody()->write(json_encode($centro));
        return $response->withStatus(200)->withHeader('Content-Type','application/json');
    });
    $group->put('/centros/{id}', function($request, Response $response, $args) use ($pdo){
        $id = $args['id'];
        //preparamos para ver si existe un centro con ese id
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM institucion WHERE id = :id ');
        $stmt->bindParam(':id',$id);
        $stmt->execute();
        $existe = $stmt->fechtColumn();
        //si el fechtcolum da 0, enviamos mensaje de error que no hay centro con ese id
        if ($existe == 0) {
            $errorResponse = ['El codigo seleccionado no corresponde a un ningun centro'];
            $response->getBody()->write (json_encode($errorResponse));
            return $response->withStatus(404)->withHeader('Content-Type','application/json');
        } else {
            $body = $request->getBody()->getContents();
            $data = json_decode($body,true);

            // revisamos si tiene todos los campos
            $columnas = ['id', 'nombre', 'direccion', 'hora_abre', 'hora_cierra'];
            
            foreach ($columna as $colum){
                if (!isset($data[$colum])||$data[$colum]===0){
                    $errorResponse=['error' = 'el campo: ' . $colum . ' es requerido'];
                    $response->getBody()->write(json_encode($errorResponse));
                    return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
                }
            }
            //revisamos la cantidad de carcteres
            revisarCar = ['nombre', 'direccion'];
            foreach ($revisarCar as $revi){
                if (iseet($data[$revi]) && strlen($data[$revi] > 255)){
                    $errorResponse=['error'='el campo' . $revi . 'excede los 255 caracteres permitidos'];
                    $response->getBody()->write($errorResponse);
                    return $respones->withStatus(400
                );
                }
            }
        }
    })

});
?>