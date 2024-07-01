<?php

use Collections\CollectionsStream;

class Estadisticas{
    function __construct(protected IntercambiosHandler $intercambiosHandler){}

    function totalDe(string $motivo, array $datos, string $estado, string $desde, string $hasta){
        $listado = $this->intercambiosHandler->listar($datos);

        $stream = new CollectionsStream($listado);
        $retorno = $stream
            ->filter(function ($intercambio) use ($estado, $motivo) {
                return $intercambio['estado'] == $estado && ($motivo == '' || $intercambio['motivo'] == $motivo);
            })
            ->filter(function ($intercambio) use ($desde, $hasta, $estado, $motivo) { // true lo deja, false lo quita
                return $intercambio['horario'] >= $desde && $intercambio['horario'] <= $hasta;
            })
            ->distinct(fn ($i1, $i2) => $i1['id'] == $i2['id'])
            ->get();
        return count($retorno);
    }
}