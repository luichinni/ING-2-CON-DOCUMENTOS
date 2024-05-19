<?php
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
require_once __DIR__ . '/../utilities/generadorQuerys.php';

$camposUser = [
    "username" => "varchar",
    "clave" => "varchar",
    "nombre" => "varchar",
    "apellido" => "varchar",
    "dni" => "int",
    "mail" => "varchar",
    "telefono" => "int",
    "rol" => "enum",
];

function userValidator(array $data, PDO $pdo, array $camposUser){
    $valid = true;
    $roles = ['admin', 'user', 'volunt'];
    match ($valid){
        // menos de 50 chars y que no esté utilizado
        (array_key_exists('username', $data)) && ((strlen($data['username']) > 50) || (existeUsuario(array('username' => $data['username']), $pdo, $camposUser))) => $valid = false,
        // menos de 50 char y que tenga más de 6
        (array_key_exists('clave', $data)) && ((strlen($data['clave']) > 50) || (strlen($data['clave']) < 6)) => $valid = false,
        // mayor a 2 letras
        (array_key_exists('nombre', $data)) && (strlen($data['nombre']) < 2) => $valid = false,
        // mayor a 2 letras
        (array_key_exists('apellido', $data)) && (strlen($data['apellido']) < 2) => $valid = false,
        // que sea solo numerico
        (array_key_exists('dni', $data)) && (!ctype_digit($data['dni'])) => $valid = false,
        // que tenga @ y que no sea utilizado por nadie
        (array_key_exists('mail', $data)) && ((strpos($data['mail'], '@') === false) || (existeUsuario(array('mail' => $data['mail']), $pdo, $camposUser))) => $valid = false,
        // que sea solo numerico
        (array_key_exists('telefono', $data)) && (!ctype_digit($data['telefono'])) => $valid = false,
        // rol existente
        (array_key_exists('rol', $data)) && (!in_array($data['rol'], $roles)) => $valid = false,
        default => $valid = true
    };
    return $valid;
}



function existeUsuario(array $queryParams, PDO $pdo, array $camposUser){
    // armar query SELECT * FROM `usuarios` WHERE params LIMIT 1
    $querySql = generarSelect('usuarios',$camposUser,$queryParams);
    $querySql .= "LIMIT 1";
    // ejecutar query y retornar si se obtuvo o no algun user
    $return = $pdo->query($querySql)->rowCount();
    return $return > 0;
}

function obtenerUsuario(array $queryParams,PDO $pdo,array $camposUser){
    // armar query SELECT * FROM `usuarios` WHERE params LIMIT 1
    $querySql = generarSelect('usuarios',$camposUser,$queryParams);
    $querySql .= "LIMIT 1";
    // ejecutar query y retornar json
    $return = $pdo->query($querySql)->fetchAll();
    return $return;
}

function listarUsuarios(array $queryParams,PDO $pdo, array $camposUser, int $limite = 20){
    // armar query SELECT * FROM `usuarios` WHERE params LIMIT 1
    $querySql = generarSelect('usuarios',$camposUser,$queryParams);
    $querySql .= "LIMIT $limite";
    if (array_key_exists('pag', $queryParams) && $queryParams['pag'] >= 0) {
        $querySql .= " OFFSET " . ($queryParams['pag'] * 20);
    }
    // ejecutar query y retornar json
    return $pdo->query($querySql)->fetchAll();
}

$app->group('/public', function (RouteCollectorProxy $group) use ($pdo,$camposUser) {
    //obtener usuario
    $group->get('/obtenerUsuario', function (Request $req,Response $res, $args) use ($pdo,$camposUser){
        // obtener los parametros de la query
        $queryParams = $req->getQueryParams();

        // obtener user
        $userValid = existeUsuario($queryParams,$pdo,$camposUser);
        //comprobar existencia
        $msgResponse = [];
        $status = 200;
        if(!$userValid){
            $msgResponse = [
                'error' => 'No existe el usuario'
            ];
            $status = 404;
        }else{
            $msgResponse = obtenerUsuario($queryParams,$pdo,$camposUser)[0];
        }

        //retornar user
        $res->getBody()->write(json_encode($msgResponse));
        return $res->withHeader('Content-Type', 'application/json')->withStatus($status);
    });

    //listar usuarios
    $group->get('/listarUsuarios',function (Request $req, Response $res, $args) use ($pdo,$camposUser){
        // obtener los parametros de la query
        $queryParams = $req->getQueryParams();

        $sqlRes = listarUsuarios($queryParams,$pdo,$camposUser);

        $res->getBody()->write(json_encode($sqlRes));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $group->post('/newUsuario',function (Request $req, Response $res, $args) use ($pdo,$camposUser){
        $msgResponse = [
            'error' => 'No fue posible registrar el usuario'
        ];
        $status = 409;
        // separar querys
        $bodyParams = (array) $req->getParsedBody();

        $valid = userValidator($bodyParams,$pdo,$camposUser);
        if ($valid){
            //chequear todos los campos
            $contador = 0;
            foreach ($bodyParams as $key => $value) {
                if (array_key_exists($key, $camposUser)) {
                    $contador++;
                }
            }
            $msgResponse = [
                'Exito' => 'Usuario registrado con exito'
            ];
            $status = 200;
            //comprobar existencia
            $tryUser = existeUsuario(array('username' => $bodyParams['username']), $pdo, $camposUser);
            if ($tryUser) {
                $msgResponse = [
                    'error' => 'Ya existe el usuario',
                ];
                $status = 409; // conflict
            } else {
                if ($contador < count($camposUser) - 1) {
                    $msgResponse = [
                        'error' => 'Faltan datos',
                    ];
                    $status = 500;
                } else {
                    // armar querysql
                    $querySql = generarInsert('usuarios',$camposUser,$bodyParams);
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

    $group->delete('/deleteUsuario',function (Request $req,Response $res, $args) use ($pdo,$camposUser){
        //obtener params de la query url
        $queryParams = $req->getQueryParams();

        $tryUsers = existeUsuario($queryParams,$pdo,$camposUser);

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
            $querySql = generarDelete('usuarios',$camposUser,$queryParams);
            $queryWhere = armarWhere($queryParams,$camposUser);
            
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

    $group->put('/updateUsuario',function(Request $req,Response $res, $args) use ($pdo,$camposUser){
        $bodyParams = (array) $req->getParsedBody();
        $valid = existeUsuario($bodyParams,$pdo,$camposUser);
        $msgResponse = [
            'Error' => 'No fue posible actualizar el usuario'
        ];
        $status = 500;

        if ($valid){
            // UPDATE `usuarios` SET `dni` = '1234' WHERE `username` = 'claudio'
            $querySql = generarUpdate('usuarios',$camposUser,$bodyParams);
            try {
                $pdo->prepare($querySql)->execute();
                if (array_key_exists('centro',$bodyParams) && array_key_exists('username',$bodyParams) && existeUsuario(array('username'=>$bodyParams['username'],'rol'=>'volunt'),$pdo,$camposUser)){
                    // borrar otro centrovolun
                    borrarCentroVolun(array('user'=>$bodyParams['username']),$pdo);
                    // cargar el nuevo
                    agregarCentroVolun(array('user'=>$bodyParams['username'],'centro'=>$bodyParams['centro']),$pdo);
                }
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