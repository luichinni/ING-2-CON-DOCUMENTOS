<?PHP
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../utilities/generadorQuerys.php';
// //obtener, 
// //validar, 
// //borrar, 
// editar, ESTE NO IRIA, SOLO HAY CENTRO Y VOLUNT
// //agregar, 
// listar SOLUCIONADO EN EL OBTENER CON LIMIT
// CentroVolun = ((centro(FK),voluntario(FK))(PK))
$campoPubliCentro = [
    "Publicacion" => "varchar",
    "centro" => "int"
];


function borrarPubliCentro(array $valuesWhere, PDO $pdo){
    global $camposPubliCentro;
    $querySql = generarDelete('publi_centro',$camposPubliCentro,$valuesWhere);
    $where = armarWhere($valuesWhere,$camposPubliCentro);
    $pudo = false;
    if($where != ""){
        try{
            $pdo->query($querySql)->execute();
            $pudo = true;
        }catch (Exception $e){
            $pudo = false;
        }
    }
    return $pudo;
}

function validarPubliCentro(array $valuesWhere, PDO $pdo){
    global $camposPubliCentro;
    $tieneCampos = array_key_exists('publicacion', $valuesWhere) && array_key_exists('centro', $valuesWhere);
    $valido = false;
    if($tieneCampos){
        $querySql = generarSelect('centro_volun', $camposPubliCentro, $valuesWhere);
        $valido = $pdo->query($querySql)->rowCount() > 0;
    }
    return $valido;
}

function listarPubliCentros(array $valuesWhere,PDO $pdo){
    return obtenerPubliCentros($valuesWhere,$pdo,null);
}

function obtenerPubliCentros(array $valuesWhere, PDO $pdo, ?int $limit = 1){
    global $camposPubliCentro;
    $querySql = generarSelect('publi_centro',$camposPubliCentro,$valuesWhere);
    if ($limit != null){
        $querySql .= "LIMIT $limit";
    }
    $return = $pdo->query($querySql)->fetchAll();
    return $return;
}

function agregarPubliCentros(array $datosIn, PDO $pdo){
    global $camposPubliCentro;
    $msgResponse = ['Exito' => 'Centro agregado con exito a la publicacion'];
    // INSERT INTO `publi_centro` (`publicacion`, `centro`) VALUES ('', '')
    $querySql = generarInsert('publi_centro',$camposPubliCentro,$datosIn);
    // return response
    try {
        $pdo->prepare($querySql)->execute();
    } catch (Exception $e) {
        $msgResponse = [
            'error' => 'Ocurrio un error inesperado'
        ];
    }

    return $msgResponse;
}

?>