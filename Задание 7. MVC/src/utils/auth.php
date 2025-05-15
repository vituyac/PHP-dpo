<?php
    namespace App\helpers;

    class Auth {
        public static function isLoggedIn(): bool {
            return isset($_SESSION['user_id']);
        }

        public static function isAdmin(): bool {
            return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
        }

        public static function getUsername(): ?string {
            return $_SESSION['username'] ?? null;
        }
    }