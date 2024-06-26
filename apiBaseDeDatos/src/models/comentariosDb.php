<?php
$camposComentarios = [
    'id' => [
        "pk" => true,
        "tipo" => "int",
        "autoincrement" => true,
        "comparador" => "="
    ],
    'publicacion' => [
        "tipo" => "int",
        "comparador" => "=",
        "fk" => [
            "tabla" => "publicacion",
            "campo" => "id"
        ]
    ],
    'user' => [
        "tipo" => "varchar (50)",
        "comparador" => "like",
        "fk" => [
            "tabla" => "usuarios",
            "campo" => "username"
        ]
    ],
    'texto' => [
        "tipo" => "text",
        "comparador" => "like"
    ],
    'respuesta' => [
        "tipo" => "text",
        "comparador" => "like",
        "opcional" => true
    ],
    /*     'fecha_publicacion'=>'?datetime',  created_at
    'fecha_modificado'=>'?datetime'  updated_at */
];

$comentariosDB = new bdController('comentario', $pdo, $camposComentarios);