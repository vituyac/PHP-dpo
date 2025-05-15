<?php
namespace App\controllers;

use App\models\User;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class AuthController {
    private User $userModel;
    private Environment $twig;

    public function __construct() {
        $this->userModel = new User();
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new Environment($loader);
    }

    public function showLoginForm(): void {
        echo $this->twig->render('login.twig');
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            $user = $this->userModel->getUserByName($username);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: /");
                exit;
            } else {
                echo $this->twig->render('login.twig', [
                    'error' => 'Неверный email или пароль',
                    'username' => htmlspecialchars($username)
                ]);
            }
        }
    }

    public function logout(): void {
        session_destroy();
        header("Location: /");
        exit;
    }

    public function showRegisterForm(): void {
        echo $this->twig->render('register.twig');
    }

    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            $confirm = $_POST['confirm_password'];

            if ($password !== $confirm) {
                echo $this->twig->render('register.twig', [
                    'error' => 'Пароли не совпадают',
                    'username' => htmlspecialchars($username)
                ]);
                return;
            }

            if ($this->userModel->getUserByName($username)) {
                echo $this->twig->render('register.twig', [
                    'error' => 'Имя пользователя уже занято',
                    'username' => htmlspecialchars($username)
                ]);
                return;
            }

            $this->userModel->createUser($username, $password);
            header("Location: /login");
            exit;
        }
    }
}
