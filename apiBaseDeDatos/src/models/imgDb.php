<?php
$camposImg = [
    "id" => [
        "pk" => true,
        "tipo" => "int",
        "autoincrement" => true,
        "comparador" => "="
    ],
    'archivo' => [
        "tipo" => "mediumtext",
        "comparador" => "like"
    ],
    'publicacion' => [
        "tipo" => "int",
        "comparador" => "like",
        "fk" => [
            "tabla" => "publicacion",
            "campo" => "id"
        ]
    ]
];

$imgDB = new bdController('imagen', $pdo, $camposImg);