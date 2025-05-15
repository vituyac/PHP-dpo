<?php

namespace App\core;

class Router {
    private array $routes = [
        'GET' => [],
        'POST' => []
    ];

    public function get(string $route, $callback): void {
        $this->routes['GET'][$route] = $callback;
    }

    public function post(string $route, $callback): void {
        $this->routes['POST'][$route] = $callback;
    }

    public function resolve(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes[$method] ?? [] as $route => $callback) {
            $pattern = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([0-9]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // удаляем полное совпадение
                if (is_array($callback) && count($callback) === 2) {
                    call_user_func_array($callback, $matches);
                    return;
                } elseif (is_callable($callback)) {
                    call_user_func_array($callback, $matches);
                    return;
                }
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}
