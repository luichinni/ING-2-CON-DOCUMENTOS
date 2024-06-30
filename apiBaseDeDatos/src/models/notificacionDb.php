<?php
$camposNotificacion = [
    'id' => [
        "pk" => true,
        "tipo" => "int",
        "autoincrement" => true,
        "comparador" => "="
    ],
    'user' => [
        "tipo" => "varchar(50)",
        "comparador" => "like",
        "fk" => [
            "tabla" => "usuarios",
            "campo" => " username"
        ]
    ],
    'texto' => [
        "tipo" => "text",
        "comparador" => "like"
    ],
    /* 'fecha'=>'?datetime', created_at  */
    'visto' => [
        "tipo" => "boolean",
        "comparador" => "=",
        "default" => "FALSE",
        "opcional" => true
    ],
    'url' => [
        "tipo" => "text",
        "comparador" => "like"
    ]
];

$notificacionDB = new bdController('notificacion', $pdo, $camposNotificacion);