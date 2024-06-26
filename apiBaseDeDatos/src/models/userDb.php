<?php
$camposUser = [
    "username" => [
        "pk" => true,
        "tipo" => "varchar (50)",
        "comparador" => "like",
    ],
    "clave" => [
        "tipo" => "varchar (50)",
        "comparador" => "like",
    ],
    "nombre" => [
        "tipo" => "varchar (255)",
        "comparador" => "like",
    ],
    "apellido" => [
        "tipo" => "varchar (255)",
        "comparador" => "like",
    ],
    "dni" => [
        "tipo" => "int(8)",
        "comparador" => "=",
    ],
    "mail" => [
        "tipo" => "varchar (255)",
        "comparador" => "like"
    ],
    "telefono" => [
        "tipo" => "int",
        "comparador" => "=",
        "opcional" => true
    ],
    /* "fecha_registro"=>'?datetime',  aparece como created_at*/
    "rol" => [
        "tipo" => "ENUM('user', 'volunt', 'admin')",
        "comparador" => "like"
    ],
    "notificacion" => [
        "tipo" => "BOOLEAN",
        "comparador" => "=",
        "opcional" => true,
        "default" => "TRUE"
    ]
];

$userDB = new bdController('usuarios', $pdo, $camposUser);