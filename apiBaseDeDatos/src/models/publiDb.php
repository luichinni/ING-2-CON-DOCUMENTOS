<?php
$camposPublicacion = [
    'id' => [
        "pk" => true,
        "tipo" => "int",
        "autoincrement" => true,
        "comparador" => "="
    ],
    'nombre' => [
        "tipo" => "varchar (255)",
        "comparador" => "like"
    ],
    'descripcion' => [
        "tipo" => "text",
        "comparador" => "like"
    ],
    'user' => [
        "tipo" => "varchar (50)",
        "comparador" => "like",
        "fk" => [
            "tabla" => "usuarios",
            "campo" => "username"
        ]
    ],
    'categoria_id' => [
        "tipo" => "int",
        "comparador" => "=",
        "fk" => [
            "tabla" => "categoria",
            "campo" => "id"
        ]
    ],
    'estado' => [
        "tipo" => "varchar (50)",
        "comparador" => "like"
    ],
    /* 'fecha_carga' => '?datetime'  created_at*/
];
$publiDB = new bdController('publicacion', $pdo, $camposPublicacion);