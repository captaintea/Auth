<?php

return [
    'getIndex' => [
        'controller' => 'App\Controllers\UserController',
        'action' => 'getIndexAction',
        'method' => 'GET',
        'url' => '/'
    ],
    'getLogin' => [
        'controller' => 'App\Controllers\UserController',
        'action' => 'getLoginAction',
        'method' => 'GET',
        'url' => '/login'
    ],
    'postLogin' => [
        'controller' => 'App\Controllers\UserController',
        'action' => 'postLoginAction',
        'method' => 'POST',
        'url' => '/login'
    ],
    'postLogout' => [
        'controller' => 'App\Controllers\UserController',
        'action' => 'postLogoutAction',
        'method' => 'POST',
        'url' => '/logout'
    ],
    'getRegister' => [
        'controller' => 'App\Controllers\UserController',
        'action' => 'getRegisterAction',
        'method' => 'GET',
        'url' => '/register'
    ],
    'postRegister' => [
        'controller' => 'App\Controllers\UserController',
        'action' => 'postRegisterAction',
        'method' => 'POST',
        'url' => '/register'
    ],
];
