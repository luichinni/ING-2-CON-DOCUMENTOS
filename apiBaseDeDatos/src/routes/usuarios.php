<?php
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
require_once __DIR__ . '/../utilities/bdController.php';

$camposUser = [
    "username" => "varchar",
    "clave" => "varchar",
    "nombre" => "varchar",
    "apellido" => "varchar",
    "dni" => "int",
    "mail" => "varchar",
    "telefono" => "?int",
    "rol" => "enum",
];

$userDB = new bdController('usuarios',$pdo,$camposUser);

function userValidator(array $data, PDO $pdo, array $camposUser){
    global $userDB;
    $valid = true;
    $roles = ['admin', 'user', 'volunt'];
    match ($valid){
        // menos de 50 chars y que no esté utilizado
        (array_key_exists('username', $data)) && ((strlen($data['username']) > 50) || ($userDB->exists(array('username' => $data['username'])))) => $valid = false,
        // menos de 50 char y que tenga más de 6
        (array_key_exists('clave', $data)) && ((strlen($data['clave']) > 50) || (strlen($data['clave']) < 6)) => $valid = false,
        // mayor a 2 letras
        (array_key_exists('nombre', $data)) && (strlen($data['nombre']) < 2) => $valid = false,
        // mayor a 2 letras
        (array_key_exists('apellido', $data)) && (strlen($data['apellido']) < 2) => $valid = false,
        // que sea solo numerico
        (array_key_exists('dni', $data)) && (!ctype_digit($data['dni'])) => $valid = false,
        // que tenga @ y que no sea utilizado por nadie
        (array_key_exists('mail', $data)) && ((strpos($data['mail'], '@') === false) || ($userDB->exists(array('mail' => $data['mail'])))) => $valid = false,
        // que sea solo numerico
        (array_key_exists('telefono', $data)) && (!ctype_digit($data['telefono'])) => $valid = false,
        // rol existente
        (array_key_exists('rol', $data)) && (!in_array($data['rol'], $roles)) => $valid = false,
        default => $valid = true
    };
    return $valid;
}

$app->group('/public', function (RouteCollectorProxy $group) use ($pdo,$camposUser) {
    //obtener usuario
    $group->get('/obtenerUsuario', function (Request $req,Response $res, $args){
        global $userDB;
        $status = 404;
        // obtener los parametros de la query
        $queryParams = $req->getQueryParams();

        $where = $userDB->getWhereParams($queryParams);

        if (empty($where)) return $res->withHeader('Content-Type', 'application/json')->withStatus($status);

        $existe = $userDB->exists($where);

        if (!$existe) return $res->withHeader('Content-Type', 'application/json')->withStatus($status);

        $user = $userDB->getFirst($where);

        $res->getBody()->write($user);
        $status = 200;

        return $res->withHeader('Content-Type', 'application/json')->withStatus($status);
    });

    //listar usuarios
    $group->get('/listarUsuarios',function (Request $req, Response $res, $args){
        global $userDB;
        $status = 404;
        // obtener los parametros de la query
        $queryParams = $req->getQueryParams();

        $where = $userDB->getWhereParams($queryParams);

        if (empty($where) || !$userDB->exists($where)) return $res->withHeader('Content-Type', 'application/json')->withStatus($status);

        $offset = (array_key_exists('pag',$queryParams)) ? $queryParams['pag'] : 0;

        $user = $userDB->getFirst($where,false,20, $offset);

        $res->getBody()->write($user);
        $status = 200;

        return $res->withHeader('Content-Type', 'application/json')->withStatus($status);
    });

    $group->post('/newUsuario',function (Request $req, Response $res, $args) use ($pdo,$camposUser){
        global $userDB;
        $pudo = false;
        $status = 500;

        $bodyParams = (array) $req->getParsedBody();
        $where = $userDB->getWhereParams($bodyParams); // esto es para los values

        if (empty($where)) return $res->withStatus($status)->withHeader('Content-Type', 'application/json');

        $valid = userValidator($bodyParams,$pdo,$camposUser) && !$userDB->exists($where);

        if (!$valid) return $res->withStatus($status)->withHeader('Content-Type', 'application/json');

        $pudo = $userDB->insert($bodyParams);

        $status = ($pudo) ? 200 : $status;

        return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
    });

    $group->delete('/deleteUsuario',function (Request $req,Response $res, $args){
        global $userDB;
        $pudo = false;
        $status = 500;

        $queryParams = $req->getQueryParams();
        $where = $userDB->getWhereParams($queryParams);

        if (!$userDB->exists($where)) return $res->withStatus($status)->withHeader('Content-Type', 'application/json');

        $pudo = $userDB->delete($where);

        $status = ($pudo) ? 200 : $status;

        return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
    });

    $group->put('/updateUsuario',function(Request $req,Response $res, $args) use ($pdo,$camposUser){
        global $userDB;
        $pudo = false;
        $status = 500;

        $bodyParams = (array) $req->getParsedBody();
        $where = $userDB->getWhereParams($bodyParams);

        if (empty($where) || !$userDB->exists($where)) return $res->withStatus($status)->withHeader('Content-Type', 'application/json');

        $pudo = $userDB->update($bodyParams);

        $status = ($pudo) ? 200 : $status;
            
        return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
    });
});