<?php
$dbConfig = [
    'host' => 'localhost',
    'dbname' => 'trueca_big_data',
    'user' => 'root',
    'password' => '',
];

$pdo = new PDO(
    'mysql:host=' . $dbConfig['host'] . ';dbname=' . $dbConfig['dbname'],
    $dbConfig['user'],
    $dbConfig['password']
);
