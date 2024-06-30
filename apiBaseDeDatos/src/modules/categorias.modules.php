<?php

require_once __DIR__ . '/../utilities/bdController.php';

class CategoriaHandler extends BaseHandler{
    protected PublicacionesHandler $publiHandler;
    function __construct(bdController $db)
    {
        parent::__construct($db);
    }

    public function setPublicacionesHandler(PublicacionesHandler $publicacionesHandler){
        $this->publiHandler = $publicacionesHandler;
    }

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
        if (count($this->listar([]))==1){
            $this->mensaje = 'No se puede eliminar la última categoría del sistema';
            $restringido = true;
        }
        if (!$restringido && $this->publiHandler->existe(['categoria_id' => $datos['id']])){
            $this->mensaje = 'No se puede eliminar la categoría, tiene publicaciones asociadas';
            $restringido = true;
        }
        return $restringido;
    }

    protected function eliminarDependencias(array $datos){
        // categoria no elimina dependencias
    }
    
    public function nombre(int|string $id){
        if (!$this->existe(['id' => $id])) return false;

        $categoria = (array)$this->listar(['id' => $id]);

        $categoria = $categoria[0];
        return $categoria['nombre'];
    }

    public function idPorNombre(string $nombre){
        if (!$this->existe(['nombre' => $nombre])) return false;

        $categoria = (array)$this->listar(['nombre' => $nombre]);

        $categoria = $categoria[0];
        return $categoria['id'];
    }
}