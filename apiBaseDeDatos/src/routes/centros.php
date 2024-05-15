<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

function validaDatos($data, $response) {
    $columna = ['nombre', 'direccion', 'hora_abre', 'hora_cierra'];

    foreach ($columna as $colum) {
        if (!isset($data[$colum]) || empty($data[$colum])) {
            $errorResponse = ['error' => 'El campo: ' . $colum . ' es requerido'];
            $response->getBody()->write(json_encode($errorResponse));
            return false;
        }
    }

    $revisarCar = ['nombre', 'direccion'];
    foreach ($revisarCar as $revi) {
        if (isset($data[$revi]) && strlen($data[$revi]) > 255) {
            $errorResponse = ['error' => 'El campo ' . $revi . ' excede los 255 caracteres permitidos'];
            $response->getBody()->write(json_encode($errorResponse));
            return false;
        }
        if (isset($data[$revi]) && strlen($data[$revi]) < 3) {
            $errorResponse = ['error' => 'El campo ' . $revi . ' debe tener al menos 3 caracteres'];
            $response->getBody()->write(json_encode($errorResponse));
            return false;
        }
    }

    $hora_abre = $data['hora_abre'];
    $hora_cierra = $data['hora_cierra'];

    if (!preg_match('/^(?:2[0-3]|[0-1][0-9]):[0-5][0-9]$/', $hora_abre) || !preg_match('/^(?:2[0-3]|[0-1][0-9]):[0-5][0-9]$/', $hora_cierra)) {
        $errorResponse = ['error' => 'Los campos de apertura y cierre deben estar en formato HH:MM, y estar entre los valores 00:00 y 23:59'];
        $response->getBody()->write(json_encode($errorResponse));
        return false;
    }

    if ($hora_abre >= $hora_cierra) {
        $errorResponse = ['error' => 'El horario de apertura debe ser menor al horario de cierre'];
        $response->getBody()->write(json_encode($errorResponse));
        return false;
    }

    return true;
}

function validarExiste($id, $pdo){
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM centros WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $existeCentro = $stmt->fetchColumn();

    return $existeCentro > 0;
}


$app->group('/public', function (RouteCollectorProxy $group) use ($pdo) {
    $group->POST('/newCentros', function ($request, $response, $args) use ($pdo) {
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $errorResponse = ['error' => 'JSON no válido'];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        if (!validaDatos($data, $response)) {
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $stmt = $pdo->prepare('INSERT INTO centros (nombre, direccion, hora_abre, hora_cierra) VALUES (:nombre, :direccion, :hora_abre, :hora_cierra)');
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':direccion', $data['direccion']);
        $stmt->bindParam(':hora_abre', $data['hora_abre']);
        $stmt->bindParam(':hora_cierra', $data['hora_cierra']);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            $errorResponse = ['error' => 'Error al ejecutar la consulta: ' . $e->getMessage()];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }

        $centro = [
            'id' => $pdo->lastInsertId(),
            'nombre' => $data['nombre'],
            'direccion' => $data['direccion'],
            'hora_abre' => $data['hora_abre'],
            'hora_cierra' => $data['hora_cierra']
        ];

        $response->getBody()->write(json_encode($centro));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->PUT('/updateCentros/{id}', function ($request, Response $response, $args) use ($pdo) {
        $id = $args['id'];

        if (!validarExiste($id, $pdo)) {
            $errorResponse = ['error' => 'No existe un centro con ese id'];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $errorResponse = ['error' => 'JSON no válido'];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        if (!validaDatos($data, $response)) {
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $stmt = $pdo->prepare('UPDATE centros SET nombre = :nombre, direccion = :direccion, hora_abre = :hora_abre, hora_cierra = :hora_cierra WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':direccion', $data['direccion']);
        $stmt->bindParam(':hora_abre', $data['hora_abre']);
        $stmt->bindParam(':hora_cierra', $data['hora_cierra']);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            $errorResponse = ['error' => 'Error al ejecutar la consulta: ' . $e->getMessage()];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }

        $centro = [
            'id' => $id,
            'nombre' => $data['nombre'],
            'direccion' => $data['direccion'],
            'hora_abre' => $data['hora_abre'],
            'hora_cierra' => $data['hora_cierra']
        ];

        $response->getBody()->write(json_encode($centro));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->DELETE('/deleteCentro/{id}', function (Request $request, Response $response, $args) use ($pdo) {
        $id = $args['id'];

        if (!validarExiste($id, $pdo)) {    
            $errorResponse = ['error' => 'No existe un centro con ese id'];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $stmt = $pdo->prepare('DELETE FROM centros WHERE id = :id');

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            $errorResponse = ['error' => 'Error al ejecutar la consulta: ' . $e->getMessage()];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['mensaje' => 'Centro eliminado con éxito']));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->GET('/listarCentros', function ($request, Response $response, $args) use ($pdo) {
        $nombre = $request->getQueryParams()['nombre'] ?? null;
        $direccion = $request->getQueryParams()['direccion'] ?? null;

        $consulta = 'SELECT * FROM centros';
        $condiciones = [];
        $parametros = [];

        if ($nombre != null) {
            $condiciones[] = 'nombre LIKE :nombre';
            $parametros['nombre'] = "%{$nombre}%";
        }
        if ($direccion != null) {
            $condiciones[] = 'direccion LIKE :direccion';
            $parametros['direccion'] = "%{$direccion}%";
        }
        if (!empty($condiciones)) {
            $consulta .= ' WHERE ' . implode(' AND ', $condiciones);
        }

        $stmt = $pdo->prepare($consulta);

        try {
            $stmt->execute($parametros);
        } catch (PDOException $e) {
            $errorResponse = ['error' => 'Error al ejecutar la consulta: ' . $e->getMessage()];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }

        $centros = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($centros));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });
});
?>
