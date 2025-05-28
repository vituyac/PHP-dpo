<?php
namespace App\helpers;

class Auth {
    // Проверяем, вошёл ли пользователь (существует ли user_id в сессии)
    public static function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    // Проверяем, является ли пользователь администратором (role = 'admin')
    public static function isAdmin(): bool {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    // Получаем имя пользователя из сессии или возвращаем null, если его нет
    public static function getUsername(): ?string {
        return $_SESSION['username'] ?? null;
    }
}