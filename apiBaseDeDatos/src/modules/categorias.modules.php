<?php

require_once __DIR__ . '/../utilities/bdController.php';

class CategoriaHandler extends BaseHandler{

    public function validarDatos(array $data, bool $todos){
        $valid = false;
        if (!array_key_exists('nombre',$data)){
            $this->mensaje = 'El campo nombre es necesario';
        }else{
            $valid = true;
        }
        return $valid;
    }

    protected function restriccionBorrado(array $datos){// true -> restringido, false -> puede seguir
        $restringido = false;
        if (count($this->listar($datos))==1){
            $this->mensaje = 'No se puede eliminar la última categoría del sistema';
            $restringido = true;
        }
        return $restringido;
    }

    protected function eliminarDependencias(array $datos){
        // categoria no elimina dependencias
    }
    
}