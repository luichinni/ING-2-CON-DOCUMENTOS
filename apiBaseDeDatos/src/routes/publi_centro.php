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
$campoPubliCentro = [
    "publicacion" => [
        "pk" => true,
        "tipo" => "int",
        "comparador" => "=",
        "opcional" => false,
        "fk" => [
            "tabla" => "publicacion",
            "campo" => "id"
        ]
    ],
    "centro" => [
        "pk" => true,
        "tipo" => "int",
        "comparador" => "like",
        "opcional" => false,
        "fk" => [
            "tabla" => "centros",
            "campo" => "id"
        ]
    ]
];

$publiCentroDB = new bdController('publi_centro',$pdo,$campoPubliCentro);


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

    return ($ret == false) ? json_decode('{}') : json_decode($ret);
}

function agregarPubliCentros(array $datosIn, PDO $pdo){
    global $publiCentroDB;
    $pudo = false;

    if ($publiCentroDB->exists($datosIn)) return $pudo;

    return $publiCentroDB->insert($datosIn);
}

?>