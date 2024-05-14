<?php
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$campos = [
    "username" => "varchar",
    "clave" => "varchar",
    "nombre" => "varchar",
    "apellido" => "varchar",
    "dni" => "int",
    "mail" => "varchar",
    "telefono" => "int",
    "rol" => "enum",
];

function userValidator(array $data, PDO $pdo, array $campos){
    $valid = true;
    $roles = ['admin', 'user', 'volunt'];
    match ($valid){
        // menos de 50 chars y que no esté utilizado
        (array_key_exists('username', $data)) && ((strlen($data['username']) > 50) || (existeUsuario(array('username' => $data['username']), $pdo, $campos))) => $valid = false,
        // menos de 50 char y que tenga más de 6
        (array_key_exists('clave', $data)) && ((strlen($data['clave']) > 50) || (strlen($data['clave']) < 6)) => $valid = false,
        // mayor a 2 letras
        (array_key_exists('nombre', $data)) && (strlen($data['nombre']) < 2) => $valid = false,
        // mayor a 2 letras
        (array_key_exists('apellido', $data)) && (strlen($data['apellido']) < 2) => $valid = false,
        // que sea solo numerico
        (array_key_exists('dni', $data)) && (!ctype_digit($data['dni'])) => $valid = false,
        // que tenga @ y que no sea utilizado por nadie
        (array_key_exists('mail', $data)) && ((strpos($data['mail'], '@') === false) || (existeUsuario(array('mail' => $data['mail']), $pdo, $campos))) => $valid = false,
        // que sea solo numerico
        (array_key_exists('telefono', $data)) && (!ctype_digit($data['telefono'])) => $valid = false,
        // rol existente
        (array_key_exists('rol', $data)) && (!in_array($data['rol'], $roles)) => $valid = false,
        default => $valid = true
    };
    return $valid;
}

function armarWhere(array $queryParams, array $campos){
    $queryWhere = "WHERE ";
    $querySize = strlen($queryWhere);
    foreach ($queryParams as $key => $value) { // para cada param
        if (array_key_exists($key, $campos)) { // si existe en la lista de campos
            if ($value == "null") { // si es null en la query
                $queryWhere .= "`$key` IS NULL ";
            } else {
                switch ($campos[$key]) {
                    case 'int': // si es numero
                        $queryWhere .= "`$key`=$value ";
                        break;
                    default: // si es otra cosa
                        $queryWhere .= "`$key` LIKE '$value' ";
                        break;
                }
            }
        }
    }

    if (strlen($queryWhere)<=$querySize){
        $queryWhere = "";
    }

    return $queryWhere;
}

function existeUsuario(array $queryParams, PDO $pdo, array $campos){
    // armar query SELECT * FROM `usuarios` WHERE params LIMIT 1
    $querySql = "SELECT * FROM `usuarios` ";
    $querySql .= armarWhere($queryParams, $campos);
    $querySql .= "LIMIT 1";
    // ejecutar query y retornar json
    $return = $pdo->query($querySql)->rowCount();
    return $return > 0;
}

function obtenerUsuario(array $queryParams,PDO $pdo,array $campos){
    // armar query SELECT * FROM `usuarios` WHERE params LIMIT 1
    $querySql = "SELECT * FROM `usuarios` ";
    $querySize = strlen($querySql);
    $querySql .= armarWhere($queryParams, $campos);
    $querySql .= "LIMIT 1";
    // ejecutar query y retornar json
    $return = $pdo->query($querySql)->fetchAll();
    return $return;
}

function listarUsuarios(array $queryParams,PDO $pdo, array $campos){
    // armar query SELECT * FROM `usuarios` WHERE params LIMIT 1
    $querySql = "SELECT * FROM `usuarios` ";
    $querySql .= armarWhere($queryParams, $campos);
    $querySql .= "LIMIT 20";
    if (array_key_exists('pag', $queryParams) && $queryParams['pag'] >= 0) {
        $querySql .= " OFFSET " . ($queryParams['pag'] * 20);
    }
    // ejecutar query y retornar json
    return $pdo->query($querySql)->fetchAll();
}

$app->group('/public', function (RouteCollectorProxy $group) use ($pdo,$campos) {
    //obtener usuario
    $group->get('/obtenerUsuario', function (Request $req,Response $res, $args) use ($pdo,$campos){
        // obtener los parametros de la query
        $queryParams = $req->getQueryParams();

        // obtener user
        $userValid = existeUsuario($queryParams,$pdo,$campos);
        //comprobar existencia
        $msgResponse = [];
        $status = 200;
        if(!$userValid){
            $msgResponse = [
                'error' => 'No existe el usuario'
            ];
            $status = 404;
        }else{
            $msgResponse = obtenerUsuario($queryParams,$pdo,$campos)[0];
        }

        //retornar user
        $res->getBody()->write(json_encode($msgResponse));
        return $res->withHeader('Content-Type', 'application/json')->withStatus($status);
    });

    //listar usuarios
    $group->get('/listarUsuarios',function (Request $req, Response $res, $args) use ($pdo,$campos){
        // obtener los parametros de la query
        $queryParams = $req->getQueryParams();

        $sqlRes = listarUsuarios($queryParams,$pdo,$campos);

        $res->getBody()->write(json_encode($sqlRes));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $group->post('/newUsuario',function (Request $req, Response $res, $args) use ($pdo,$campos){
        $msgResponse = [
            'error' => 'No fue posible registrar el usuario'
        ];
        $status = 409;
        // separar querys
        $bodyParams = (array) $req->getParsedBody();

        $valid = userValidator($bodyParams,$pdo,$campos);
        if ($valid){
            //chequear todos los campos
            $contador = 0;
            foreach ($bodyParams as $key => $value) {
                if (array_key_exists($key, $campos)) {
                    $contador++;
                }
            }
            $msgResponse = [
                'Exito' => 'Usuario registrado con exito'
            ];
            $status = 200;
            //comprobar existencia
            $tryUser = obtenerUsuario(array('username' => $bodyParams['username']), $pdo, $campos);
            if (count($tryUser) >= 1) {
                $msgResponse = [
                    'error' => 'Ya existe el usuario ' . $tryUser[0]['username'],
                ];
                $status = 409; // conflict
            } else {
                if ($contador < count($campos) - 1) {
                    $msgResponse = [
                        'error' => 'Faltan datos',
                    ];
                    $status = 500;
                } else {
                    // armar querysql
                    $querySql = "INSERT INTO `usuarios` (";
                    // por clave poner en insert
                    foreach ($bodyParams as $key => $value) {
                        if (array_key_exists($key, $campos)) {
                            $querySql .= "`$key`,";
                        }
                    }
                    $querySql = substr($querySql, 0, strlen($querySql) - 1) . ") VALUES (";
                    // por valor poner en el insert
                    foreach ($bodyParams as $key => $value) {
                        if (array_key_exists($key, $campos)) {
                            $querySql .= "'$value',";
                        }
                    }
                    $querySql = substr($querySql, 0, strlen($querySql) - 1) . ")";
                    // return response
                    try {
                        $pdo->prepare($querySql)->execute();
                    } catch (Exception $e) {
                        $msgResponse = [
                            'error' => 'Ocurrio un error inesperado'
                        ];
                        $status = 500; // internal server error
                    }
                }
            }
        }
        $res->getBody()->write(json_encode($msgResponse));
        return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
    });

    $group->delete('/deleteUsuario',function (Request $req,Response $res, $args) use ($pdo,$campos){
        //obtener params de la query url
        $queryParams = $req->getQueryParams();

        $tryUsers = existeUsuario($queryParams,$pdo,$campos);

        $msgResponse = [
            'Exito' => 'Usuario eliminado con exito'
        ];
        $status = 200;
        if (!$tryUsers){
            $msgResponse = [
                'error' => 'No existe usuario que cumpla con la descripcion'
            ];
            $status = 500;
        }else{
            $querySql = "DELETE FROM `usuarios` ";
            $queryWhere = armarWhere($queryParams, $campos);
            $querySql .= $queryWhere;
            
            $hayParams = false;
            if ($queryWhere != "") {
                $hayParams = true;
            }

            if ($hayParams) {
                try {
                    $pdo->prepare($querySql)->execute();
                } catch (Exception $e) {
                    $msgResponse = [
                        'error' => 'No fue posible eliminar, ocurrió un error inesperado'
                    ];
                    $status = 500;
                }
            } else {
                $msgResponse = [
                    'error' => 'No existe usuario que cumpla con la descripcion'
                ];
                $status = 500;
            }
        }

        $res->getBody()->write(json_encode($msgResponse));
        return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
    });

    $group->put('/updateUsuario',function(Request $req,Response $res, $args) use ($pdo,$campos){
        $bodyParams = (array) $req->getParsedBody();
        $valid = existeUsuario($bodyParams,$pdo,$campos);
        $msgResponse = [
            'Error' => 'No fue posible actualizar el usuario'
        ];
        $status = 500;

        if ($valid){
            // UPDATE `usuarios` SET `dni` = '1234' WHERE `username` = 'claudio'
            $querySql = 'UPDATE `usuarios` SET ';

            // ACA TIENEN QUE MANDAR setdni=1234 o setusername=nuevo_user
            $setParams = [];
            foreach ($bodyParams as $key => $value){
                if (str_starts_with($key,'set') && array_key_exists(substr($key, 3), $campos)){
                    $setParams[substr($key, 3)] = $value;
                }
            }
            foreach ($setParams as $key => $value){
                if (array_key_exists($key,$campos)){
                    $querySql .= "`$key` = '$value', ";
                }
            }
            $querySql = substr($querySql, 0, strlen($querySql) - 2);
            $querySql .= armarWhere($bodyParams,$campos);
            try {
                $pdo->prepare($querySql)->execute();
                $msgResponse = [
                    'Exito' => 'Usuario actualizado correctamente'
                ];
                $status = 200; // internal server error
            } catch (Exception $e) {
                $msgResponse = [
                    'error' => 'Ocurrio un error inesperado'
                ];
                $status = 500; // internal server error
            }
        }
        $res->getBody()->write(json_encode($msgResponse));
        return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
    });
});