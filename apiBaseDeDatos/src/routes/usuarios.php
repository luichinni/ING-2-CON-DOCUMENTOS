<?php
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

require_once __DIR__ . '/../utilities/bdController.php';

$camposUser = [
    "username" => "varchar",
    "clave" => "varchar",
    "nombre" => "varchar",
    "apellido" => "varchar",
    "dni" => "int",
    "mail" => "varchar",
    "telefono" => "?int",
    "fecha_registro"=>'?datetime',
    "rol" => "enum",
];

$userDB = new bdController('usuarios',$pdo,$camposUser);

function userValidator(array $data, PDO $pdo, array $camposUser){
    global $userDB;
    $valid = ['invalido'=>''];
    $roles = ['admin', 'user', 'volunt'];
    match (true){
        // menos de 50 chars y que no esté utilizado
        (array_key_exists('username', $data)) && ((strlen($data['username']) > 50) || ($userDB->exists(array('username' => $data['username'])))) => $valid['invalido'] = 'username',
        // menos de 50 char y que tenga más de 6
        (array_key_exists('clave', $data)) && ((strlen($data['clave']) > 50) || (strlen($data['clave']) < 6)) => $valid['invalido'] = 'clave',
        // mayor a 2 letras
        (array_key_exists('nombre', $data)) && (strlen($data['nombre']) < 2) => $valid['invalido'] = 'nombre',
        // mayor a 2 letras
        (array_key_exists('apellido', $data)) && (strlen($data['apellido']) < 2) => $valid['invalido'] = 'apellido',
        // que sea solo numerico
        (array_key_exists('dni', $data)) && (!ctype_digit($data['dni'])) => $valid['invalido'] = 'dni',
        // que tenga @ y que no sea utilizado por nadie
        (array_key_exists('mail', $data)) && ((strpos($data['mail'], '@') === false) || ($userDB->exists(array('mail' => $data['mail'])))) => $valid['invalido'] = 'mail',
        // que sea solo numerico
        (array_key_exists('telefono', $data)) && (!ctype_digit($data['telefono'])) => $valid['invalido'] = 'telefono',
        // rol existente
        (array_key_exists('rol', $data)) && (!in_array($data['rol'], $roles)) => $valid['invalido'] = 'rol',
        default => $valid = null
    };
    return $valid;
}

function cancelarIntercambios(string $user){
    global $intercambioDB, $publiDB;
    $publis = (array) json_decode($publiDB->getAll(['user'=>$user]));
    //error_log(json_encode($publis));
    foreach ($publis as $public){
        $publi = (array) $public;
        $intercambioDB->update(['setestado' => 'cancelado', 'publicacion1' => $publi['id']]);
        $intercambioDB->update(['setestado' => 'cancelado', 'publicacion2' => $publi['id']]);
    }
}

$app->group('/public', function (RouteCollectorProxy $group) use ($pdo,$camposUser) {
    //obtener usuario
    $group->get('/obtenerUsuario', function (Request $req,Response $res, $args){
        global $userDB;
        $status = 404;
        $msgReturn = ['Mensaje' => 'Usuario no encontrado'];
        // obtener los parametros de la query
        $queryParams = $req->getQueryParams();

        $where = $userDB->getWhereParams($queryParams);

        if (empty($where)){
            $msgReturn['Mensaje'] = 'Necesita parametros para buscar';
            $res->getBody()->write(json_encode($msgReturn));
            return $res->withHeader('Content-Type', 'application/json')->withStatus($status);
        } 

        $existe = $userDB->exists($where,true);

        if (!$existe) {
            $res->getBody()->write(json_encode($msgReturn));
            return $res->withHeader('Content-Type', 'application/json')->withStatus($status);
        } 

        $user = (array) json_decode($userDB->getFirst($where,true));
        $user['Mensaje'] = 'Usuario encontrado';

        $res->getBody()->write(json_encode($user));
        $status = 200;

        $msgReturn['Mensaje'] = 'Usuario encontrado con éxito';

        return $res->withHeader('Content-Type', 'application/json')->withStatus($status);
    });

    //listar usuarios
    $group->get('/listarUsuarios',function (Request $req, Response $res, $args){
        global $userDB;
        $status = 404;
        $msgReturn = ['Mensaje'=>'Usuarios no encontrados'];
        // obtener los parametros de la query
        $queryParams = $req->getQueryParams();

        $where = $userDB->getWhereParams($queryParams);

        if (!$userDB->exists($where,true)) {
            $res->getBody()->write(json_encode($msgReturn));
            return $res->withHeader('Content-Type', 'application/json')->withStatus($status);
        }

        $offset = (array_key_exists('pag',$queryParams)) ? $queryParams['pag'] : 0;

        $user = json_decode($userDB->getFirst($where,true,20, $offset));

        $user['Mensaje'] = 'Usuarios encontrados con éxito';

        $res->getBody()->write(json_encode($user));
        $status = 200;

        return $res->withHeader('Content-Type', 'application/json')->withStatus($status);
    });

    $group->post('/newUsuario',function (Request $req, Response $res, $args) use ($pdo,$camposUser){
        global $userDB;
        $pudo = false;
        $status = 500;
        $msgReturn = ['Mensaje' => 'Faltan parametros'];

        $bodyParams = (array) $req->getParsedBody();
        $where = $userDB->getWhereParams($bodyParams); // esto es para los values

        if (empty($where)) {
            $res->getBody()->write(json_encode($msgReturn));
            return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        $campoInv = userValidator($bodyParams, $pdo, $camposUser);
        $valid = ($campoInv == null) && !$userDB->exists($where);
        
        if (!$valid) {
            $msgReturn['Mensaje'] = "El campo " . $campoInv['invalido'] . " no es válido";
            $res->getBody()->write(json_encode($msgReturn));
            return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        $pudo = $userDB->insert($bodyParams);

        $status = ($pudo) ? 200 : $status;

        $msgReturn['Mensaje'] = $status == 200 ? 'Usuario agregado con éxito' : 'Ocurrio un error al agregar el usuario';

        $res->getBody()->write(json_encode($msgReturn));

        return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
    });
    $group->post('/probarIntercambio', function (Request $req, Response $res, $args) use ($pdo, $camposUser) {
        cancelarIntercambios('user2');
        return $res->withStatus(200)->withHeader('Content-Type', 'application/json');
    });
    $group->post('/newVoluntario', function (Request $req, Response $res, $args) use ($pdo, $camposUser) {
        global $userDB, $centroDB, $publiDB, $centroVolunDB;
        $pudo = false;
        $status = 500;
        $msgReturn = ['Mensaje' => 'Faltan parametros'];

        $bodyParams = (array) $req->getParsedBody();

        if (!array_key_exists('centro',$bodyParams) || !array_key_exists('username',$bodyParams)) {
            $res->getBody()->write(json_encode($msgReturn));
            return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        $validUser = $userDB->exists(['username'=>$bodyParams['username']]);
        $validCentro = $centroDB->exists(['id'=>$bodyParams['centro']]);

        if (!$validUser || !$validCentro){
            $msgReturn['Mensaje'] = 'El centro no es válido';
            $res->getBody()->write(json_encode($msgReturn));
            return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        $existeCentroVolun = $centroVolunDB->exists(['user'=>$bodyParams['username'],'centro'=>$bodyParams['centro']]);

        if ($existeCentroVolun){
            $msgReturn['Mensaje'] = 'El voluntario ya está registrado en este centro';
            $res->getBody()->write(json_encode($msgReturn));
            return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        cancelarIntercambios($bodyParams['username']);

        $pudo = $publiDB->update(['user'=>$bodyParams['username'],'setestado'=>'baja']);

        $pudo = $pudo && $userDB->update(['setrol'=>'volunt','username'=>$bodyParams['username']]);

        $status = ($pudo) ? 200 : $status;

        $msgReturn['Mensaje'] = $status == 200 ? 'Voluntario agregado con éxito' : 'Ocurrio un error al agregar el voluntario';

        $res->getBody()->write(json_encode($msgReturn));

        return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
    });

    $group->delete('/deleteUsuario',function (Request $req,Response $res, $args){
        global $userDB;
        $pudo = false;
        $status = 500;
        $msgReturn = ['Mensaje' => 'No existe usuario que cumpla con los datos ingresados'];

        $queryParams = $req->getQueryParams();
        $where = $userDB->getWhereParams($queryParams);

        if (!$userDB->exists($where)) {
            $res->getBody()->write(json_encode($msgReturn));
            return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        $pudo = $userDB->delete($where);

        $status = ($pudo) ? 200 : $status;

        $msgReturn['Mensaje'] = $status == 200 ? 'Usuario eliminado con éxito' : 'Ocurrio un error al eliminar el usuario';

        $res->getBody()->write(json_encode($msgReturn));

        return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
    });

    $group->put('/updateUsuario',function(Request $req,Response $res, $args) use ($pdo,$camposUser){
        global $userDB;
        $pudo = false;
        $status = 500;
        $msgReturn = ['Mensaje' => 'Faltan datos para identificar y modificar el usuario'];

        $bodyParams = (array) $req->getParsedBody();
        $where = $userDB->getWhereParams($bodyParams);

        if (empty($where) || !$userDB->exists($where)) {
            $res->getBody()->write(json_encode($msgReturn));
            return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
        }

        $pudo = $userDB->update($bodyParams);

        $status = ($pudo) ? 200 : $status;

        $msgReturn['Mensaje'] = $status == 200 ? 'Usuario actualizado con éxito' : 'Ocurrio un error al actualizar el usuario';

        $res->getBody()->write(json_encode($msgReturn));

        return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
    });
});