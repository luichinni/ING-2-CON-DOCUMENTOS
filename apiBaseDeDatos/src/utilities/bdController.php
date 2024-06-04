<?php

class bdController{

    /**
     * @param string $tableName - Nombre de la tabla que corresponde al controlador instanciado
     * @param PDO $pdo - Conexion a la base de datos
     * @param array $camposTabla - Se compone de los campos tal que:
     * ```php
     * ["campo" => [
     *     "comparador" => "=" | "like",
     *     "opcional" => true | false
     *   ],
     *   ...
     * ]
     * ```
     * Por defecto todos los campos son obligatorios y utilizan el comparador like, aunque es recomendable especificar
     * cada campo.
     * @param callable $validador - Es el validador de los campos, por norma general deberia contar con la posibilidad
     * de evaluarse campos individuales asi como todos los campos obligatorios.
     * ```php
     * $validador = function (array $campos,bool $comprobarTodos=false){
     *     // tu implementacion aqui
     *     return $miBool;
     * }
     * ```
     * Es opcional, si no se carga ninguno los datos no serán validados, en caso de ingresar una funcion que no sirva, lanza error.
     */
    function __construct(private string $tableName, private PDO $pdo, private array $camposTabla, private callable $validador = null)
    {
        if ($validador != null && (((new ReflectionMethod($validador))->getNumberOfParameters() != 2) || ((new ReflectionMethod($validador))->getNumberOfRequiredParameters() != 1))){
            throw new Exception("El validador pasado por parametro no es correcto.");
        }
    }

    /**
     * @param bool $like - Habilita la comparacion parcial cuando es true, si es false compara por coincidencia exacta
     * @param array $whereParams - Define los parametros que se usaran en el where, se compone de los campos tal que:
     * ```php
     * [
     *   "campo" => "valor para filtrar",
     *    ...
     * ]
     * ```
     * Si no se definen campos para buscar, la funcion devuelve true si existe aunque sea una unica fila en la tabla
     * @return bool
     */
    public function exists(array $whereParams, bool $like = false){
        $querySelect = $this->generarSelect($whereParams, null, $like);
        $opSql = $this->pdo->query($querySelect);
        $existe = false;
        if ($opSql->rowCount() > 0) {
            $existe = true;
        }
        return $existe;
    }

    /**
     * @param array $params - Array donde se buscaran los parametros para actualizar, el nombre de la columna debe ser <set>columna, ej:setnombre.
     * 
     * Se compone de los campos tal que:
     * ```php
     * [
     *   "setcampo" => "valor nuevo",
     *    ...
     * ]
     * ```
     * En caso de no haber valores que actualizar, retorna un arreglo vacio.
     * @return array
     */ 
    public function getSetParams(array $params){
        $arrReturn = [];
        foreach ($params as $key => $value) {
            if (str_starts_with($key, 'set') && array_key_exists(substr($key, 3), $this->camposTabla)) {
                $arrReturn[substr($key, 3)] = $value;
            }
        }
        return $arrReturn;
    }

    /**
     * @param array $params - Array donde se buscaran los parametros del where, el nombre del campo debe ser igual al respectivo de la tabla en la bd.
     * 
     * Se compone de los campos tal que:
     * ```php
     * [
     *   "campo" => "valor",
     *    ...
     * ]
     * ```
     * En caso de no haber valores que correspondan a los campos, retorna un arreglo vacio.
     * @return array
     */ 
    public function getWhereParams(array $params){
        $arrReturn = [];
        foreach ($params as $key => $value){
            if (array_key_exists($key,$this->camposTabla)){
                $arrReturn[$key] = $value;
            }
        }
        return $arrReturn;
    }

    /**
     * @param array $whereParams - Define los parametros que se usaran en el where, se compone de los campos tal que:
     * ```php
     * [
     *   "campo" => "valor para filtrar",
     *    ...
     * ]
     * ```
     * En caso de no haber valores para filtrar, no elimina nada por defecto.
     * @param bool $deleteSinWhere - Por defecto está en false, si se pone en true no es necesario enviar $whereParams ya que permite eliminar toda la tabla.
     * @return bool
     */ 
    public function delete(array $whereParams, bool $deleteSinWhere = false){
        $pudo = false;
        $where = $this->armarWhere($whereParams);

        if ($where != "" || $deleteSinWhere){
            $whereQuery = $this->generarDelete($whereParams);
            $pudo = $this->pdo->query($whereQuery)->execute();
        }

        return $pudo;
    }

    /**
     * @param array $queryParams - Array asociativo donde están los campos para filtar y los que se deben actualizar
     * 
     * Se compone de los campos tal que:
     * ```php
     * [
     *   "campo" => "valor para filtrar",
     *   "setcampo" => "valor nuevo",
     *    ...
     * ]
     * ```
     * En caso de no haber valores para filtrar, actualiza todas las filas de la tabla
     * @return bool
     */    
    public function update(array $queryParams){
        $validos = true;
        $pudo = false;
        if ($this->validador != null) $validos = ($this->validador)($this->getSetParams($queryParams));
        if ($validos){
            $queryUpdate = $this->generarUpdate($queryParams);
            $pudo=$this->pdo->query($queryUpdate)->execute();
        }
        return $pudo;
    }

    /**
     * @param array $datosIn - Array asociativo donde están los campos a cargar con sus valores
     * 
     * Se compone de los campos tal que:
     * ```php
     * [
     *   "campo" => "valor a insertar",
     *    ...
     * ]
     * ```
     * Es necesario pasar todos los campos obligatorios, caso contrario no será posible cargar los datos a la bd.
     * @return bool
     */   
    public function insert(array $datosIn){
        $validos = true;
        $pudo = false;

        if ($this->validador != null) $validos = ($this->validador)($datosIn,true);

        $contador = 0;
        foreach ($datosIn as $key => $value) {
            if (array_key_exists($key, $this->camposTabla)) {
                $contador++;
            }
        }
        
        

        //error_log("Campos necesarios:" . json_encode($contador >= $this->obligatorios));
        if ($contador >= $this->obligatorios){
            $queryInsert = $this->generarInsert($datosIn);
            $pudo = $this->pdo->prepare($queryInsert)->execute();
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
        $querySql = substr($querySql, 0, strlen($querySql) - 2); // quita ultimo espacio y coma
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