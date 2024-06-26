<?php
$campoPubliCentro = [
    "publicacion" => [
        "pk" => true,
        "tipo" => "int",
        "comparador" => "=",
        "fk" => [
            "tabla" => "publicacion",
            "campo" => "id"
        ]
    ],
    "centro" => [
        "pk" => true,
        "tipo" => "int",
        "comparador" => "=",
        "fk" => [
            "tabla" => "centros",
            "campo" => "id"
        ]
    ]
];

$publiCentroDB = new bdController('publi_centro', $pdo, $campoPubliCentro);