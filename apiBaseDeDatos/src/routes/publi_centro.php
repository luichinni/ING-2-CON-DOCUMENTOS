<?PHP
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
CREATE TABLE publi_centro (
    publicacion int,
    centro int,
    PRIMARY KEY (publicacion, centro),
    FOREIGN KEY (publicacion) REFERENCES Publicacion (id),
    FOREIGN KEY (centro) REFERENCES Centros (id)
);
*/

require_once __DIR__ .'/../models/publiCentroDb.php';

function borrarPubliCentro(array $valuesWhere){
    global $publiCentroDB;
    $pudo = false;

    if (!$publiCentroDB->exists($valuesWhere)) return $pudo;

    $pudo = $publiCentroDB->delete($valuesWhere);

    return $pudo;
}

function validarPubliCentro(array $valuesWhere){
    global $publiCentroDB;
    return $publiCentroDB->exists($valuesWhere);
}

function listarPubliCentros(array $valuesWhere){
    return obtenerPubliCentros($valuesWhere, null);
}

function obtenerPubliCentros(array $valuesWhere, ?int $limit=1){
    global $publiCentroDB;
    $ret = '{}';
    if ($limit != null){
        $ret = $publiCentroDB->getFirst($valuesWhere, $limit);
    }else{
        $ret = $publiCentroDB->getAll($valuesWhere);
    }

    return ($ret == false) ? json_decode('{}') : $ret;
}

function agregarPubliCentros(array $datosIn, PDO $pdo){
    global $publiCentroDB;
    $pudo = false;

    if ($publiCentroDB->exists($datosIn)) return $pudo;

    return $publiCentroDB->insert($datosIn);
}

?>