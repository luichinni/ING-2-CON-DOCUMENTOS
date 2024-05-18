<?php
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../utilities/generadorQuerys.php';
// //obtener, 
// //validar, 
// //borrar, 
// editar, ESTE NO IRIA, SOLO HAY CENTRO Y VOLUNT
// //agregar, 
// listar SOLUCIONADO EN EL OBTENER CON LIMIT
// CentroVolun = ((centro(FK),voluntario(FK))(PK))
$camposCentroVolun = [
    "user" => "varchar",
    "centro" => "int"
];

function borrarCentroVolun(array $valuesWhere, PDO $pdo){
    global $camposCentroVolun;
    $querySql = generarDelete('centro_volun',$camposCentroVolun,$valuesWhere);
    $where = armarWhere($valuesWhere,$camposCentroVolun);
    $pudo = false;
    if($where != ""){
        try{
            $pdo->query($querySql)->execute();
            $pudo = true;
        }catch (Exception $e){
            $pudo = false;
        }
    }
    return $pudo;
}

function validarCentroVolun(array $valuesWhere, PDO $pdo){
    global $camposCentroVolun;
    $tieneCampos = array_key_exists('voluntario', $valuesWhere) && array_key_exists('centro', $valuesWhere);
    $valido = false;
    if($tieneCampos){
        $querySql = generarSelect('centro_volun', $camposCentroVolun, $valuesWhere);
        $valido = $pdo->query($querySql)->rowCount() > 0;
    }
    return $valido;
}

function listarCentroVolun(array $valuesWhere,PDO $pdo){
    return obtenerCentroVolun($valuesWhere,$pdo,null);
}

function obtenerCentroVolun(array $valuesWhere, PDO $pdo, ?int $limit = 1){
    global $camposCentroVolun;
    $querySql = generarSelect('centro_volun',$camposCentroVolun,$valuesWhere);
    if ($limit != null){
        $querySql .= "LIMIT $limit";
    }
    $return = $pdo->query($querySql)->fetchAll();
    return $return;
}

function agregarCentroVolun(array $datosIn, PDO $pdo){
    global $camposCentroVolun;
    $msgResponse = ['Exito' => 'Voluntario agregado con exito al centro'];
    // INSERT INTO `centro_volun` (`centro`, `voluntario`) VALUES ('', '')
    $querySql = generarInsert('centro_volun',$camposCentroVolun,$datosIn);
    // return response
    try {
        $pdo->prepare($querySql)->execute();
    } catch (Exception $e) {
        $msgResponse = [
            'error' => 'Ocurrio un error inesperado'
        ];
    }
    return $msgResponse;
}

?>