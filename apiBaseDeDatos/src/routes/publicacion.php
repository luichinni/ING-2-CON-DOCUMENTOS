<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../utilities/bdController.php';

$camposPublicacion = [
    'id' => 'int',
    'nombre' => 'varchar',
    'descripcion' => 'text',
    'user' => 'varchar',
    'categoria_id' => 'int',
    'estado' => 'varchar'
];

$publiDB = new bdController('publicacion',$pdo,$camposPublicacion);

$app->group('/public', function (RouteCollectorProxy $group) use ($pdo) {
    $group->POST('/newPublicacion', function ($request, $response, $args) use ($pdo){
        global $publiDB, $camposPublicacion;
        $pudo = false;
        $status = 500;

        $bodyParams = (array) $request->getParsedBody();
        $where = $publiDB->getWhereParams($bodyParams); // esto es para los values

        if (empty($where) || count($camposPublicacion) != count($where) || $publiDB->exists($where)) return $response->withStatus($status)->withHeader('Content-Type', 'application/json');

        $pudo = $publiDB->insert($bodyParams);

        if (!$pudo) return $response->withStatus($status)->withHeader('Content-Type', 'application/json');

        $publiID = json_decode($publiDB->getFirst($where))['id'];

        for ($i = 1; $i <= 3; $i++) {
            if (array_key_exists("centro$i", $bodyParams)) {
                $pudo = $pudo && agregarPubliCentros(array('centro' => $bodyParams["centro$i"], 'publicacion' => $publiID),$pdo);
            }
        }

        for ($j = 1; $j <= 6; $j++) {
            if (array_key_exists("imagen$i", $bodyParams)) {
                $pudo = $pudo && agregarImg(array('archivo' => $bodyParams["imagen$i"], 'publicacion' => $publiID), $pdo);
            }
        }

        $status = ($pudo) ? 200 : $status;

        if ($status == 200) return $response->withStatus($status)->withHeader('Content-Type', 'application/json');

        // si falla algo borra todo para evitar inconsistencias
        $publiDB->delete(array('id'=>$publiID));
        for ($i = 1; $i <= 3; $i++) {
            if (array_key_exists("centro$i", $bodyParams)) {
                borrarPubliCentro(array('centro' => $bodyParams["centro$i"], 'publicacion' => $publiID), $pdo);
            }
        }

        for ($j = 1; $j <= 6; $j++) {
            if (array_key_exists("imagen$i", $bodyParams)) {
                eliminarImg(array('archivo' => $bodyParams["imagen$i"], 'publicacion' => $publiID), $pdo);
            }
        }

        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    });

    $group->PUT('/updatePublicacion', function ($request, Response $response, $args){
        global $publiDB;
        $pudo = false;
        $status = 500;

        //
        // FALTARIA PODER ACTUALIZAR LAS FOTOS Y CENTROS, QUIZAS HACERLO POR OTRA RUTA DE ELIMINAR ESPECIFICO ESO
        //

        $bodyParams = (array) $request->getParsedBody();
        $where = $publiDB->getWhereParams($bodyParams);

        if (empty($where) || !$publiDB->exists($where)) return $response->withStatus($status)->withHeader('Content-Type', 'application/json');

        $pudo = $publiDB->update($bodyParams);

        $status = ($pudo) ? 200 : $status;

        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    });

    $group->DELETE('/deletePublicacion', function (Request $request, Response $response, $args){
        global $publiDB;
        $pudo = false;
        $status = 500;

        $queryParams = $request->getQueryParams();
        $where = $publiDB->getWhereParams($queryParams);

        if (!$publiDB->exists($where)) return $response->withStatus($status)->withHeader('Content-Type', 'application/json');

        $pudo = $publiDB->delete($where);

        $status = ($pudo) ? 200 : $status;

        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    });

    $group->GET('/listarPublicaciones', function ($request, Response $response, $args) use ($pdo) {
        global $publiDB, $centroDB, $publiCentroDB, $imgDB;
        $status = 404;
        // obtener los parametros de la query
        $queryParams = $request->getQueryParams();

        $where = $publiDB->getWhereParams($queryParams);

        if (empty($where) || !$publiDB->exists($where)) return $response->withHeader('Content-Type', 'application/json')->withStatus($status);

        $publis = json_decode($publiDB->getFirst($where, false, 20));

        foreach($publis as $key => $value){
            $where = [
                'publicacion' => $value['id']
            ];

            $publiCent = listarPubliCentros($where);

            for ($i = 0; $i < count($publiCent); $i++){
                $wherCentro = ['id' => $publiCent[$i]['centro']];
                $value["centro$i"] = $centroDB->getFirst($wherCentro);
            }
            
            $publiImg = listarImg($where);

            for ($i = 0; $i < count($publiImg); $i++) {
                $value["imagen$i"] = $publiImg[$i];
            }
        }

        $response->getBody()->write(json_encode($publis));
        $status = 200;

        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    });
});
?>