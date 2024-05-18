<?php

/**
 * @param string $tableName Nombre de la tabla a operar
 * @param array $camposTabla Array asociativo de los campos de la tabla como 
 * ``` $camposTabla = [
 *      'nombre_campo' => 'tipo_de_dato',
 *      'username' => 'varchar'
 * ]```
 * @param array $valuesIn Array asociativo de los valores para asignar a cada campo
 * ``` $valuesIn = [
 *      'nombre_campo' => 'valor',
 *      'username' => 'pepe'
 * ]```
 * 
 * Se insertaran solo aquellos campos que aparezcan en $valuesIn, si un campo no aparece no será tomado en cuenta.
 */
function generarInsert(string $tableName,array $camposTabla, array $valuesIn){
    // INSERT INTO `centro_volun` (`centro`, `voluntario`) VALUES ('', '')
    $querySql = "INSERT INTO `$tableName` (";
    // armar parte de los campos
    foreach ($valuesIn as $key => $value) {
        if (array_key_exists($key, $camposTabla)) {
            $querySql .= "`$key`,";
        }
    }
    $querySql = substr($querySql, 0, strlen($querySql) - 1) . ") VALUES (";
    // armar parte de los valores
    foreach ($valuesIn as $key => $value) {
        if (array_key_exists($key, $camposTabla)) {
            $querySql .= "'$value',";
        }
    }
    $querySql = substr($querySql, 0, strlen($querySql) - 1) . ")";
    return $querySql;
}

/**
 * @param array $whereParams Array asociativo de los valores para asignar a cada campo
 * ``` $whereParams = [
 *      'nombre_campo' => 'valor',
 *      'username' => 'pepe'
 * ]```
 * @param array $camposTabla Array asociativo de los campos de la tabla como 
 * ``` $camposTabla = [
 *      'nombre_campo' => 'tipo_de_dato',
 *      'username' => 'varchar'
 * ]```
 * 
 * Se insertaran solo aquellos campos que aparezcan en $valuesWhere, si un campo no aparece no será tomado en cuenta.
 */
function armarWhere(array $whereParams, array $camposTabla){
    $queryWhere = "WHERE ";
    $querySize = strlen($queryWhere);
    foreach ($whereParams as $key => $value) { // para cada param
        if (array_key_exists($key, $camposTabla)) { // si existe en la lista de campos
            if ($value == "null") { // si es null en la query
                $queryWhere .= "`$key` IS NULL ";
            } else {
                switch ($camposTabla[$key]) {
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

/**
 * @param string $tableName Nombre de la tabla a operar
 * @param array $camposTabla Array asociativo de los campos de la tabla como 
 * ``` $camposTabla = [
 *      'nombre_campo' => 'tipo_de_dato',
 *      'username' => 'varchar'
 * ]```
 * @param array $valuesWhere Array asociativo de los valores para el where
 * ``` $valuesWhere = [
 *      'nombre_campo' => 'valor',
 *      'username' => 'pepe'
 * ]```
 */
function generarSelect(string $tableName, array $camposTabla, array $valuesWhere){
    // SELECT * FROM `usuarios` WHERE params LIMIT 1
    $querySql = "SELECT * FROM `$tableName` " . armarWhere($valuesWhere,$camposTabla);
    return $querySql;
}

/**
 * @param string $tableName Nombre de la tabla a operar
 * @param array $camposTabla Array asociativo de los campos de la tabla como 
 * ``` $camposTabla = [
 *      'nombre_campo' => 'tipo_de_dato',
 *      'username' => 'varchar'
 * ]```
 * @param array $valuesIn Array asociativo de los valores para actualizar y para el filtrar con where
 * ``` $valuesIn = [
 *      'nombre_campo' => 'valor',
 *      'setnombre_campo' => 'valor',
 *      'username' => 'pepe',
 *      'setdni' => '1234'
 * ]```
 * 
 * Los campos normales son los que iran en el where, los campos son el prefijo set serán los actualizados por el valor
 */
function generarUpdate(string $tableName, array $camposTabla, array $valuesIn){
    //UPDATE `usuarios` SET `dni` = '1234' WHERE `username` = 'claudio'
    $querySql = "UPDATE `$tableName` SET ";
    // ACA TIENEN QUE MANDAR setdni=1234 o setusername=nuevo_user
    $setParams = [];
    foreach ($valuesIn as $key => $value) {
        if (str_starts_with($key, 'set') && array_key_exists(substr($key, 3), $camposTabla)) {
            $setParams[substr($key, 3)] = $value;
        }
    }
    foreach ($setParams as $key => $value) {
        if (array_key_exists($key, $camposTabla)) {
            $querySql .= "`$key` = '$value', ";
        }
    }
    $querySql = substr($querySql, 0,
        strlen($querySql) - 2
    );
    $querySql .= armarWhere($valuesIn, $camposTabla);

    return $querySql;
}

/**
 * @param string $tableName Nombre de la tabla a operar
 * @param array $camposTabla Array asociativo de los campos de la tabla como 
 * ``` $camposTabla = [
 *      'nombre_campo' => 'tipo_de_dato',
 *      'username' => 'varchar'
 * ]```
 * @param array $valuesWhere Array asociativo de los valores para el where
 * ``` $valuesWhere = [
 *      'nombre_campo' => 'valor',
 *      'username' => 'pepe'
 * ]```
 */
function generarDelete(string $tableName, array $camposTabla, array $valuesWhere){
    // DELETE FROM `usuarios` WHERE COSAS
    $querySql = "DELETE FROM `$tableName` " . armarWhere($valuesWhere,$camposTabla);
    return $querySql;
}

?>