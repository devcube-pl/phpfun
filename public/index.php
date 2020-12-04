<?php

require '../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use Szkolenie\Controller\WebControllerInterface;
use Szkolenie\Controller\BooksAPIControllerInterface;

// congif .env
if (is_file('../.env')) {
    $dotenv = new Dotenv();
    $dotenv->load('../.env');
} else {
    die('Brak pliku .env! Skopiuj plik .env.dist do .env i ustaw w nim dane do bazy oraz API');
}

// router
$router = \Szkolenie\Core\Router\Router::create(include('../config/routes.php'));
$route = $router->getRoute();

$ctrl = $route->getController();
$action = $route->getAction();

// kontroler
$controller = new $ctrl();

if ($controller instanceof WebControllerInterface) {
    session_start();
    // twig
    $loader = new \Twig\Loader\FilesystemLoader('../templates');
    $twig = new \Twig\Environment($loader, [
        // cache dla twiga, upewnij się czy istnieje katalog
        // 'cache' => '../var/cache/twig',
    ]);

    $controller->setTemplateEngine($twig);

    // PDO
    $dbh = new PDO(
        'mysql:host='.$_ENV['MYSQL_HOST'] .
        ';dbname=' . $_ENV['MYSQL_DATABASE'],
        $_ENV['MYSQL_USER'],
        $_ENV['MYSQL_PASSWORD']
    );
    $controller->setDatabaseConnection($dbh);
}

if ($controller instanceof BooksAPIControllerInterface) {
    if ($_ENV['REST_API_CLIENT'] == 'guzzle') {
        $controller->setBooksRepository(new \Szkolenie\Repository\BooksAPIGuzzleRepository());
    } else {
        $controller->setBooksRepository(new \Szkolenie\Repository\BooksAPIRepository());
    }
}


// uruchom akcje kontrolera
echo call_user_func_array([$controller, $action], $route->getParams());
