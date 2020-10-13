<?php

namespace App\Josel\Core;

use Throwable;
use ReflectionClass;
use App\Josel\Core\Resolver;

/**
 * This class describes a dispatcher.
 */
class Dispatcher
{
    /**
     * Request object
     */
    private $request;

    /**
     * Dispatched resutls
     */
    private $result = null;

    /**
     * Dispatcher the route
     */
    public function dispatch()
    {
        $this->request = new Request();
        Router::parse($this->request, $this->request->url);
        $controller = $this->loadController();
        if (!$controller) {
            http_response_code(404);
            $this->result = array(
                'success' => false,
                'message' => 'Page not found.',
            );
            return $this;
        }

        if (!$controller->hasMethod($this->request->action)) {
            http_response_code(404);
            $this->result = array(
                'success' => false,
                'message' => 'Page not found.',
            );
            return $this;
        }

        try {
            $reflection_method = new \ReflectionMethod(
                $controller->name,
                $this->request->action
            );
            $resolver = new Resolver;
            $controller_instance = $resolver->resolve($controller->name);
            $method_dependencies = $resolver->getDependencies(
                $reflection_method->getParameters()
            );

            $this->result = $reflection_method->invokeArgs(
                $controller_instance,
                $method_dependencies
            );
        } catch (Throwable $exception) {
            $this->result = array(
                'success' => false,
                'message' => $exception->getMessage(),
            );
        }

        return $this;
    }

    /**
     * Loads a controller.
     *
     * @return     Controller instance
     */
    public function loadController()
    {
        $name       = ucfirst($this->request->controller) . "Controller";
        $controller = null;
        try {
            $controller = new \ReflectionClass(
                "\App\Josel\Controllers\\" . $name
            );
        } catch (Throwable $exception) {
            if (!strpos($exception->getMessage(), "does not exist") !== false) {
                throw $exception;
            }
        }
        return $controller;
    }

    /**
     * Renders the response
     */
    public function renderReponse()
    {
        header('Content-Type: application/json');
        if (!http_response_code()) {
            http_response_code(200);
        }
        echo json_encode($this->result);
    }
}
