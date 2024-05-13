<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->group('/public', function (RouteCollectorProxy $group) use ($pdo) {
    $group->post('/centros', function ($request, $response, $args) use ($pdo){
    
    })


});
?>