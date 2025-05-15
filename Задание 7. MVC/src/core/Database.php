<?php
    // Объявляет пространство имён для класса базы данных
    namespace App\core;

    // Подключает класс PDO для работы с базой данных
    use PDO;

    // Подключает класс PDOException для обработки ошибок подключения
    use PDOException;

    // Определяет класс Database для управления соединением с базой данных
    class Database {
        // Статическая переменная для хранения единственного экземпляра PDO
        private static ?PDO $pdo = null;

        // Метод для установки соединения с базой данных (использует паттерн Singleton)
        public static function connect(): PDO {
            // Проверяет, есть ли уже установленное соединение
            if (self::$pdo === null) {
                // Загружает параметры подключения из файла .env
                $config = parse_ini_file(__DIR__ . '/../../.env');
                try {
                    $dsn = "pgsql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']}";
                    self::$pdo = new PDO($dsn,
                        $config['DB_USER'],
                        $config['DB_PASS'],
                        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
                    );
                } catch (PDOException $e) {
                    // Если подключение не удалось, выводит сообщение об ошибке и завершает выполнение скрипта
                    die("Ошибка подключения к базе данных: " . $e->getMessage());
                }
            }
            // Возвращает объект PDO (единственный экземпляр)
            return self::$pdo;
        }
    }
?>
