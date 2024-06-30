<?php

require_once __DIR__ . '/../utilities/bdController.php';

class ComentariosHandler extends BaseHandler{

    function __construct(bdController $db, protected PublicacionesHandler $publiHandler, protected UsuariosHandler $userHandler)
    {
        parent::__construct($db);
    }

    protected function restriccionBorrado(array $datos){ // true -> restringido, false -> puede seguir
        $restringido = false;
        if (!array_key_exists('userMod',$datos) || (array_key_exists('userMod', $datos) && !$this->userHandler->existe(['username' => $datos['userMod']]))){
            $this->mensaje = 'El usuario no es válido';
            $restringido = true;
        }
        if (!$restringido){
            $comment = (array)$this->db->getFirst(['id'=>$datos['id']])[0];
            if ($comment['user']!=$datos['userMod'] && ($this->userHandler->rol($datos['userMod'])!='admin' || $this->publiHandler->getDueño($datos['publicacion'])!=$datos['userMod'])){
                $this->mensaje = 'No tienes permisos para eliminar el comentario';
                $restringido = true;
            }
        }
        return $restringido;
    }
    protected function eliminarDependencias(array $datos){
        // comentario no tiene dependencias
    }
    public function validarDatos(array $data, bool $todos){
        $campos = ['publicacion', 'user', 'texto', 'respuesta'];

        $valido = false;

        if ($todos && !$this->db->comprobarObligatorios($data)) {
            $this->mensaje = 'Faltan campos por completar';
            return false;
        }

        match (true) {
            (array_key_exists('publicacion', $data)) && ((!isset($data['publicacion']) || empty($data['publicacion'])) || (!$this->publiHandler->existe(['id'=>$data['publicacion']]))) => $this->mensaje = 'La publicacion no es válida',
            (array_key_exists('user', $data)) && ((!isset($data['user']) || empty($data['user'])) || (strlen($data['user']) > 50) || (strlen($data['user']) < 3) || (!$this->userHandler->existe(['username'=>$data['user']]))) => $this->mensaje = 'El usuario no es válido',
            (array_key_exists('texto', $data)) && ((!isset($data['texto']) || empty($data['texto']))) => $this->mensaje = 'El texto no es válido',
            (array_key_exists('respuesta', $data)) && ((!isset($data['respuesta']) || empty($data['respuesta']))) => $this->mensaje = 'La respuesta no es válida',
            default => $valido = true
        };
        return $valido;
    }
}