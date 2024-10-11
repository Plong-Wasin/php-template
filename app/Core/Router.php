<?php

namespace App\Core;

class Router
{
    private $routes = [];
    private $groupMiddleware = [];
    private $params = [];
    private $isUrlRewrite = true;
    private $documentRoot = '';

    public function addRoute($method, $route, $handler, $middleware = [])
    {
        $this->routes[] = [
            'method' => array_map('strtoupper', is_array($method) ? $method : [$method]),
            'route' => $route,
            'handler' => $handler,
            'middleware' => array_merge($this->groupMiddleware, $middleware),
        ];
    }
    public function group($config, $callback)
    {
        $prevMiddleware = $this->groupMiddleware;

        // Set the new prefix and middleware for the group
        $this->groupMiddleware = array_merge($prevMiddleware, $config['middleware']);

        // Call the group callback to add routes inside the group
        call_user_func($callback);

        // Restore the previous prefix and middleware
        $this->groupMiddleware = $prevMiddleware;
    }
    public function dispatch()
    {
        foreach ($this->routes as $route) {
            $this->params = [];
            if ($this->match($route)) {
                foreach ($route['middleware'] as $middleware) {
                    if (is_string($middleware)) {
                        $middleware = new $middleware();
                        $middleware = $middleware->handle();
                    } elseif (is_array($middleware)) {
                        $middleware = new $middleware[0]();
                        $middleware = $middleware->handle($middleware[1]);
                    }
                }
                if (is_callable($route['handler'])) {
                    call_user_func_array($route['handler'], $this->params);
                } elseif (is_array($route['handler'])) {
                    $class = new $route['handler'][0];
                    $method = $route['handler'][1];
                    call_user_func_array([$class, $method], $this->params);
                }
                return true;
            }
        }
        return false;
    }
    public function setIsUrlRewrite($isUrlRewrite)
    {
        $this->isUrlRewrite = $isUrlRewrite;
        return $this;
    }
    public function setDocumentRoot($documentRoot)
    {
        $documentRoot = realpath($documentRoot);
        $this->documentRoot = $documentRoot;
        return $this;
    }
    private function match($route)
    {
        // Get the current request method and convert to uppercase
        $requestMethod = strtoupper($_SERVER['REQUEST_METHOD']);

        // Check if the request method is allowed
        if (!in_array($requestMethod, $route['method'])) {
            return false;
        }
        if (is_array($route['route'])) {
            // If no GET parameters are defined for the route, method match is sufficient
            if (empty($route['route']['get'])) {
                return true;
            }
            if (!$this->matchUri($route)) {
                return false;
            }

            // Check GET parameters
            foreach ($route['route']['get'] as $getKey => $getValue) {
                // If the required GET parameter is missing, return false
                if (!isset($_GET[$getKey])) {
                    return false;
                }

                $currentGetValue = $_GET[$getKey];

                if ($currentGetValue != $getValue) {
                    return false;
                }
            }

            // All checks passed
            return true;
        } else {
            if ($this->matchUri($route)) {
                return true;
            }
        }
        return false;
    }
    private function matchUri($route)
    {
        $routeUri = '/' . trim($route['route'], '/');
        $requestUri = $_SERVER['REQUEST_URI'];
        if (!$this->isUrlRewrite) {
            if ($this->documentRoot) {
                $dir = basename($this->documentRoot);
                $requestUri = preg_replace('/^.*?\/' . $dir . '\//', '/', $requestUri);
            }
            $requestUri = preg_replace('/^.*?\/index\.php\//', '/', $requestUri);
            $requestUri = rtrim($requestUri, '/index.php');
        }
        preg_match_all('/\{([^}]+)\}/', $routeUri, $paramNames);
        $paramNames = $paramNames[1];
        $routePattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routeUri);
        $routePattern = str_replace('/', '\/', $routePattern);
        $routePattern = '/^' . $routePattern . '\/?$/';
        if (preg_match($routePattern, $requestUri, $matches)) {
            array_shift($matches); // Remove the full match
            $params = [];
            foreach ($paramNames as $i => $paramName) {
                $params[$paramName] = $matches[$i];
            }
            $this->params = $params;
            return true;
        }

        return false;
    }
}
