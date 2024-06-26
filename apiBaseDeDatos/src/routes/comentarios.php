<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../utilities/bdController.php';
// son obligatorios:
// id publicacion como "publicacion"
// username de quien comentó como "user"
// texto del coment como "texto"
// es opcional el "respondeA" que es id del comentario al que responde

/*
CREATE TABLE Comentario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    publicacion int,
    user varchar (50),
    texto TEXT,
    respondeA int NULL,
    fecha_publicacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificado DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user) REFERENCES Usuarios(username),
    FOREIGN KEY (respondeA) REFERENCES Comentario(id)
);
*/

require_once __DIR__ . '/../models/comentariosDb.php';

$app->group('/public', function (RouteCollectorProxy $group) {

    $group->post('/newComentario', function (Request $req, Response $res, $args){
        global $comentariosDB;
        $status = 500;
        $msgReturn = ['Mensaje'=>'No fue posible publicar el comentario'];

        $bodyParams = (array) $req->getParsedBody();

        if (!array_key_exists('texto',$bodyParams) || (array_key_exists('texto',$bodyParams)&&empty(trim($bodyParams['texto'])))){
            $msgReturn['Mensaje'] = 'El comentario no puede estar vacio';
            $res->getBody()->write(json_encode($msgReturn));
            return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        $pudo = $comentariosDB->insert($bodyParams);

        if ($pudo) $status = 200;

        $msgReturn['Mensaje'] = 'Publicacion comentada con éxito';
        $res->getBody()->write(json_encode($msgReturn));
        return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
    });

    $group->get('/listarComentarios', function (Request $req, Response $res, $args){
        global $comentariosDB;
        $status = 500;
        $msgReturn = ['Mensaje' => 'No hay comentarios'];

        $queryParams = (array) $req->getQueryParams();

        if (!array_key_exists('publicacion',$queryParams) || !$comentariosDB->exists($queryParams)){
            $res->getBody()->write(json_encode($msgReturn));
            return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        $ret = (array) $comentariosDB->getAll($queryParams);

        $status = 200;
        $ret['Mensaje'] = 'Comentarios listados con éxito';

        $res->getBody()->write(json_encode($ret));
        return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
    });

    $group->delete('/deleteComentario', function (Request $req, Response $res, $args) {
        global $comentariosDB;
        $status = 500;
        $msgReturn = ['Mensaje' => 'No existe el comentario'];

        $queryParams = (array) $req->getQueryParams();

        /* error_log(json_encode($queryParams));
        error_log('id: '. (int)(!array_key_exists('id', $queryParams)));
        error_log('existe: '.(int)(!$comentariosDB->exists($queryParams)));
        error_log('no es vacio: '. (int)(array_key_exists('id', $queryParams) && empty(trim($queryParams['id'])))); */
        if (!array_key_exists('id',$queryParams) || !$comentariosDB->exists($queryParams) || (array_key_exists('id', $queryParams)&&empty(trim($queryParams['id'])))){
            $res->getBody()->write(json_encode($msgReturn));
            return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        $comment =(array)($comentariosDB->getFirst($queryParams)[0]);
        /* error_log(json_encode($comment)); */
        if (array_key_exists('userMod',$queryParams)&&$queryParams['userMod']!=$comment['user']){
            $msgReturn['Mensaje'] = "No puedes eliminar un comentario que no es tuyo";
            $res->getBody()->write(json_encode($msgReturn));
            return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
        }
        /* error_log('pasa if'); */
        try{
            $pudo = $comentariosDB->delete($queryParams);
        }catch(Exception $e){
            $pudo = false;
        }

        if ($pudo) {
            $status = 200;
            $msgReturn['Mensaje'] = 'Comentario eliminado con éxito';
        }else{
            $msgReturn['Mensaje'] = 'No se pudo eliminar el comentario';
        }

        $res->getBody()->write(json_encode($msgReturn));
        return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
    });

    $group->delete('/updateComentario', function (Request $req, Response $res){
        global $comentariosDB;
        $status = 500;
        $msgReturn = ['Mensaje' => 'No existe el comentario'];

        $bodyParams = (array) $req->getParsedBody();
        if (!array_key_exists('id', $bodyParams) || !$comentariosDB->exists($bodyParams) || (array_key_exists('id', $bodyParams) && empty(trim($bodyParams['id'])))) {
            $res->getBody()->write(json_encode($msgReturn));
            return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        if ($comentariosDB->update($bodyParams)){
            $status=200;
            $msgReturn['Mensaje'] = 'Comentario actualizado con éxito';
        }
        
        $res->getBody()->write(json_encode($msgReturn));
        return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
    });
});