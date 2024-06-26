<?php
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../utilities/bdController.php';
// //obtener, 
// //validar, 
// //borrar, 
// editar, ESTE NO IRIA, SOLO HAY CENTRO Y VOLUNT
// //agregar, 
// listar SOLUCIONADO EN EL OBTENER CON LIMIT
// CentroVolun = ((centro(FK),voluntario(FK))(PK))

/*
CREATE TABLE centro_volun (
    centro int,
    voluntario varchar(50),
    PRIMARY KEY (centro, voluntario),
    FOREIGN KEY (centro) REFERENCES centros(id),
    FOREIGN KEY (voluntario) REFERENCES usuarios (username)
);
*/

$camposCentroVolun = [
    "voluntario" => [
        "pk" => true,
        "tipo" => "varchar (50)",
        "comparador" => "like",
        "fk" => [
            "tabla" => "usuarios",
            "campo" => "username"
        ]
    ],
    "centro" => [
        "pk" => true,
        "tipo" => "int",
        "comparador" => "=",
        "fk" => [
            "tabla" => "centros",
            "campo" => "id"
        ]
    ]
];

$centroVolunDB = new bdController('centro_volun',$pdo,$camposCentroVolun);

function borrarCentroVolun(array $valuesWhere){
    global $centroVolunDB;
    $pudo = false;

    if (!$centroVolunDB->exists($valuesWhere)) return $pudo;

    return $centroVolunDB->delete($valuesWhere);
}

function validarCentroVolun(array $valuesWhere){
    global $centroVolunDB;
    return $centroVolunDB->exists($valuesWhere);
}

function listarCentroVolun(array $valuesWhere){
    return obtenerCentroVolun($valuesWhere,null);
}

function obtenerCentroVolun(array $valuesWhere, ?int $limit = 1){
    global $centroVolunDB;
    $retCV = '';
    if ($limit != null){
        $retCV = $centroVolunDB->getFirst($valuesWhere, false, $limit);
    }else{
        $retCV = $centroVolunDB->getAll($valuesWhere);
    }
    
    return $retCV;
}

function agregarCentroVolun(array $datosIn){
    global $centroVolunDB;
    $pudo = false;

    if ($centroVolunDB->exists($datosIn)) return $pudo;

    return $centroVolunDB->insert($datosIn);
}

?>