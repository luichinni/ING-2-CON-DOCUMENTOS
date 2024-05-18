<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

function validaDatos($data, $response) {
    $columna = ['nombre', 'descripcion', 'user', 'categoria', 'estado'];

    foreach ($columna as $colum) {
        if (!isset($data[$colum]) || empty($data[$colum])) {
            $errorResponse = ['error' => 'El campo: ' . $colum . ' es requerido'];
            $response->getBody()->write(json_encode($errorResponse));
            return false;
        }
    }

    //falta las validaciones
    return true;
}

function ExistePublicacion($id, $pdo) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM publicacion WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $existePublicacion = $stmt->fetchColumn();

    return $existePublicacion > 0;
}

$app->group('/public', function (RouteCollectorProxy $group) use ($pdo) {
    $group->POST('/newPublicacion', function ($request, $response, $args) use ($pdo) {
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);
        // vemos si es un json valido
        if (json_last_error() !== JSON_ERROR_NONE) {
            $errorResponse = ['error' => 'JSON no válido'];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        //vemos las validaciones
        if (!validaDatos($data, $response)) {
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        //preparamos la ejecucion
        $stmt = $pdo->prepare('INSERT INTO publicacion (nombre, descripcion, user, categoria, estado) VALUES (:nombre, :descripcion, :user, :categoria, :estado)');
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        $stmt->bindParam(':user', $data['user']);
        $stmt->bindParam(':categoria', $data['categoria']);
        $stmt->bindParam(':estado', $data['estado']);
        //ejecutamos o mandamos mensaje de error
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            $errorResponse = ['error' => 'Error al ejecutar la consulta: ' . $e->getMessage()];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
        //preparamos mensaje de respuesta
        $publicacion = [
            'id' => $pdo->lastInsertId(),
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'user' => $data['user'],
            'categoria' => $data['categoria'],
            'estado' => $data['estado']
        ];
        // enviamos mensaje
        $response->getBody()->write(json_encode($publicacion));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->PUT('/updatePublicacion/{id}', function ($request, Response $response, $args) use ($pdo) {
        $id = $args['id'];
        //nos fijamos si existe la publicacion
        if (!ExistePublicacion($id, $pdo)) {
            $errorResponse = ['error' => 'No existe una publicación con ese id'];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        // obtenemos los datos
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);
        //vemos si es un json valido
        if (json_last_error() !== JSON_ERROR_NONE) {
            $errorResponse = ['error' => 'JSON no válido'];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        // validamos datos
        if (!validaDatos($data, $response)) {
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        // preparamos la consulta
        $stmt = $pdo->prepare('UPDATE publicacion SET nombre = :nombre, descripcion = :descripcion, user = :user, categoria = :categoria, estado = :estado WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        $stmt->bindParam(':user', $data['user']);
        $stmt->bindParam(':categoria', $data['categoria']);
        $stmt->bindParam(':estado', $data['estado']);
        // la ejecutamos
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            $errorResponse = ['error' => 'Error al ejecutar la consulta: ' . $e->getMessage()];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
        //preparamos mensaje de vuetla
        $publicacion = [
            'id' => $id,
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'user' => $data['user'],
            'categoria' => $data['categoria'],
            'estado' => $data['estado']
        ];
        // envia mensaje
        $response->getBody()->write(json_encode($publicacion));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->DELETE('/deletePublicacion/{id}', function (Request $request, Response $response, $args) use ($pdo) {
        $id = $args['id'];
        // nos fijamos si existe la publicacion
        if (!ExistePublicacion($id, $pdo)) {    
            $errorResponse = ['error' => 'No existe una publicación con ese id'];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        //preparamos la consulta
        $stmt = $pdo->prepare('DELETE FROM publicacion WHERE id = :id');
        $stmt->bindParam(':id', $id);
        //intentamos ejecutar o mandamos el error
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            $errorResponse = ['error' => 'Error al ejecutar la consulta: ' . $e->getMessage()];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
        //enviamos mensaje de exito
        $response->getBody()->write(json_encode(['mensaje' => 'Publicación eliminada con éxito']));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->GET('/listarPublicaciones', function ($request, Response $response, $args) use ($pdo) {
        //preparamos los parametros
        $nombre = $request->getQueryParams()['nombre'] ?? null;
        $user = $request->getQueryParams()['user'] ?? null;
        $categoria = $request->getQueryParams()['categoria'] ?? null;
        $estado = $request->getQueryParams()['estado'] ?? null;
        $id = $request->getQueryParams()['id'] ?? null;
        //preparamos la consulta y para agregarle parametros
        $consulta = 'SELECT * FROM publicacion';
        $condiciones = [];
        $parametros = [];
        // verificamos si tiene parametrso, si los tiene los agrega
        if ($nombre != null) {
            $condiciones[] = 'nombre LIKE :nombre';
            $parametros['nombre'] = "%{$nombre}%";
        }
        if ($user != null) {
            $condiciones[] = 'user = :user';
            $parametros['user'] = $user;
        }
        if ($categoria != null) {
            $condiciones[] = 'categoria = :categoria';
            $parametros['categoria'] = $categoria;
        }
        if ($estado != null) {
            $condiciones[] = 'estado = :estado';
            $parametros['estado'] = $estado;
        }
        if ($id != null) {
            $condiciones[] = 'id = :id';
            $parametros['id'] = $id;
        }

        if (!empty($condiciones)) {
            $consulta .= ' WHERE ' . implode(' AND ', $condiciones);
        }
        //prepara la consulta
        $stmt = $pdo->prepare($consulta);
        //ejecuta o envia el error
        try {
            $stmt->execute($parametros);
        } catch (PDOException $e) {
            $errorResponse = ['error' => 'Error al ejecutar la consulta: ' . $e->getMessage()];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
        // envia mensaje de vuelta
        $publicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($publicaciones));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });
});
?>