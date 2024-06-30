<?php

require_once __DIR__ . '/../utilities/bdController.php';

require_once __DIR__ . '/../models/imgDb.php';

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