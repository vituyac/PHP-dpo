<?php

namespace App\core;

class Router {
    private array $routes = [
        'GET' => [],    // маршруты для GET-запросов
        'POST' => []    // маршруты для POST-запросов
    ];

    // Регистрация GET маршрута с его обработчиком
    public function get(string $route, $callback): void {
        $this->routes['GET'][$route] = $callback;
    }

    // Регистрация POST маршрута с его обработчиком
    public function post(string $route, $callback): void {
        $this->routes['POST'][$route] = $callback;
    }

    // Обработка текущего запроса и вызов соответствующего обработчика
    public function resolve(): void {
        $method = $_SERVER['REQUEST_METHOD'];                  // получаем метод запроса (GET или POST)
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // получаем путь из URL

        foreach ($this->routes[$method] ?? [] as $route => $callback) {
            // заменяем параметры вида {id} на регулярное выражение для чисел
            $pattern = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([0-9]+)', $route);
            $pattern = '#^' . $pattern . '$#';                  // формируем полный паттерн

            if (preg_match($pattern, $uri, $matches)) {          // проверяем совпадение с маршрутом
                array_shift($matches); // удаляем полное совпадение из массива аргументов

                // вызываем callback с параметрами из URL
                if (is_array($callback) && count($callback) === 2) {
                    call_user_func_array($callback, $matches);
                    return;
                } elseif (is_callable($callback)) {
                    call_user_func_array($callback, $matches);
                    return;
                }
            }
        }

        // если ни один маршрут не подошёл — выдаём 404 ошибку
        http_response_code(404);
        echo "404 Not Found";
    }
}
