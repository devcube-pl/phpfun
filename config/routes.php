<?php
return [
    '/' => [
        'name' => 'homepage',
        'controller' => \Szkolenie\Controller\HomeController::class,
        'action' => 'index'
    ],
    'logowanie' => [
        'name' => 'login',
        'controller' => \Szkolenie\Controller\LoginController::class,
        'action' => 'index'
    ],
    'wyloguj' => [
        'name' => 'logout',
        'controller' => \Szkolenie\Controller\LoginController::class,
        'action' => 'logout'
    ],
    'rejestracja' => [
        'name' => 'signup',
        'controller' => \Szkolenie\Controller\SignUpController::class,
        'action' => 'index'
    ],
    'szukaj' => [
        'name' => 'search',
        'controller' => \Szkolenie\Controller\SearchController::class,
        'action' => 'index'
    ],
    'lista-api/:id' => [
        'name' => 'itemapi',
        'controller' => \Szkolenie\Controller\ListApiController::class,
        'action' => 'item',
        'params' => [
            'id' => '\d+'
        ]
    ],
    'lista-api/:id/edycja' => [
        'name' => 'itemeditapi',
        'controller' => \Szkolenie\Controller\ListApiController::class,
        'action' => 'edit',
        'params' => [
            'id' => '\d+'
        ]
    ],
    'lista-api/:id/usun' => [
        'name' => 'itemremoveapi',
        'controller' => \Szkolenie\Controller\ListApiController::class,
        'action' => 'remove',
        'params' => [
            'id' => '\d+'
        ]
    ],
    'lista-api/dodaj' => [
        'name' => 'listapiadd',
        'controller' => \Szkolenie\Controller\ListApiController::class,
        'action' => 'add'
    ],
    'lista-api' => [
        'name' => 'listapi',
        'controller' => \Szkolenie\Controller\ListApiController::class,
        'action' => 'index'
    ],
    'lista-allegro' => [
        'name' => 'listallegro',
        'controller' => \Szkolenie\Controller\ListAllegroController::class,
        'action' => 'index'
    ],
    'lista-allegro-pkce' => [
        'name' => 'listallegropkce',
        'controller' => \Szkolenie\Controller\ListAllegroController::class,
        'action' => 'pkce'
    ],
];
