<?php
// Получаем параметры подключения к базе из переменных окружения
$host = getenv("DB");
$port = getenv("PORT");
$dbname = getenv("DBNAME");
$user = getenv("USER");
$password = getenv("PASSWORD");

// Формируем строку подключения DSN для PostgreSQL
$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

try {
    // Создаём новое подключение к базе данных через PDO
    $pdo = new PDO($dsn, $user, $password);
    // Устанавливаем режим обработки ошибок — исключения
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // В случае ошибки подключения выводим сообщение с причиной
    echo "Ошибка подключения: " . $e->getMessage();
}
?>
