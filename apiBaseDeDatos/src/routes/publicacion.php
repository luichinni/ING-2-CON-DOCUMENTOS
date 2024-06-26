<?php

use Collections\CollectionsStream;
use Collections\Collector;
use Collections\Examples\CollectionsSampleObject;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../utilities/bdController.php';

/*
CREATE TABLE Publicacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255),
    descripcion TEXT,
    user varchar (50),
    categoria_id INT,
    estado VARCHAR(50),
    fecha_carga DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user) REFERENCES Usuarios(username),
    FOREIGN KEY (categoria_id) REFERENCES Categoria(id)
);
*/

require_once __DIR__ . '/../models/publiDb.php';

function publiValidator(array $data){
    global $userDB;
    $valid = ['invalido'=>''];
    match (true){
        (array_key_exists('nombre', $data)) && (strlen($data['nombre']) <= 1) => $valid['invalido'] = 'nombre',
        (array_key_exists('descripcion', $data)) && ((strlen($data['descripcion']) > 255) || (strlen($data['descripcion']) < 2)) => $valid['invalido'] = 'descripcion',
        (array_key_exists('categoria_id', $data)) && ($data['categoria_id'] == '') => $valid['invalido'] = 'categoria',
        (array_key_exists('centro1', $data)) && ($data['centro1'] == '') => $valid['invalido'] = 'centro',
        (!array_key_exists('centro1', $data)) => $valid['invalido'] = 'centro',
        default => $valid = null
    };
    return $valid;
}

$app->group('/public', function (RouteCollectorProxy $group) use ($pdo) {
    $group->POST('/newPublicacion', function ($request, $response, $args) use ($pdo){
        global $publiDB, $camposPublicacion;
        $pudo = false;
        $status = 500;
        $msgReturn = ['Mensaje' => 'Faltan campos por completar'];

        $bodyParams = (array) $request->getParsedBody();
        $where = $publiDB->getWhereParams($bodyParams); // esto es para los values

        error_log(json_encode($bodyParams));

        $foto = false;
        for ($i = 1; $i <= 6; $i++){
            $foto = $foto || array_key_exists('foto'.$i,$bodyParams);
        }
        //error_log("hay fotos: " . json_encode($foto));

        $valid = publiValidator($bodyParams);

        if (empty($where) || count($camposPublicacion) < count($where) || !$foto || $valid!=null) {
            match(true){
                ($valid != null && $valid['invalido']=='nombre') => $msgReturn['Mensaje'] = 'El nombre no es válido',
                ($valid != null && $valid['invalido']== 'descripcion') => $msgReturn['Mensaje'] = 'La descripción no es válida',
                ($valid != null && $valid['invalido']=='centro') => $msgReturn['Mensaje'] = 'Falta seleccionar un centro',
                ($valid != null && $valid['invalido']=='categoria') => $msgReturn['Mensaje'] = 'Falta seleccionar una categoria',
                default => $msgReturn['Mensaje']='Ups, parece que algo falló, no deberias estar viendo este mensaje :c'
            };
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
        global $publiDB, $centroDB, $categoriaDB, $publiCentroDB, $centroVolunDB;
        $status = 404;
        $msgReturn = ['Mensaje'=>'No se encontraron coincidencias'];
        // obtener los parametros de la query
        $queryParams = $request->getQueryParams();
        if (array_key_exists('categoria_id',$queryParams) && !ctype_digit($queryParams['categoria_id']) && $queryParams['categoria_id']!=""){
            if($categoriaDB->exists(['nombre'=>$queryParams['categoria_id']])){
                $cate =(array)((array)$categoriaDB->getFirst(['nombre'=>$queryParams['categoria_id']]))[0];
                $queryParams['categoria_id'] = $cate['id'];
            }
        }
        //error_log(json_encode($queryParams));
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

        $publis = $publiDB->getAll($where, $queryParams['like']);

        if(!array_key_exists('habilitado',$queryParams) || (array_key_exists('habilitado',$queryParams) && $queryParams['habilitado'] == '1')){
            $collector = Collector::of(Collector::TO_FLAT_ARRAY,fn($obj)=>$obj);
            $stream = new CollectionsStream($publis);
            //error_log("antes del stream: " . json_encode($publis));
            $publis = $stream->reject(function($publi){
                global $publiCentroDB, $centroVolunDB;
                $publi = (array) $publi;
                $valido = true;
                $centros = (array) $publiCentroDB->getAll(['publicacion'=>$publi['id']]);
                //error_log(json_encode($centros));
                foreach ($centros as $key){
                    $id = (array) $key;
                    $id = $id['centro'];
                    //error_log('Centro '.$id.': '. $centroVolunDB->getFirst(['centro'=>$id]));
                    $valido = $valido && !empty((array)$centroVolunDB->getFirst(['centro' => $id]));
                    //error_log('valid='.((!$valido)?'true':'false'));
                }
                return !$valido;
            })->collect($collector);
            //error_log("despues del stream: ". json_encode($publis));
        }

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
                $value['centros'][$i] = ((array)$centroDB->getFirst($wherCentro))[0];
            }
            
            //error_log(json_encode($value));

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