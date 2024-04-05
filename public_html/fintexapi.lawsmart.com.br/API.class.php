<?php

class API {
    private array $routes = [];

    public function addRoute(string $method, string $path, string $controllerMethod) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controllerMethod' => $controllerMethod
        ];
    }

    public function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $_SERVER['REQUEST_URI'];

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchRoute($route['path'], $path)) {
                $parts = explode('@', $route['controllerMethod']);
                $controllerName = $parts[0];
                $methodName = $parts[1];

                $controller = new $controllerName();
                $controller->$methodName();
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
    }

    private function matchRoute(string $pattern, string $path): bool {
        $pattern = preg_replace('/\{[^\/]+\}/', '[^/]+', $pattern);
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = '/^' . $pattern . '$/';

        return preg_match($pattern, $path);
    }
}
