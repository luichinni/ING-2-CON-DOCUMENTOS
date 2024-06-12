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

    $group->POST('/newIntercambio', function (Request $request, Response $response, $args) { //necesita en el body publicacionOferta, publicacionOfertada, centro, horario
        global $publiDB, $intercambioDB, $userDB;
        $pudo = false;
        $status = 500;
        $msgReturn = ['Mensaje' => 'Faltan campos por completar'];

        $bodyParams = (array) $request->getParsedBody();

        //error_log(json_encode($bodyParams));
        //error_log(json_encode($bodyParams));
        if (!$publiDB->exists(['id'=>$bodyParams['publicacionOferta']]) || !$publiDB->exists(['id' => $bodyParams['publicacionOferta']])){
            $msgReturn['Mensaje'] = 'Ocurrió un error al comprobar las publicaciones';
            $response->getBody()->write(json_encode($msgReturn));
            return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
        }
        
        $intercambio1 = (array) json_decode($intercambioDB->getFirst(['publicacionOferta' => $bodyParams['publicacionOferta'], 'publicacionOfertada' => $bodyParams['publicacionOfertada']]));
        $intercambio2 = (array) json_decode($intercambioDB->getFirst(['publicacionOferta' => $bodyParams['publicacionOfertada'], 'publicacionOfertada' => $bodyParams['publicacionOferta']]));

        //error_log(json_encode($intercambio1));
        //error_log(json_encode($intercambio2));

        if (!empty($intercambio1)) $intercambio1 = (array) $intercambio1[0];
        if (!empty($intercambio2)) $intercambio2 = (array) $intercambio2[0]; 

        if (!empty($intercambio1) && ($intercambio1['estado'] != 'rechazado' || $intercambio1['estado'] != 'cancelado')){
            $msgReturn['Mensaje'] = 'Ya hay un intercambio activo con la misma publicacion';
            $response->getBody()->write(json_encode($msgReturn));
            return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
        }else if(!empty($intercambio2) && ($intercambio2['estado'] != 'rechazado' || $intercambio2['estado'] != 'cancelado')){
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
        $bodyParams['ofertadaAcepta'] = true;

        $pudo = $intercambioDB->insert($bodyParams);
        
        $status = ($pudo) ? 200 : $status;

        if ($pudo) {
            //obtener ambas publis
            $p1 = $bodyParams['publicacionOferta'];
            $p1 = (array) json_decode($publiDB->getFirst(['id' => $p1]))[0];
            $p2 = $bodyParams['publicacionOfertada'];
            $p2 = (array) json_decode($publiDB->getFirst(['id' => $p2]))[0];
            //obtener ambos users
            
            $redirect = (array) json_decode($intercambioDB->getFirst(['publicacionOferta'=>$p1['id'], 'publicacionOfertada'=>$p2['id'], 'estado'=>'pendiente']));
            $redirect = (array) $redirect[0];
            $mensaje = $p1['user'] . " te ha ofrecido \"" . $p1['nombre'] . "\" por \"" . $p2['nombre'] . "\"";
            enviarNotificacion($p2['user'], $mensaje, './Intercambio');
            global $mailer;
            $user = (array)((array)json_decode($userDB->getFirst(['username' => $p2['user']])))[0];
            
            if ($user['notificacion']) $mailer->send($user['mail'], 'Notificacion de Intercambio!', $mensaje, true);

            $mensaje2 = "Has ofrecido \"" . $p1['nombre'] . "\" por \"".$p2['nombre']."\" a " . $p2['user'];
            $user2 = (array)((array)json_decode($userDB->getFirst(['username' => $p1['user']])))[0];
            
            if ($user2['notificacion']) $mailer->send($user2['mail'], 'Notificacion de Intercambio!', $mensaje2, true);
            enviarNotificacion($p1['user'], $mensaje2, './Intercambio');
        }

        $msgReturn['Mensaje'] = ($pudo) ? 'Intercambio registrado con exito' : 'Ocurrió un error al registrar el intercambio';

        $response->getBody()->write(json_encode($msgReturn));
        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    });

    $group->GET('/listarIntercambios', function (Request $request,Response $response, $args) {
        global $intercambioDB, $centroDB, $publiDB;
        $status = 404;
        $msgReturn = ['Mensaje' => 'No hay intercambios disponibles'];

        $queryParams = $request->getQueryParams();
        //error_log('QUERY PARAMS  '.json_encode($queryParams));
        $listado = [];

        if (array_key_exists('publicacionOferta',$queryParams) && $queryParams['publicacionOferta']!=""){
            // pido todas las del nombre
            $publisO = (array) json_decode($publiDB->getAll(['nombre'=>$queryParams['publicacionOferta']],true));
            // pido todos los intercambios de uno por uno
            //error_log('publisO: '.json_encode($publisO). " -- existe->".$publiDB->exists(['nombre' => $queryParams['publicacionOferta']]));
            foreach ($publisO as $publi){
                // los pongo en un array comun
                $publi = (array) $publi;
                foreach (array_values((array)json_decode($intercambioDB->getAll(['publicacionOferta' => $publi['id']]))) as $inter){
                    $listado[] = $inter;
                }
            }
            //error_log('listado: '.json_encode($listado));
            
            if (array_key_exists('publicacionOfertada',$queryParams) && $queryParams['publicacionOfertada'] != ""){
                // pido todas las publis del nombre
                $publisOf = (array) json_decode($publiDB->getAll(['nombre' => $queryParams['publicacionOfertada']], true));
                // elimino los intercambios donde no aparezca como publiOfertada
                //error_log("PUBLISOF: ". json_encode($publisOf));
                $newListado = [];
                foreach ($publisOf as $publi) {
                    // los pongo en un array comun
                    $publi = (array) $publi;
                    foreach ($listado as $inter) {
                        $inter = (array) $inter;
                        //error_log("Cumple: ".($inter['publicacionOfertada'] == $publi['id']));
                        if ($inter['publicacionOfertada'] == $publi['id']){
                            $newListado[] = $inter;
                        }
                    }
                }
                $listado = $newListado;
            }
            //error_log('listado2: ' . json_encode($listado));
        } else if (array_key_exists('publicacionOfertada', $queryParams) && $queryParams['publicacionOfertada'] != "") {
            // pido todas las del nombre
            $publisO = (array) json_decode($publiDB->getAll(['nombre' => $queryParams['publicacionOfertada']], true));
            // pido todos los intercambios de uno por uno
            foreach ($publisO as $publi) {
                // los pongo en un array comun
                $publi = (array) $publi;
                foreach (array_values((array)json_decode($intercambioDB->getAll(['publicacionOfertada' => $publi['id']]))) as $inter) {
                    $listado[] = $inter;
                }
            }
        }else{
            $listado = (array) json_decode($intercambioDB->getAll($queryParams));
        }
        
        foreach ($listado as $id => $intercambio){
            $intercambio = (array) $intercambio;
            $centro = (array) json_decode($centroDB->getFirst(['id'=>$intercambio['centro']]));
            $centro = (array) $centro[0];
            //error_log(json_encode($centro));
            $intercambio['centro'] = $centro['Nombre'];
            $listado[$id] = $intercambio;
        }

        $listado['Mensaje'] = (!empty($listado)) ? 'Intercambios listados con exito' : $msgReturn['Mensaje'];

        $status = (!empty($listado)) ? 200 : 404;
        //error_log(json_encode($listado));
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
            $inter = (array)((array)json_decode($intercambioDB->getFirst(['id'=>$bodyParams['id']])))[0];
            error_log("inter: ".json_encode($inter));
            $p1 = $inter['publicacionOferta'];
            $p1 = (array) json_decode($publiDB->getFirst(['id'=>$p1]))[0];
            $p2 = $inter['publicacionOfertada'];
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
                $mensaje = (array_key_exists('setestado',$bodyParams)) ? "El intercambio de \"$tuProducto\" por \"$elOtroProducto\" con $userActual fue ".$bodyParams['setestado']."." : "El intercambio de \"$tuProducto\" por \"$elOtroProducto\" con $userActual fue modificado.";
                $mensaje2 = (array_key_exists('setestado',$bodyParams)) ? "El intercambio de \"$tuProducto\" por \"$elOtroProducto\" con $otroUser fue ".$bodyParams['setestado']."." : "El intercambio de \"$tuProducto\" por \"$elOtroProducto\" con $otroUser fue modificado.";
                $redirect = (array) json_decode($intercambioDB->getFirst(['publicacionOferta' => $p1['id'], 'publicacionOfertada' => $p2['id'], 'estado' => 'pendiente']));
                $redirect = $redirect[0];
                enviarNotificacion($otroUser, $mensaje, './Intercambio');
                enviarNotificacion($userActual, $mensaje2, './Intercambio');

                $user = (array)((array)json_decode($userDB->getFirst(['username' => $otroUser])))[0];
                $user2 = (array)((array)json_decode($userDB->getFirst(['username' => $userActual])))[0];
                global $mailer;
                
                if ($user['notificacion']){
                    $mailer->send($user['mail'], 'Notificacion de Intercambio!', $mensaje, true);
                }                
                if ($user2['notificacion']){
                    $mailer->send($user2['mail'], 'Notificacion de Intercambio!', $mensaje2, true);
                }
                
            }
        }

        $res->getBody()->write(json_encode($msgReturn));
        return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
    });
});

?>