<?php
$camposCategorias = [
    'id' => [
        "pk" => true,
        "tipo" => "int",
        "autoincrement" => true,
        "comparador" => "="
    ],
    'nombre' => [
        "tipo" => "varchar (255)",
        "comparador" => "like"
    ]
];

$categoriaDB = new bdController('categoria', $pdo, $camposCategorias);