<?php
$camposCentroVolun = [
    "voluntario" => [
        "pk" => true,
        "tipo" => "varchar (50)",
        "comparador" => "like",
        "fk" => [
            "tabla" => "usuarios",
            "campo" => "username"
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

$centroVolunDB = new bdController('centro_volun', $pdo, $camposCentroVolun);