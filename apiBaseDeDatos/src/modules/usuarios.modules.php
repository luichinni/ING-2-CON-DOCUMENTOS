<?php

require_once __DIR__ . '/../utilities/bdController.php';

class UsuariosHandler extends BaseHandler{
    function __construct(bdController $db)
    {
        parent::__construct($db);
    }

    protected function restriccionBorrado(array $datos){// true -> restringido, false -> puede seguir
        // no tiene?
        return false;
    }
    protected function eliminarDependencias(array $datos){
        // publicaciones, intercambios, etc.. como no se borran no lo necesito por ahora
    }
    public function validarDatos(array $data, bool $todos){
        $campos = ['username', 'clave', 'nombre', 'apellido', 'dni', 'mail', 'telefono', 'rol'];
        $roles = ['admin', 'user', 'volunt'];

        $valido = false;

        if ($todos && !$this->db->comprobarObligatorios($data)) {
            $this->mensaje = 'Faltan campos por completar';
            return false;
        }

        match (true){
            // menos de 50 chars y que no esté utilizado
            (array_key_exists('username', $data)) && ((strlen($data['username']) > 50) || ($this->existe(array('username' => $data['username'])))) => $this->mensaje = "El nombre de usuario no es válido o ya está registrado",
            // menos de 50 char y que tenga más de 6
            (array_key_exists('clave', $data)) && ((strlen($data['clave']) > 50) || (strlen($data['clave']) < 6)) => $this->mensaje = "La clave no es válida",
            // mayor a 2 letras
            (array_key_exists('nombre', $data)) && (strlen($data['nombre']) < 2) => $this->mensaje = "El nombre no puede ser menor a 2 caracteres",
            // mayor a 2 letras
            (array_key_exists('apellido', $data)) && (strlen($data['apellido']) < 2) => $this->mensaje = "El apellido no puede ser menor a 2 caracteres",
            // que sea solo numerico
            (array_key_exists('dni', $data)) && (!ctype_digit($data['dni'])) => $this->mensaje = "El dni no es válido",
            // que tenga @ y que no sea utilizado por nadie
            (array_key_exists('mail', $data)) && ((strpos($data['mail'], '@') === false) || ($this->existe(array('mail' => $data['mail'])))) => $this->mensaje = "El mail no es válido o ya se encuentra registrado",
            // que sea solo numerico
            (array_key_exists('telefono', $data)) && ($data['telefono']!== '') && (!ctype_digit($data['telefono'])) => $this->mensaje = "El telefono no es válido, debe ser numerico",
            // rol existente
            (array_key_exists('rol', $data)) && (!in_array($data['rol'], $roles)) => $this->mensaje = "El rol que se intenta asignar no corresponde a un rol del sistema",
            default => $valido = true
        };
        
        return $valido;
    }

    public function notificacion(string $user){
        if ($user == "" || !$this->existe(['username'=>$user])) return false;

        $usuario = (array)$this->listar(['username'=>$user]);

        if (empty($usuario)) return false;

        $usuario = $usuario[0];
        return $usuario['notificacion'];
    }

    public function mail(string $user){
        if ($user == "" || !$this->existe(['username'=>$user])) return false;

        $usuario = (array)$this->listar(['username'=>$user]);

        if (empty($usuario)) return false;

        $usuario = $usuario[0];
        return $usuario['mail'];
    }

    public function rol(string $user){
        if ($user == "" || !$this->existe(['username'=>$user])) return false;

        $usuario = (array)$this->listar(['username'=>$user]);

        if (empty($usuario)) return false;

        $usuario = $usuario[0];
        return $usuario['rol'];
    }
}