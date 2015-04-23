<?php
use Cake\Routing\Router;

Router::plugin('PhotoCrop', function ($routes) {
    $routes->fallbacks('InflectedRoute');
});
