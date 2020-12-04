<?php

namespace Szkolenie\Core\Router;

class Route
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $controller;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $action;

    /**
     * @var array
     */
    private $params;

    /**
     * @param array $route
     */
    public function __construct(array $route)
    {
        if (!isset($route['controller'])) {
            throw new \RuntimeException('Route does not contain "controller" value');
        }

        $this->controller = $route['controller'];

        if (!isset($route['action'])) {
            throw new \RuntimeException('Route does not contain "action" value');
        }

        $this->action = $route['action'];

        if (isset($route['name'])) {
            $this->name = $route['name'];
        }

        if (isset($route['params'])) {
            $this->params = $route['params'];
        } else {
            $this->params = [];
        }

        if (isset($route['method'])) {
            $this->method = $route['method'];
        } else {
            $this->method = 'GET';
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}
