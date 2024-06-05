<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$camposPublicacion = [
    'id' => [
        "pk" => true,
        "autoincrement" => true,
        "tipo" => "int",
        "autoincrement" => true,
        "comparador" => "=",
        "opcional" => false,
     ],
    'nombre' => [
        "tipo" => "varchar(255)",
        "comparador" => "like",
        "opcional" => false,
     ],
    'descripcion' => [
        "tipo" => "text",
        "comparador" => "like",
        "opcional" => false,
     ],
    'user' => [
        "tipo" => "varchar(50)",
        "comparador" => "like",
        "opcional" => false,
        "fk" => [
            "tabla"=>"usuarios", 
            "campo"=>"user"
        ]
     ],
    'categoria_id' => [
        "tipo" => "int",
        "comparador" => "=",
        "opcional" => false,
        "fk" => [
            "tabla" => "categoria",
            "campo" => "id"
        ]
    ],
    'estado' => [
        "tipo" => "varchar(50)",
        "comparador" => "like",
        "opcional" => false,
    ]
];

$publiDB = new bdController('publicacion',$pdo,$camposPublicacion);

$app->group('/public', function (RouteCollectorProxy $group) use ($pdo) {
    $group->POST('/newPublicacion', function ($request, $response, $args) use ($pdo){
        global $publiDB, $camposPublicacion;
        $pudo = false;
        $status = 500;
        $msgReturn = ['Mensaje' => 'Faltan campos por completar'];

        $bodyParams = (array) $request->getParsedBody();
        error_log("LO QUE RECIBO DEL FRONT:" . json_encode($bodyParams));
        $where = $publiDB->getWhereParams($bodyParams); // esto es para los values

        //error_log(json_encode($bodyParams));

        $foto = false;
        for ($i = 1; $i <= 6; $i++){
            $foto = $foto || array_key_exists('foto'.$i,$bodyParams);
        }
        //error_log("hay fotos: " . json_encode($foto));

        if (empty($where) || count($camposPublicacion) < count($where) || !$foto) {
            $response->getBody()->write(json_encode($msgReturn));
            return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        //error_log("Ahora intenta insertar");

        $pudo = $publiDB->insert($bodyParams);

        //error_log("Insertar: " . json_encode($pudo));

        $msgReturn['Mensaje'] = 'Ocurrió un error al cargar la publicación';

        if (!$pudo) {
            $response->getBody()->write(json_encode($msgReturn));
            return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        $publiID = (array)(($publiDB->getFirst($where))[0]);
        $publiID = $publiID[0];
        $whereAgregado['publicacion'] = $publiID;

        for ($i = 1; $i <= 3; $i++) {
            $strCentro = "centro" . $i;
            if (array_key_exists($strCentro, $bodyParams)) {
                $whereAgregado['centro'] = $bodyParams[$strCentro];
                //error_log("InsertarCentro '".$whereAgregado['centro']."' : " . json_encode($pudo));
                $pudo = $pudo && agregarPubliCentros($whereAgregado,$pdo);
            }
        }
        //error_log("InsertarCENTRO: " . json_encode($pudo));
        for ($j = 1; $j <= 6; $j++) {
            $strImg = "foto" . $j;
            if (array_key_exists($strImg, $bodyParams)) {
                $whereAgregado['archivo'] = $bodyParams[$strImg];
                $pudo = $pudo && agregarImg($whereAgregado, $pdo);
            }
        }

        //error_log("InsertarIMG: " . json_encode($pudo));
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
        foreach (array_keys($queryParams, "undefined", true) as $key) {
            unset($queryParams[$key]); //elimina los undefined
        }
        //error_log(json_encode($queryParams));
        if (array_key_exists('like', $queryParams)){
            $queryParams['like'] = $queryParams['like']=="true" ? true : false;
        }else{
            $queryParams['like'] = true;
        }
        
        $where = $publiDB->getWhereParams($queryParams);

        if (/* empty($where) || */ !$publiDB->exists($where, $queryParams['like'])) {
            $response->getBody()->write(json_encode($msgReturn));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
        }

        $offset = (array_key_exists('pag', $queryParams)) ? $queryParams['pag'] : 0;

        $publis = $publiDB->getFirst($where, true,$queryParams['like'], 20,$offset);

        foreach($publis as $key => $value){
            $value = (array) $value;

            $miCategoria = (array) $categoriaDB->getFirst(array('id' => $value['categoria_id']));
            $value['categoria_id'] = ((array) $miCategoria[0])['nombre'];

            $where = [
                'publicacion' => $value['id']
            ];

            $publiCent = listarPubliCentros($where);

            for ($i = 0; $i < count($publiCent); $i++){
                $tempArr = (array) $publiCent[$i];
                $wherCentro = ['id' => $tempArr['centro']];
                $value['centros'][$i] = $centroDB->getFirst($wherCentro);
            }
            
            //error_log(json_encode($where));

            $value['imagenes'] = listarImg($where);

            //error_log(json_encode(count($value['imagenes'])));

            $publis[$key] = $value;
        }

        $publis['Mensaje'] = 'Publicaciones listadas con éxito';
        $response->getBody()->write(json_encode($publis));
        $status = 200;

        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    });
});
?>