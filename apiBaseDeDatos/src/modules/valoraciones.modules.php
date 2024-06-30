<?php

require_once __DIR__ . '/../utilities/bdController.php';

class ValoracionesHandler extends BaseHandler{
    function __construct(bdController $db, protected UsuariosHandler $usuariosHandler)
    {
        parent::__construct($db);
    }

    protected function restriccionBorrado(array $datos){// true -> restringido, false -> puede seguir
        // no se borra
        return false;
    }
    protected function eliminarDependencias(array $datos){
        // no tiene
    }
    public function validarDatos(array $data, bool $todos)
    {
        $campos = ['userValorado', 'userValorador', 'puntos'];

        $valido = false;

        if ($todos && !$this->db->comprobarObligatorios($data)) {
            $this->mensaje = 'Faltan campos por completar';
            return false;
        }

        match (true) {
            (array_key_exists('userValorado', $data)) && (!$this->usuariosHandler->existe(['username' => $data['userValorado']])) => $this->mensaje = 'No se pudo encontrar el usuario a valorar',
            (array_key_exists('userValorador', $data)) && (!$this->usuariosHandler->existe(['username' => $data['userValorador']])) => $this->mensaje = 'Ocurrió un error al valorar',
            (array_key_exists('puntos', $data)) && ($data['puntos']>5 || $data['puntos']<0) => $this->mensaje = 'Ocurrió un error al procesar la valoración',
            default => $valido = true
        };
        return $valido;
    }
    public function valoracion(string $user){
        $puntajes = (array)$this->listar(['userValorado'=>$user]);

        if (empty($puntajes)){
            $this->mensaje = 'El usuario no ha sido valorado';
            $this->status = 404;
            return 0;
        }

        $total = 0;
        foreach($puntajes as $pos => $datos){
            $total += $datos['puntos'];
        }

        $this->mensaje = 'Valoraciones contadas con éxito';
        $this->status = 200;
        return ($total/count($puntajes));
    }
}