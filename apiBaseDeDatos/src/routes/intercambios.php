<?php
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/notificaciones.php';

/* CREATE TABLE Intercambio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voluntario varchar (50),
    publicacionOferta int,
    publicacionOfertada int,
    ofertaAcepta BOOLEAN,
    ofertadaAcepta BOOLEAN,
    horario DATETIME,
    estado ENUM('pendiente','cancelado','rechazado','aceptado','concretado'),
    descripcion TEXT,
    donacion BOOLEAN,
    centro int,
    fecha_propuesta DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificado DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (voluntario) REFERENCES Usuarios(username),
    FOREIGN KEY (publicacionOferta) REFERENCES Publicacion(id),
    FOREIGN KEY (publicacionOfertada) REFERENCES Publicacion(id),
    FOREIGN KEY (centro) REFERENCES Centros(id)
); */

$camposIntercambio = [
    'id' => [
        "pk" => true,
        "tipo" => "int",
        "autoincrement" => true,
        "comparador" => "="
    ],
    'voluntario' => [
        "tipo" => "varchar(50)",
        "comparador" => "like",
        "fk" => [
            "tabla" => "usuarios",
            "campo" => "username"
        ]
    ],
    'publicacionOferta' => [
        "tipo" => "int",
        "comparador" => "=",
        "fk" => [
            "tabla" => "intercambio",
            "campo" => "id"
        ]
    ], // quien publicó
    'publicacionOfertada' => [
        "tipo" => "int",
        "comparador" => "=",
        "fk" => [
            "tabla" => "intercambio",
            "campo" => "id"
        ]
    ], // quien ofertó
    'ofertaAcepta' => [
        "tipo" => "boolean",
        "comparador" => "=",
        "opcional" => true
    ],
    'ofertadaAcepta' => [
        "tipo" => "boolean",
        "comparador" => "=",
        "default" => "true"
    ],
    'horario' => [
        "tipo" => "datetime",
        "comparador" => "="
    ],
    'estado' => [
        "tipo" => "ENUM('pendiente','cancelado','rechazado','aceptado','concretado')",
        "comparador" => "like"
    ],
    'descripcion' => [
        "tipo" => "text",
        "comparador" => "like",
        "opcional" => true
    ],
    'donacion' => [
        "tipo" => "boolean",
        "comparador" => "=",
        "opcional" => true,
    ],
    'centro' => [
        "tipo" => "int",
        "comparador" => "=",
        "fk" => [
            "tabla" => "centros",
            "campo" => "id"
        ]
    ],
    /* 'fecha_propuesta' => '?DATETIME', created_at
    'fecha_modificado' => '?DATETIME'   updated_at    */
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
        
        $intercambio1 = (array) $intercambioDB->getFirst(['publicacionOferta' => $bodyParams['publicacionOferta'], 'publicacionOfertada' => $bodyParams['publicacionOfertada']]);
        $intercambio2 = (array) $intercambioDB->getFirst(['publicacionOferta' => $bodyParams['publicacionOfertada'], 'publicacionOfertada' => $bodyParams['publicacionOferta']]);

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
        $p1 = $publiDB->getFirst(['id'=>$bodyParams['publicacionOferta']]);
        $p1 = (array) $p1[0];
        $p2 = $publiDB->getFirst(['id'=>$bodyParams['publicacionOfertada']]);
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
            $p1 = (array) $publiDB->getFirst(['id' => $p1])[0];
            $p2 = $bodyParams['publicacionOfertada'];
            $p2 = (array) $publiDB->getFirst(['id' => $p2])[0];
            //obtener ambos users
            
            $redirect = (array) $intercambioDB->getFirst(['publicacionOferta'=>$p1['id'], 'publicacionOfertada'=>$p2['id'], 'estado'=>'pendiente']);
            $redirect = (array) $redirect[0];
            $mensaje = $p1['user'] . " te ha ofrecido \"" . $p1['nombre'] . "\" por \"" . $p2['nombre'] . "\"";
            enviarNotificacion($p2['user'], $mensaje, './Intercambio');
            global $mailer;
            $user = (array)($userDB->getFirst(['username' => $p2['user']]))[0];
            
            if ($user['notificacion']) $mailer->send($user['mail'], 'Notificacion de Intercambio!', $mensaje, true);

            $mensaje2 = "Has ofrecido \"" . $p1['nombre'] . "\" por \"".$p2['nombre']."\" a " . $p2['user'];
            $user2 = (array)($userDB->getFirst(['username' => $p1['user']]))[0];
            
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
        error_log("MIS QUERIES: ".json_encode($queryParams));
        if (array_key_exists('username',$queryParams) && $queryParams['username']!=""){
            $publisUser = (array) $publiDB->getAll(['user'=>$queryParams['username']],true);
            error_log("PUBLIS USER: " . json_encode($publisUser));
            foreach($publisUser as $index => $publi){
                $publi = (array)$publi;
                $copy = $queryParams;
                $copy['publicacionOferta'] = $publi['id'];

                if(array_key_exists('publicacionOfertada',$copy)) unset($copy['publicacionOfertada']);

                foreach (array_values($intercambioDB->getAll($copy,true)) as $inter){
                    $listado[] = $inter;
                }

                $copy = $queryParams;
                $copy['publicacionOfertada'] = $publi['id'];

                if (array_key_exists('publicacionOferta', $copy)) unset($copy['publicacionOferta']);
                
                foreach (array_values((array)$intercambioDB->getAll($copy,true)) as $inter) {
                    $listado[] = $inter;
                }
            }
            if (array_key_exists('publicacionOferta', $queryParams) && $queryParams['publicacionOferta']!=""){
                $publisO = (array) $publiDB->getAll(['nombre'=>$queryParams['publicacionOferta']],true);
                $newList = [];
                error_log("PUBLIS OFERTA EN USER: ". json_encode($publisO));
                foreach($publisO as $index => $publi){
                    $publi = (array) $publi;
                    foreach ($listado as $inter){
                        $inter = (array) $inter;
                        if ($inter['publicacionOferta']==$publi['id']){
                            $newList[] = $inter;
                        }
                    }
                }
                $listado = $newList;
                if (array_key_exists('publicacionOfertada',$queryParams)&&$queryParams['publicacionOfertada']!=""){
                    $publisOf = (array) $publiDB->getAll(['nombre' => $queryParams['publicacionOfertada']],true);
                    $newList = [];
                    error_log("PUBLIS OFERTADAS EN USER: " . json_encode($publisOf));
                    foreach ($publisOf as $index => $publi) {
                        $publi = (array) $publi;
                        foreach ($listado as $inter) {
                            $inter = (array) $inter;
                            if ($inter['publicacionOfertada'] == $publi['id']) {
                                $newList[] = $inter;
                            }
                        }
                    }
                    $listado = $newList;
                }
            }
        }else if (array_key_exists('publicacionOferta',$queryParams) && $queryParams['publicacionOferta']!=""){
            $publisO = (array) $publiDB->getAll(['nombre'=>$queryParams['publicacionOferta']],true);
            error_log("PUBLIS OFERTA SOLAS: " . json_encode($publisO));
            foreach ($publisO as $ind => $publi){
                $publi = (array) $publi;
                $copy = $queryParams;
                $copy['publicacionOferta'] = $publi['id'];

                if (array_key_exists('publicacionOfertada', $copy)) unset($copy['publicacionOfertada']);

                foreach (array_values($intercambioDB->getAll($copy,true)) as $inter){
                    $listado[] = $inter;
                }
            }
            error_log("listado antes de publi ofertda en oferta ". json_encode($listado));
            if (array_key_exists('publicacionOfertada',$queryParams)&&$queryParams['publicacionOfertada']!=""){
                $publisOf = (array) $publiDB->getAll(['nombre'=>$queryParams['publicacionOfertada']],true);
                $newList = [];
                error_log("PUBLIS OFERTADAS EN OFERTA: " . json_encode($publisOf));
                foreach ($publisOf as $ind => $publi2){
                    $publi2 = (array) $publi2;
                    foreach ($listado as $inter) {
                        $inter = (array)$inter;
                        if ($inter['publicacionOfertada']==$publi2['id'])
                        $newList[] = $inter;
                    }
                }
                $listado = $newList;
            }
        }else if (array_key_exists('publicacionOfertada',$queryParams) && $queryParams['publicacionOfertada']!=""){
            $publisO = (array) $publiDB->getAll(['nombre' => $queryParams['publicacionOfertada']],true);
            error_log("PUBLIS OFERTADAS SOLAS: " . json_encode($publisO));
            foreach ($publisO as $ind => $publi) {
                $publi = (array) $publi;
                $copy = $queryParams;
                $copy['publicacionOfertada'] = $publi['id'];

                if (array_key_exists('publicacionOferta', $copy)) unset($copy['publicacionOferta']);

                foreach (array_values((array)$intercambioDB->getAll($copy,true)) as $inter) {
                    $listado[] = $inter;
                }
            }
        }else{
            $listado = (array) $intercambioDB->getAll($queryParams,true);
        }
        
        error_log("MI LISTADO: ".json_encode($listado));

        foreach ($listado as $id => $intercambio){
            $intercambio = (array) $intercambio;
            $centro = (array) $centroDB->getFirst(['id'=>$intercambio['centro']]);
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
            $inter = (array)((array)$intercambioDB->getFirst(['id'=>$bodyParams['id']]))[0];
            error_log("inter: ".json_encode($inter));
            $p1 = $inter['publicacionOferta'];
            $p1 = (array) $publiDB->getFirst(['id'=>$p1])[0];
            $p2 = $inter['publicacionOfertada'];
            $p2 = (array) $publiDB->getFirst(['id'=>$p2])[0];
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
                $redirect = (array) $intercambioDB->getFirst(['publicacionOferta' => $p1['id'], 'publicacionOfertada' => $p2['id'], 'estado' => 'pendiente']);
                $redirect = $redirect[0];
                enviarNotificacion($otroUser, $mensaje, './Intercambio');
                enviarNotificacion($userActual, $mensaje2, './Intercambio');

                $user = (array)((array)$userDB->getFirst(['username' => $otroUser]))[0];
                $user2 = (array)((array)$userDB->getFirst(['username' => $userActual]))[0];
                global $mailer;
                
                if ($user['notificacion']){
                    $mailer->send($user['mail'], 'Notificacion de Intercambio!', $mensaje, true);
                }                
                if ($user2['notificacion']){
                    $mailer->send($user2['mail'], 'Notificacion de Intercambio!', $mensaje2, true);
                }
                if (array_key_exists('setestado',$bodyParams) && $bodyParams['setestado']=="concretado"){
                    try{
                        // esta parte no manda notificaciones, habria que refactorizar un cacho
                        $intercambioDB->update(['publicacionOferta' => $p1['id'], 'estado'=>'pendiente', 'setestado' => 'cancelado']);
                        $intercambioDB->update(['publicacionOfertada' => $p2['id'], 'estado' => 'pendiente','setestado' => 'cancelado']);
                        $intercambioDB->update(['publicacionOferta' => $p2['id'], 'setestado' => 'cancelado', 'estado' => 'pendiente']);
                        $intercambioDB->update(['publicacionOfertada' => $p1['id'], 'setestado' => 'cancelado', 'estado' => 'pendiente']);
                    }catch (Exception $e){
                        error_log($e);
                    }
                }
            }
        }

        $res->getBody()->write(json_encode($msgReturn));
        return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
    });
});

?>