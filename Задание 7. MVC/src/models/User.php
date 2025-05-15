<?php
    namespace App\models;
    use App\core\Database;
    use PDO;

    class User {
        private PDO $pdo;

        public function __construct() {
            $this->pdo = Database::connect();
        }

        public function getUserByName(string $username): ?array {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            return $stmt->fetch() ?: null;
        }

        public function createUser(string $username, string $password): void {
            $stmt = $this->pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
        }

    }
?>
