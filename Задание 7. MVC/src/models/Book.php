<?php
    namespace App\models;

    use App\core\Database;
    use PDO;

    class Book {
        private PDO $pdo;

        public function __construct() {
            $this->pdo = Database::connect();
        }

        public function addBook(string $title, string $author, string $username, string $readDate, bool $download, array $coverFile, array $bookFile): void {
            $timestamp = date('Ymd_His');
            $basePath = "uploads/$username";

            // Создаём папки если не существуют
            $coversPath = "$basePath/covers";
            $booksPath = "$basePath/books";
            if (!is_dir($coversPath)) mkdir($coversPath, 0777, true);
            if (!is_dir($booksPath)) mkdir($booksPath, 0777, true);

            // Подготовка файлов
            $coverFilename = $timestamp . "_" . basename($coverFile['name']);
            $bookFilename = $timestamp . "_" . basename($bookFile['name']);

            $coverTarget = "$coversPath/$coverFilename";
            $bookTarget = "$booksPath/$bookFilename";

            // Перемещаем загруженные файлы
            move_uploaded_file($coverFile['tmp_name'], $coverTarget);
            move_uploaded_file($bookFile['tmp_name'], $bookTarget);

            // Сохраняем в базу
            $stmt = $this->pdo->prepare("
                INSERT INTO books (title, author, cover, book, read_date, download)
                VALUES (:title, :author, :cover, :book, :read_date, :download)
            ");

            $stmt->execute([
                'title'     => htmlspecialchars($title),
                'author'    => htmlspecialchars($author),
                'cover'     => $coverTarget,
                'book'      => $bookTarget,
                'read_date' => $readDate,
                'download'  => $download
            ]);
        }

        public function getAll(): array {
            $stmt = $this->pdo->query("SELECT * FROM books ORDER BY read_date");
            $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($books as &$book) {
                $book['cover_url'] = $book['cover'] ? '/' . ltrim($book['cover'], '/') : null;
                $book['download_url'] = $book['book'] ? '/' . ltrim($book['book'], '/') : null;
                $book['download_allowed'] = (bool)$book['download'];
            }

            return $books;
        }

        public function deleteBook(int $id): void {
            $stmt = $this->pdo->prepare("SELECT cover, book FROM books WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $book = $stmt->fetch();

            if ($book) {
                if (!empty($book['cover']) && file_exists($book['cover'])) {
                    unlink($book['cover']);
                }
                if (!empty($book['book']) && file_exists($book['book'])) {
                    unlink($book['book']);
                }
            }

            $stmt = $this->pdo->prepare("DELETE FROM books WHERE id = :id");
            $stmt->execute(['id' => $id]);
        }

        public function getById(int $id): ?array {
            $stmt = $this->pdo->prepare("SELECT * FROM books WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $book = $stmt->fetch(PDO::FETCH_ASSOC);
            return $book ?: null;
        }

        public function updateBook(int $id, string $title, string $author, string $readDate, bool $download, string $username, ?array $newCover, ?array $newBook, array $oldBook): void {
            $timestamp = date('Ymd_His');
            $coverPath = $oldBook['cover'] ?? null;
            $bookPath  = $oldBook['book'] ?? null;

            $basePath = "uploads/$username";
            if (!is_dir("$basePath/covers")) mkdir("$basePath/covers", 0777, true);
            if (!is_dir("$basePath/books")) mkdir("$basePath/books", 0777, true);

            if ($newCover) {
                $coverFilename = $timestamp . "_" . basename($newCover['name']);
                $coverPath = "$basePath/covers/$coverFilename";
                move_uploaded_file($newCover['tmp_name'], $coverPath);
            }

            if ($newBook) {
                $bookFilename = $timestamp . "_" . basename($newBook['name']);
                $bookPath = "$basePath/books/$bookFilename";
                move_uploaded_file($newBook['tmp_name'], $bookPath);
            }

            // Явно приводим к нужному типу
            $coverPath = $coverPath ?: null;
            $bookPath = $bookPath ?: null;

            $stmt = $this->pdo->prepare("
                UPDATE books 
                SET title = :title, 
                    author = :author, 
                    read_date = :read_date, 
                    download = :download, 
                    cover = :cover, 
                    book = :book 
                WHERE id = :id
            ");

            $stmt->execute([
                'id'        => $id,
                'title'     => htmlspecialchars($title),
                'author'    => htmlspecialchars($author),
                'read_date' => $readDate,
                'download'  => (int) $download,      // ← boolean
                'cover'     => $coverPath,     // ← null или строка
                'book'      => $bookPath       // ← null или строка
            ]);
        }
    }

?>
