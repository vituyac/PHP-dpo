<?php
    $host = getenv("DB");
    $port = getenv("PORT");
    $dbname = getenv("DBNAME");
    $user = getenv("USER");
    $password = getenv("PASSWORD");

    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

    try {
        $pdo = new PDO($dsn, $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Ошибка подключения: " . $e->getMessage();
    }
?>
