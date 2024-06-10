<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../utilities/bdController.php';

$camposPublicacion = [
    'id' => '?int',
    'nombre' => 'varchar',
    'descripcion' => 'text',
    'user' => 'varchar',
    'categoria_id' => 'int',
    'estado' => 'varchar',
    'fecha_carga' => '?datetime'
];

$publiDB = new bdController('publicacion',$pdo,$camposPublicacion);

$app->group('/public', function (RouteCollectorProxy $group) use ($pdo) {
    $group->POST('/newPublicacion', function ($request, $response, $args) use ($pdo){
        global $publiDB, $camposPublicacion;
        $pudo = false;
        $status = 500;
        $msgReturn = ['Mensaje' => 'Faltan campos por completar'];

        $bodyParams = (array) $request->getParsedBody();
        $where = $publiDB->getWhereParams($bodyParams); // esto es para los values

        //error_log(json_encode($bodyParams));

        $foto = false;
        for ($i = 1; $i <= 6; $i++){
            $foto = $foto || array_key_exists('foto'.$i,$bodyParams);
        }
        error_log("hay fotos: " . json_encode($foto));

        if (empty($where) || count($camposPublicacion) < count($where) || !$foto) {
            $response->getBody()->write(json_encode($msgReturn));
            return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        error_log("Ahora intenta insertar");

        $pudo = $publiDB->insert($bodyParams);

        error_log("Insertar: " . json_encode($pudo));

        $msgReturn['Mensaje'] = 'Ocurrió un error al cargar la publicación';

        if (!$pudo) {
            $response->getBody()->write(json_encode($msgReturn));
            return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        $publiID = (array)((json_decode($publiDB->getFirst($where)))[0]);
        $publiID = $publiID[0];
        $bodyParams['publicacion'] = $publiID;

        for ($i = 1; $i <= 3; $i++) {
            $strCentro = "centro" . $i;
            if (array_key_exists($strCentro, $bodyParams)) {
                $bodyParams['centro'] = $bodyParams[$strCentro];
                $pudo = $pudo && agregarPubliCentros($bodyParams,$pdo);
            }
        }

        for ($j = 1; $j <= 6; $j++) {
            $strImg = "foto" . $j;
            if (array_key_exists($strImg, $bodyParams)) {
                $bodyParams['archivo'] = $bodyParams[$strImg];
                $pudo = $pudo && agregarImg($bodyParams, $pdo);
            }
        }

        $status = ($pudo) ? 200 : $status;

        if ($status == 200) {
            $msgReturn['Mensaje'] = 'Publicación cargada exitosamente';
            $response->getBody()->write(json_encode($msgReturn));
            return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

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

        $response->getBody()->write(json_encode($msgReturn));
        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    });

    $group->PUT('/updatePublicacion', function ($request, Response $response, $args){
        global $publiDB;
        $pudo = false;
        $status = 500;
        $msgReturn = ['Mensaje'=>'Faltan datos para poder actualizar la publicación'];

        //
        // FALTARIA PODER ACTUALIZAR LAS FOTOS Y CENTROS, QUIZAS HACERLO POR OTRA RUTA DE ELIMINAR ESPECIFICO ESO
        //

        $bodyParams = (array) $request->getParsedBody();
        $where = $publiDB->getWhereParams($bodyParams);

        if (empty($where) || !$publiDB->exists($where)) {
            $response->getBody()->write(json_encode($msgReturn));
            return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
        }
        $pudo = $publiDB->update($bodyParams);

        $status = ($pudo) ? 200 : $status;

        $msgReturn['Mensaje'] = ($status == 200) ? 'Publicación actualizada con éxito' : 'Ocurrió un erro al actualizar la publicación';

        $response->getBody()->write(json_encode($msgReturn));

        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    });

    $group->DELETE('/deletePublicacion', function (Request $request, Response $response, $args){
        global $publiDB;
        $pudo = false;
        $status = 500;
        $msgReturn = ['Mensaje' => 'No existe una publicación que coincida con los datos'];

        $queryParams = $request->getQueryParams();
        $where = $publiDB->getWhereParams($queryParams);

        if (!$publiDB->exists($where)) {
            $response->getBody()->write(json_encode($msgReturn));
            return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        $pudo = $publiDB->delete($where);

        $status = ($pudo) ? 200 : $status;

        $msgReturn['Mensaje'] = ($status == 200) ? 'Publicación eliminada correctamente' : 'Ocurrió un error al eliminar la publicación';

        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    });

    $group->GET('/listarPublicaciones', function ($request, Response $response, $args) use ($pdo) {
        global $publiDB, $centroDB, $categoriaDB;
        $status = 404;
        $msgReturn = ['Mensaje'=>'No se encontraron coincidencias'];
        // obtener los parametros de la query
        $queryParams = $request->getQueryParams();
        
        if (array_key_exists('like', $queryParams)){
            $queryParams['like'] = $queryParams['like']=="true" ? true : false;
        }else{
            $queryParams['like'] = true;
        }

        $where = $publiDB->getWhereParams($queryParams);

        if (empty($where) || !$publiDB->exists($where, $queryParams['like'])) {
            $response->getBody()->write(json_encode($msgReturn));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
        }

        $offset = (array_key_exists('pag', $queryParams)) ? $queryParams['pag'] : 0;

        $publis = json_decode($publiDB->getFirst($where, $queryParams['like'], 20,$offset));

        foreach($publis as $key => $value){
            $value = (array) $value;

            $miCategoria = (array) json_decode($categoriaDB->getFirst(array('id' => $value['categoria_id'])));
            $value['categoria_id'] = ((array) $miCategoria[0])['nombre'];

            $where = [
                'publicacion' => $value['id']
            ];

            $publiCent = listarPubliCentros($where);

            for ($i = 0; $i < count($publiCent); $i++){
                $tempArr = (array) $publiCent[$i];
                $wherCentro = ['id' => $tempArr['centro']];
                $value['centros'][$i] = ((array)json_decode($centroDB->getFirst($wherCentro)))[0];
            }
            
            error_log(json_encode($value));

            $value['imagenes'] = listarImg($where);

            error_log(json_encode(count($value['imagenes'])));

            $publis[$key] = $value;
        }

        $publis['Mensaje'] = 'Publicaciones listadas con éxito';
        $response->getBody()->write(json_encode($publis));
        $status = 200;

        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    });
});
?>