<?php

namespace Szkolenie\Core\Router;

use Szkolenie\Core\Controller\ErrorController;

class Router
{
    /**
     * @var array
     */
    private $routes;

    /**
     * @var Route
     */
    private $currentRoute;

    /**
     * @var Router
     */
    private static $instance;

    /**
     * @param array $routes
     * @return self
     */
    public static function create(array $routes)
    {
        self::$instance ??= new self($routes);
        return self::$instance;
    }

    /**
     * @return self
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * @return string
     */
    public function getCurrentRouteName()
    {
        return $this->currentRoute->getName();
    }

    /**
     * @param string $url
     */
    public function redirect(string $url)
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * @return Route
     */
    public function getRoute()
    {
        $matches = [];
        $requestUri = $this->getRequestUri();

        if (array_key_exists($requestUri, $this->routes)) {
            $route = new Route($this->routes[$requestUri]);
            $this->currentRoute = $requestUri;
        } elseif (($routeData = $this->matchRoute($requestUri, $matches)) !== false) {
            preg_match_all('/:(\w+)/', $routeData['_route'], $params);

            foreach ($params[1] as $index => $param) {
                $routeData['params'][$param] = $matches[$index];
            }

            $route = new Route($routeData);
        } else {
            $route = new Route(
                [
                    'controller' => ErrorController::class,
                    'action' => 'error404'
                ]
            );
        }

        $this->currentRoute = $route;

        return $route;
    }

    private function __construct(array $routes)
    {
        $this->compile($routes);
    }

    /**
     * @param array $routes
     */
    private function compile(array $routes)
    {
        $r = [];

        foreach ($routes as $key => $route) {
            $route['_route'] = $key;
            $route['_method'] = $_SERVER['REQUEST_METHOD'];

            if (substr($key, -1) != '/') {
                $key = '/'.$key.'/';
            }

            if (isset($route['params'])) {
                foreach ($route['params'] as $param => $paramValue) {
                    $key = str_replace(':'.$param, '('.$paramValue.')', $key);
                }
            }

            $r[$key] = $route;
        }

        $this->routes = $r;
    }

    private function matchRoute($requestUri, &$matches)
    {
        $found = false;

        foreach ($this->routes as $key => $route) {
            $key = str_replace( '/', '\/', $key);

            if (preg_match('/^'.$key.'$/i', $requestUri, $matches)) {
                array_shift($matches);
                $found = $route;
                break;
            }
        }
        return $found;
    }

    /**
     * @return string
     */
    private function getRequestUri()
    {
        $requestUri = strtok($_SERVER['REQUEST_URI'], '?');

        if (substr($requestUri, -1) != '/') {
            $requestUri .= '/';
        }

        return $requestUri;
    }
}
