<?php
    session_start();

    require __DIR__ . '/../vendor/autoload.php';
    use App\core\Router;

    use App\controllers\BookController;
    use App\controllers\AuthController;

    $router = new Router();

    $auth = new AuthController();
    $router->get('/login', [$auth, 'showLoginForm']);
    $router->post('/login', [$auth, 'login']);
    $router->get('/logout', [$auth, 'logout']);
    $router->get('/register', [$auth, 'showRegisterForm']);
    $router->post('/register', [$auth, 'register']);

    $books = new BookController();
    $router->get('/', [$books, 'index']);                     // Главная = список книг

    $router->get('/books/add', [$books, 'showAddForm']);      // Форма добавления
    $router->post('/books/add', [$books, 'add']);             // Обработка добавления

    $router->get('/books/edit/{id}', [$books, 'showEditForm']);  // Форма редактирования
    $router->post('/books/edit/{id}', [$books, 'edit']);

    $router->post('/books/delete/{id}', [$books, 'delete']);

    $router->resolve();
?>
