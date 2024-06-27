<?php
$camposIntercambio = [
    'id' => [
        "pk" => true,
        "tipo" => "int",
        "autoincrement" => true,
        "comparador" => "="
    ],
    'voluntario' => [
        "tipo" => "varchar(50)",
        "comparador" => "like",
        "fk" => [
            "tabla" => "usuarios",
            "campo" => "username"
        ]
    ],
    'publicacionOferta' => [
        "tipo" => "int",
        "comparador" => "=",
        "fk" => [
            "tabla" => "publicacion",
            "campo" => "id"
        ]
    ], // quien publicó
    'publicacionOfertada' => [
        "tipo" => "int",
        "comparador" => "=",
        "fk" => [
            "tabla" => "publicacion",
            "campo" => "id"
        ]
    ], // quien ofertó
    'ofertaAcepta' => [
        "tipo" => "boolean",
        "comparador" => "=",
        "opcional" => true
    ],
    'ofertadaAcepta' => [
        "tipo" => "boolean",
        "comparador" => "=",
        "default" => "true"
    ],
    'horario' => [
        "tipo" => "datetime",
        "comparador" => "="
    ],
    'estado' => [
        "tipo" => "ENUM('pendiente','cancelado','rechazado','aceptado','concretado')",
        "comparador" => "like"
    ],
    'descripcion' => [
        "tipo" => "text",
        "comparador" => "like",
        "opcional" => true
    ],
    'donacion' => [
        "tipo" => "boolean",
        "comparador" => "=",
        "opcional" => true,
    ],
    'centro' => [
        "tipo" => "int",
        "comparador" => "=",
        "fk" => [
            "tabla" => "centros",
            "campo" => "id"
        ]
    ],
    /* 'fecha_propuesta' => '?DATETIME', created_at
    'fecha_modificado' => '?DATETIME'   updated_at    */
];

$intercambioDB = new bdController('intercambio', $pdo, $camposIntercambio);