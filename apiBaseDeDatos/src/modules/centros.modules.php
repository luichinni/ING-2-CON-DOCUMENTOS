<?php

require_once __DIR__ . '/../utilities/bdController.php';

class CentroHandler extends BaseHandler{
    // inyecto dependencias
    function __construct(bdController $db, protected publiHandler $publiHandler, protected intercambHandler $intercambHandler)
    {
        parent::__construct($db);
    }
    public function validarDatos(array $data, bool $todos){
        $campos = ['nombre', 'direccion', 'hora_abre', 'hora_cierra'];

        $valido = false;

        if ($todos){
            $valido = true;
            foreach($campos as $campo){
                $valido = $valido && in_array($campo,$data);
            }
            if (!$valido) return false;
            else $valido = false;
        }
        
        match(true){
            (array_key_exists('nombre', $data)) && ((!isset($data['nombre']) || empty($data['nombre'])) || (strlen($data['nombre']) > 255) || (strlen($data['nombre']) < 3)) => $this->mensaje = 'El campo nombre es inválido',
            (array_key_exists('direccion', $data)) && ((!isset($data['direccion']) || empty($data['direccion'])) || (strlen($data['direccion']) > 255) || (strlen($data['direccion']) < 3)) => $this->mensaje = 'El campo direccion es inválido',
            (array_key_exists('hora_abre', $data)) && ((!isset($data['hora_abre']) || empty($data['hora_abre'])) || (!preg_match('/^(?:2[0-3]|[0-1][0-9]):[0-5][0-9]$/', $data['hora_abre']))) => $this->mensaje = 'El horario de apertura es inválido',
            (array_key_exists('hora_cierra', $data)) && ((!isset($data['hora_cierra']) || empty($data['hora_cierra'])) || (!preg_match('/^(?:2[0-3]|[0-1][0-9]):[0-5][0-9]$/', $data['hora_cierra']))) => $this->mensaje = 'El horario de cierre es inválido',
            (array_key_exists('hora_abre', $data) && array_key_exists('hora_cierra', $data)) && ($data['hora_abre']>=$data['hora_cierra']) => $this->mensaje = 'El horario de apertura debe ser menor al horario de cierre',
            default => $valido = true
        };
        return $valido;
    }

    public function comprobarVoluntariosEn(int|string $id){
        return validarCentroVolun(['centro'=>$id]);
    }

    protected function restriccionBorrado(array $datos){ // true -> restringido, false -> puede seguir
        $restringido = false;
        if (!$this->comprobarVoluntariosEn($datos['id'])) {
            $this->mensaje = 'No se pudo eliminar, el centro tiene voluntarios asociados';
            $restringido = true;
        }
        return $restringido;
    }

    protected function eliminarDependencias(array $datos){
        $this->publiHandler->baja(['centro' => $datos['id']]);
        $this->intercambHandler->cancelar(['centro' => $datos['id']]);
    }

    public function listar(array $datos){
        $ret = [];
        try{
            $ret = $this->db->getAll($datos);
            $this->status = (empty($ret)) ? 404 : 200;
            $this->mensaje = (empty($ret)) ? 'No se encontraron centros' : 'Centros listados con éxito';
        }catch (Exception $e){
            $this->status = 500;
            $this->mensaje = 'Ocurrió un error al listar los centros';
        }
        return (array) $ret;
    }

    public function obtenerCentroDeVoluntario(string $voluntario){
        $this->status = 404;
        if (!validarCentroVolun(['voluntario'=>$voluntario])){
            $this->mensaje = 'El voluntario no corresponde a un centro';
            return false;
        }

        $centroVol = (array) ((array) obtenerCentroVolun(['voluntario'=>$voluntario]))[0];
        $centro = $this->listar(['id'=>$centroVol['centro']])[0];

        if ($this->status == 500){
            $this->mensaje = 'Ocurrió un error al obtener el centro de '. $voluntario;
            return false;
        }

        $this->status = 200;
        $this->mensaje = 'Centro obtenido con éxito';
        return (array) $centro;
    }
}

