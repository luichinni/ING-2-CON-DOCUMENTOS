<?php
// Imagen = (archivo,publicacion(FK))
// obtener
// eliminar
require_once __DIR__ . '/../utilities/bdController.php';
/*
CREATE TABLE imagen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    archivo mediumtext,
    publicacion int,
    FOREIGN KEY (publicacion) REFERENCES publicacion (id)
);
*/

$camposImg = [
    "id" => [
        "pk" => true,
        "tipo" => "int",
        "autoincrement" => true,
        "comparador" => "="
    ],
    'archivo' => [
        "tipo" => "mediumtext",
        "comparador" => "like"
    ],
    'publicacion' => [
        "tipo" => "int",
        "comparador" => "like",
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
    return $imgDB->getAll($whereParams);
}

function obtenerImg(array $whereParams){
    global $imgDB;
    return $imgDB->getFirst($whereParams);
}

function eliminarImg(array $whereParams){
    global $imgDB;
    $pudo = false;

    if (!$imgDB->exists($whereParams)) return $pudo;

    return $imgDB->delete($whereParams);
}

?>