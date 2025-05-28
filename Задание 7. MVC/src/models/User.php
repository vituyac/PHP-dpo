<?php
namespace App\models;

use App\core\Database;
use PDO;

class User {
    private PDO $pdo;

    public function __construct() {
        // Подключаемся к базе данных через класс Database
        $this->pdo = Database::connect();
    }

    // Получаем пользователя по имени, возвращаем массив данных или null, если не найден
    public function getUserByName(string $username): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }

    // Создаем нового пользователя с хэшированным паролем
    public function createUser(string $username, string $password): void {
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        // Хэшируем пароль перед сохранением
        $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
    }
}
?>
