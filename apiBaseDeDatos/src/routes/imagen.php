<?php
// Imagen = (archivo,publicacion(FK))
// obtener
// eliminar
require_once __DIR__ . '/../utilities/generadorQuerys.php';

$camposImg = [
    'archivo' => 'varchar',
    'publicacion' => 'int'
];

function obtenerImg(array $whereParams, PDO $pdo){
    global $camposImg;
    $querySql = generarSelect('imagen',$camposImg,$whereParams);
    $imagenes = $pdo->query($querySql)->fetchAll();
    return $imagenes;
}

function eliminarImg(array $whereParams, PDO $pdo){
    global $camposImg;
    $where = armarWhere($whereParams,$camposImg);
    $pudo = false;
    if ($where != ""){
        $querySql = generarDelete('imagen',$camposImg,$whereParams);
        try{
            $pdo->query($querySql)->execute();
            $pudo = true;
        }catch (Exception $e){
            $pudo = false;
        }
    
    }
    return $pudo;
}

?>