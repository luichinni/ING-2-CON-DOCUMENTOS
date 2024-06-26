<?php
$camposCentro = [
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
    'direccion' => [
        "tipo" => "varchar (255)",
        "comparador" => "like"
    ],
    'hora_abre' => [
        "tipo" => "time",
        "comparador" => "=" // revisar
    ],
    'hora_cierra' => [
        "tipo" => "time",
        "comparador" => "="
    ]
];

$centroDB = new bdController('centros', $pdo, $camposCentro);