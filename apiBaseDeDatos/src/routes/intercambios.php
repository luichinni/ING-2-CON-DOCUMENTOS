<?php
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/notificaciones.php';

$camposIntercambio = [
    'id' => '?int',
    'voluntario' => '?varchar',
    'publicacionOferta' => 'int', // quien publicó
    'publicacionOfertada' => 'int', // quien ofertó
    'ofertaAcepta' => '?bool',
    'ofertadaAcepta' => 'bool',
    'horario' => 'DATETIME',
    'estado' => 'varchar',
    'descripcion' => '?TEXT',
    'donacion' => '?bool',
    'centro' => 'int',
    'fecha_propuesta' => '?DATETIME',
    'fecha_modificado' => '?DATETIME'
];

$intercambioDB = new bdController('intercambio',$pdo,$camposIntercambio);

$app->group('/public', function (RouteCollectorProxy $group) {

    $group->POST('/newIntercambio', function (Request $request, Response $response, $args) {
        global $publiDB, $intercambioDB;
        $pudo = false;
        $status = 500;
        $msgReturn = ['Mensaje' => 'Faltan campos por completar'];

        $bodyParams = (array) $request->getParsedBody();

        //error_log(json_encode($bodyParams));

        if (!$publiDB->exists(['id'=>$bodyParams['publicacionOferta']]) || !$publiDB->exists(['id' => $bodyParams['publicacionOferta']])){
            $msgReturn['Mensaje'] = 'Ocurrió un error al comprobar las publicaciones';
            $response->getBody()->write(json_encode($msgReturn));
            return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        $intercambio1 = (array) json_decode($intercambioDB->getFirst(['publicacionOferta' => $bodyParams['publicacionOferta'], 'publicacionOfertada' => $bodyParams['publicacionOfertada']]));
        $intercambio2 = (array) json_decode($intercambioDB->getFirst(['publicacionOferta' => $bodyParams['publicacionOfertada'], 'publicacionOfertada' => $bodyParams['publicacionOferta']]));

        $intercambio1 = $intercambio1[0];
        $intercambio2 = $intercambio2[0];

        if ($intercambio1['estado'] != 'rechazado' || $intercambio1['estado'] != 'cancelado' || $intercambio2['estado'] == 'rechazado' || $intercambio2['estado'] == 'cancelado'){
            $msgReturn['Mensaje'] = 'Ya hay un intercambio activo con la misma publicacion';
            $response->getBody()->write(json_encode($msgReturn));
            return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        // medio atado con alambre esta parte jaja
        $p1 = json_decode($publiDB->getFirst(['id'=>$bodyParams['publicacionOferta']]));
        $p1 = (array) $p1[0];
        $p2 = json_decode($publiDB->getFirst(['id'=>$bodyParams['publicacionOfertada']]));
        $p2 = (array) $p2[0];

        if ($p1['categoria_id'] != $p2['categoria_id']){
            $msgReturn['Mensaje'] = 'Deben ser de la misma categoria';
            $response->getBody()->write(json_encode($msgReturn));
            return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        $bodyParams['estado'] = 'pendiente';
        $bodyParams['ofertadaAceptada'] = true;

        $pudo = $intercambioDB->insert($bodyParams);
        
        $status = ($pudo) ? 200 : $status;

        if ($pudo) {
            //obtener ambas publis
            $p1 = $bodyParams['publicacionOferta'];
            $p1 = (array) json_decode($publiDB->getFirst(['id' => $p1]))[0];
            $p2 = $bodyParams['publicacionOfertada'];
            $p2 = (array) json_decode($publiDB->getFirst(['id' => $p2]))[0];
            //obtener ambos users
            if ($bodyParams['userMod'] == $p1['user']) {
                $otroUser = $p2['user'];
                $userActual = $p1['user'];
                $tuProducto = $p2['nombre'];
                $elOtroProducto = $p1['nombre'];
            } else {
                $otroUser = $p1['user'];
                $userActual = $p2['user'];
                $tuProducto = $p1['nombre'];
                $elOtroProducto = $p2['nombre'];
            }
            
            $redirect = (array) json_decode($intercambioDB->getFirst(['publicacionOferta'=>$p1['id'], 'publicacionOfertada'=>$p2['id'], 'estado'=>'pendiente']));
            $redirect = $redirect[0];
            enviarNotificacion($otroUser, "$userActual te ha ofrecido \"$elOtroProducto\" por \"$tuProducto\"", './intercambio/'. $redirect['id']);
        }

        $msgReturn['Mensaje'] = ($pudo) ? 'Intercambio registrado con exito' : 'Ocurrió un error al registrar el intercambio';

        $response->getBody()->write(json_encode($msgReturn));
        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    });

    $group->GET('/listarIntercambios', function (Request $request,Response $response, $args) {
        global $intercambioDB;
        $status = 404;
        $msgReturn = ['Mensaje' => 'No hay intercambios disponibles'];

        $queryParams = $request->getQueryParams();

        $listado = (array) json_decode($intercambioDB->getAll($queryParams));

        $listado['Mensaje'] = (!empty($listado)) ? 'Intercambios listados con exito' : $msgReturn['Mensaje'];

        $status = (!empty($listado)) ? 200 : 404;

        $response->getBody()->write(json_encode($listado));
        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    });

    $group->put('/updateIntercambio', function (Request $req, Response $res){
        global $intercambioDB,$publiDB,$userDB;
        $pudo = false;
        $status = 500;
        $msgReturn = ['Mensaje' => 'No se pudo actualizar la informacion del intercambio'];

        $bodyParams = (array) $req->getParsedBody();

        $pudo = $intercambioDB->update($bodyParams);

        $msgReturn['Mensaje'] = ($pudo) ? 'Actualizado correctamente' : $msgReturn['Mensaje'];
        $status = ($pudo) ? 200 : $status;

        if ($pudo){ 
            //obtener ambas publis
            $p1 = $bodyParams['publicacionOferta'];
            $p1 = (array) json_decode($publiDB->getFirst(['id'=>$p1]))[0];
            $p2 = $bodyParams['publicacionOfertada'];
            $p2 = (array) json_decode($publiDB->getFirst(['id'=>$p2]))[0];
            //obtener ambos users
            if ($bodyParams['userMod'] == $p1['user']){
                $otroUser = $p2['user'];
                $userActual = $p1['user'];
                $tuProducto = $p2['nombre'];
                $elOtroProducto = $p1['nombre'];
                $bodyParams['ofertaAcepatada'] = true;
                $bodyParams['ofertadaAceptada'] = false;
            }else{
                $otroUser = $p1['user'];
                $userActual = $p2['user'];
                $tuProducto = $p1['nombre'];
                $elOtroProducto = $p2['nombre'];
                $bodyParams['ofertaAcepatada'] = false;
                $bodyParams['ofertadaAceptada'] = true;
            }
            $pudo = $intercambioDB->update($bodyParams);
            $msgReturn['Mensaje'] = ($pudo) ? 'Actualizado correctamente' : $msgReturn['Mensaje'];
            $status = ($pudo) ? 200 : $status;

            if ($pudo){
                $redirect = (array) json_decode($intercambioDB->getFirst(['publicacionOferta' => $p1['id'], 'publicacionOfertada' => $p2['id'], 'estado' => 'pendiente']));
                $redirect = $redirect[0];
                enviarNotificacion($otroUser, "El intercambio de \"$tuProducto\" por \"$elOtroProducto\" con $userActual fue modificado.", './intercambio/'.$redirect['id']);
            }
        }

        $res->getBody()->write(json_encode($msgReturn));
        return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
    });
});

?>