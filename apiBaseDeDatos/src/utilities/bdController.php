<?php

class bdController{
    
    private int $obligatorios = 0;

    function __construct(private string $tableName, private PDO $pdo, private array $camposTabla)
    {
        foreach($this->camposTabla as $key => $value){
            if (!str_starts_with($value,'?')){
                $this->obligatorios++;
            }
        }
        $camposTabla = $this->arrayToLower($camposTabla);
    }

    private function arrayToLower(array $arr)
    {
        $newArr = [];
        foreach ($arr as $key => $value) {
            $newArr[strtolower($key)] = strtolower($value);
        }
        return $newArr;
    }

    public function exists(array $whereParams, bool $like = false){
        $querySelect = $this->generarSelect($whereParams, null, $like);
        $opSql = $this->pdo->query($querySelect);
        $existe = false;
        if ($opSql->rowCount() > 0) {
            $existe = true;
        }
        return $existe;
    }

    public function getSetParams(array $params){
        $arrReturn = [];
        foreach ($params as $key => $value) {
            if (str_starts_with($key, 'set') && array_key_exists(substr($key, 3), $this->camposTabla)) {
                $arrReturn[substr($key, 3)] = $value;
            }
        }
        return $arrReturn;
    }

    public function getWhereParams(array $params){
        $arrReturn = [];
        foreach ($params as $key => $value){
            if (array_key_exists($key,$this->camposTabla)){
                $arrReturn[$key] = $value;
            }
        }
        return $arrReturn;
    }

    public function delete(array $whereParams, bool $deleteSinWhere = false){
        $pudo = false;
        $where = $this->armarWhere($whereParams);

        if ($where != "" || $deleteSinWhere){
            $whereQuery = $this->generarDelete($whereParams);
            $pudo = $this->pdo->query($whereQuery)->execute();
        }

        return $pudo;
    }

    public function update(array $queryParams){
        $queryUpdate = $this->generarUpdate($queryParams);
        error_log($queryUpdate);
        return $this->pdo->query($queryUpdate)->execute();
    }

    public function insert(array $datosIn){
        $pudo = false;
        //error_log(json_encode($datosIn));
        $contador = 0;
        foreach ($datosIn as $key => $value) {
            if (array_key_exists($key, $this->camposTabla)) {
                $contador++;
            }
        }
        
        //error_log("Campos necesarios: $contador/$this->obligatorios ->" . json_encode($contador >= $this->obligatorios));
        if ($contador >= $this->obligatorios){
            $queryInsert = $this->generarInsert($datosIn);
            $pudo = $this->pdo->prepare($queryInsert)->execute();
            //error_log($queryInsert);
        }
        return $pudo;
    }

    /**
     * @param array $whereParams Array asociativo de los valores para el where
     * ``` $valuesWhere = [
     *      'nombre_campo' => 'valor',
     *      'username' => 'pepe'
     * ]```
     * @param bool $like define si en el where se compara por exactos o coincidencias %valor%
     * @param ?int $limit define la cantidad de lineas a retornar
     */
    public function getFirst(array $whereParams, bool $like = false, int $limit = 1, int $offset = 0){
        $querySelect = $this->generarSelect($whereParams,$limit,$like,$offset);
        $result = $this->pdo->query($querySelect)->fetchAll();
        if ($result == false){
            $result = [];
        }
        $json = json_encode($result);
        return ($json == false) ? '{}' : json_encode($result); // esto retorna un json con los objetos, si está vacio retorna {}
    }

    /**
     * @param array $whereParams Array asociativo de los valores para asignar a cada campo
     * ``` $whereParams = [
     *      'nombre_campo' => 'valor',
     *      'username' => 'pepe'
     * ]```
     * @param bool $like define si en el where se compara por exactos o coincidencias %valor%
     */
    public function getAll(array $whereParams, bool $like = false){
        $querySelect = $this->generarSelect($whereParams, null, $like);
        //error_log($querySelect);
        $result = $this->pdo->query($querySelect)->fetchAll();
        if ($result == false) {
            $result = [];
        }
        $json = json_encode($result); 
        return ($json == false) ? '{}' : $json; // esto retorna un json con los objetos, si está vacio retorna {}
    }

    /**
     * @param array $valuesIn Array asociativo de los valores para asignar a cada campo
     * ``` $valuesIn = [
     *      'nombre_campo' => 'valor',
     *      'username' => 'pepe'
     * ]```
     * 
     * Se insertaran solo aquellos campos que aparezcan en $valuesIn, si un campo no aparece no será tomado en cuenta.
     */
    private function generarInsert(array $valuesIn)
    {
        // INSERT INTO `centro_volun` (`centro`, `voluntario`) VALUES ('', '')
        $querySql = "INSERT INTO `$this->tableName` (";
        // armar parte de los campos
        foreach ($valuesIn as $key => $value) {
            if (array_key_exists($key, $this->camposTabla)) {
                $querySql .= "`$key`,";
            }
        }
        $querySql = substr($querySql, 0, strlen($querySql) - 1) . ") VALUES (";
        // armar parte de los valores
        foreach ($valuesIn as $key => $value) {
            if (array_key_exists($key, $this->camposTabla)) {
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
     * @param bool $like define si en el where se compara por exactos o coincidencias %valor%
     */
    private function armarWhere(array $whereParams, bool $like = false)
    {
        $queryWhere = "WHERE ";
        $querySize = strlen($queryWhere);
        foreach ($whereParams as $key => $value) { // para cada param
            if (array_key_exists($key, $this->camposTabla) && $value != "") { // si existe en la lista de campos
                if ($value == "null") { // si es null en la query
                    $queryWhere .= "`$key` IS NULL ";
                } else {
                    switch ($this->camposTabla[$key]) {
                        case '?int': // se acumula con int
                        case 'int': // si es numero
                        case 'bool':
                        case '?bool':
                            $queryWhere .= "`$key`=$value ";
                            break;
                        case 'time':
                        case 'timestamp':
                        case '?timestamp':
                        case '?datetime':
                        case 'datetime':
                            if ($like) {
                                $queryWhere .= "`$key` LIKE '%$value%' ";
                            } else {
                                $queryWhere .= "`$key`=$value ";
                            }
                            break;
                        default: // si es otra cosa
                            if ($like) {
                                $queryWhere .= "`$key` LIKE '%$value%' ";
                            } else {
                                $queryWhere .= "`$key` LIKE '$value' ";
                            }
                            break;
                    }
                }
                $queryWhere .= "AND ";
            }
        }
        $queryWhere = substr($queryWhere,0,strlen($queryWhere)-4);

        if (strlen($queryWhere) <= $querySize) {
            $queryWhere = "";
        }

        return $queryWhere;
    }

    /**
     * @param array $whereParams Array asociativo de los valores para el where
     * ``` $valuesWhere = [
     *      'nombre_campo' => 'valor',
     *      'username' => 'pepe'
     * ]```
     * @param bool $like define si en el where se compara por exactos o coincidencias %valor%
     * @param ?int $limit define la cantidad de lineas a retornar
     * @param int $offset numero de pagina arrancando en 0
     */
    private function generarSelect(array $whereParams, ?int $limit = 1,bool $like = false, int $offset = 0)
    {
        // SELECT * FROM `usuarios` WHERE params LIMIT 1
        $querySql = "SELECT * FROM `$this->tableName` " . $this->armarWhere($whereParams, $like);
        if ($limit != null){
            $querySql .= "LIMIT $limit OFFSET " . ($offset * $limit);
        }
        return $querySql;
    }

    /**
     * @param array $valuesIn Array asociativo de los valores para actualizar y para el filtrar con where
     * ``` $valuesIn = [
     *      'nombre_campo' => 'valor',
     *      'setnombre_campo' => 'valor',
     *      'username' => 'pepe',
     *      'setdni' => '1234'
     * ]```
     * 
     * Los campos normales son los que iran en el where, los campos son el prefijo set serán los actualizados por el valor
     * @param bool $like define si en el where se compara por exactos o coincidencias %valor%
     */
    private function generarUpdate(array $valuesIn, bool $like = false)
    {
        //UPDATE `usuarios` SET `dni` = '1234' WHERE `username` = 'claudio'
        $querySql = "UPDATE `$this->tableName` SET ";
        // ACA TIENEN QUE MANDAR setdni=1234 o setusername=nuevo_user
        $setParams = $this->getSetParams($valuesIn);
        foreach ($setParams as $key => $value) {
            if (array_key_exists($key, $this->camposTabla)) {
                $querySql .= "`$key` = '$value', ";
            }
        }
        $querySql = substr(
            $querySql,
            0,
            strlen($querySql) - 2
        ) . ' ';
        $querySql .= $this->armarWhere($valuesIn, $like);

        return $querySql;
    }

    /**
     * @param array $valuesWhere Array asociativo de los valores para el where
     * ``` $valuesWhere = [
     *      'nombre_campo' => 'valor',
     *      'username' => 'pepe'
     * ]```
     * @param bool $like define si en el where se compara por exactos o coincidencias %valor%
     */
    private function generarDelete(array $whereParams, bool $like = false)
    {
        // DELETE FROM `usuarios` WHERE COSAS
        $querySql = "DELETE FROM `$this->tableName` " . $this->armarWhere($whereParams, $like);
        return $querySql;
    }
}

?>