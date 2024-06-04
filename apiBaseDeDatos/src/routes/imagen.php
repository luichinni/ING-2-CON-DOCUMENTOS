<?php
// Imagen = (archivo,publicacion(FK))
// obtener
// eliminar
require_once __DIR__ . '/../utilities/bdController.php';

$camposImg = [
    'archivo' => [
        "tipo" => "mediumtext",
        "comparador" => "like",
        "opcional" => false
    ],
    'publicacion' => [
        "tipo" => "int",
        "comparador" => "=",
        "opcional" => false,
        "fk" => [
            "tabla" => "publicacion",
            "campo" => "id"
        ]
    ]
];

$imgDB = new bdController('imagen',$pdo,$camposImg);

function agregarImg(array $valueParams){
    global $imgDB;
    $pudo = false;

    if ($imgDB->exists($valueParams)) return $pudo;
    
    return $imgDB->insert($valueParams);
}

function listarImg(array $whereParams){
    global $imgDB;
    return json_decode($imgDB->getAll($whereParams));
}

function obtenerImg(array $whereParams){
    global $imgDB;
    return json_decode($imgDB->getFirst($whereParams));
}

function eliminarImg(array $whereParams){
    global $imgDB;
    $pudo = false;

    if (!$imgDB->exists($whereParams)) return $pudo;

    return $imgDB->delete($whereParams);
}

?>