<?php
namespace App\controllers;

use App\models\User;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class AuthController {
    private User $userModel;      // модель пользователя для работы с БД
    private Environment $twig;    // движок шаблонов Twig

    public function __construct() {
        $this->userModel = new User();  // создаём экземпляр модели User
        $loader = new FilesystemLoader(__DIR__ . '/../views'); // указываем путь к шаблонам
        $this->twig = new Environment($loader);  // инициализируем Twig
    }

    public function showLoginForm(): void {
        // отображаем форму входа
        echo $this->twig->render('login.twig');
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);   // получаем и очищаем логин
            $password = $_POST['password'];          // получаем пароль

            $user = $this->userModel->getUserByName($username); // ищем пользователя

            if ($user && password_verify($password, $user['password'])) {
                // если пользователь найден и пароль верный, сохраняем сессию
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: /");  // редирект на главную
                exit;
            } else {
                // ошибка авторизации, показываем форму с сообщением
                echo $this->twig->render('login.twig', [
                    'error' => 'Неверный email или пароль',
                    'username' => htmlspecialchars($username)
                ]);
            }
        }
    }

    public function logout(): void {
        session_destroy();          // уничтожаем сессию
        header("Location: /");      // редирект на главную
        exit;
    }

    public function showRegisterForm(): void {
        // отображаем форму регистрации
        echo $this->twig->render('register.twig');
    }

    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);            // получаем логин
            $password = $_POST['password'];                   // получаем пароль
            $confirm = $_POST['confirm_password'];            // подтверждение пароля

            if ($password !== $confirm) {
                // пароли не совпадают — показываем ошибку
                echo $this->twig->render('register.twig', [
                    'error' => 'Пароли не совпадают',
                    'username' => htmlspecialchars($username)
                ]);
                return;
            }

            if ($this->userModel->getUserByName($username)) {
                // пользователь с таким именем уже есть
                echo $this->twig->render('register.twig', [
                    'error' => 'Имя пользователя уже занято',
                    'username' => htmlspecialchars($username)
                ]);
                return;
            }

            // создаём нового пользователя
            $this->userModel->createUser($username, $password);
            header("Location: /login");  // перенаправляем на страницу входа
            exit;
        }
    }
}
