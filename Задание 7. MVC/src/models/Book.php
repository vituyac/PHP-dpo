<?php

namespace App\models;

use App\core\Database;
use PDO;

class Book {
    private PDO $pdo;

    public function __construct() {
        // Подключаемся к базе данных через класс Database
        $this->pdo = Database::connect();
    }

    // Добавляем новую книгу с файлами обложки и книги
    public function addBook(string $title, string $author, string $username, string $readDate, bool $download, array $coverFile, array $bookFile): void {
        $timestamp = date('Ymd_His');                // метка времени для уникальных имён файлов
        $basePath = "uploads/$username";             // папка для пользователя

        // Папки для обложек и книг (создаём если нет)
        $coversPath = "$basePath/covers";
        $booksPath = "$basePath/books";
        if (!is_dir($coversPath)) mkdir($coversPath, 0777, true);
        if (!is_dir($booksPath)) mkdir($booksPath, 0777, true);

        // Формируем имена файлов с меткой времени
        $coverFilename = $timestamp . "_" . basename($coverFile['name']);
        $bookFilename = $timestamp . "_" . basename($bookFile['name']);

        $coverTarget = "$coversPath/$coverFilename";
        $bookTarget = "$booksPath/$bookFilename";

        // Перемещаем загруженные файлы в нужные папки
        move_uploaded_file($coverFile['tmp_name'], $coverTarget);
        move_uploaded_file($bookFile['tmp_name'], $bookTarget);

        // Сохраняем информацию о книге в базе
        $stmt = $this->pdo->prepare("
            INSERT INTO books (title, author, cover, book, read_date, download)
            VALUES (:title, :author, :cover, :book, :read_date, :download)
        ");

        $stmt->execute([
            'title'     => htmlspecialchars($title),    // экранируем спецсимволы
            'author'    => htmlspecialchars($author),
            'cover'     => $coverTarget,
            'book'      => $bookTarget,
            'read_date' => $readDate,
            'download'  => $download
        ]);
    }

    // Получаем все книги из базы
    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM books ORDER BY read_date");
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Формируем URL для обложки и книги, а также булево значение для загрузки
        foreach ($books as &$book) {
            $book['cover_url'] = $book['cover'] ? '/' . ltrim($book['cover'], '/') : null;
            $book['download_url'] = $book['book'] ? '/' . ltrim($book['book'], '/') : null;
            $book['download_allowed'] = (bool)$book['download'];
        }

        return $books;
    }

    // Удаляем книгу и связанные с ней файлы по ID
    public function deleteBook(int $id): void {
        // Сначала получаем пути к файлам
        $stmt = $this->pdo->prepare("SELECT cover, book FROM books WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $book = $stmt->fetch();

        // Удаляем файлы, если они существуют
        if ($book) {
            if (!empty($book['cover']) && file_exists($book['cover'])) {
                unlink($book['cover']);
            }
            if (!empty($book['book']) && file_exists($book['book'])) {
                unlink($book['book']);
            }
        }

        // Удаляем запись из базы
        $stmt = $this->pdo->prepare("DELETE FROM books WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    // Получаем книгу по ID, возвращаем массив или null
    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM books WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        return $book ?: null;
    }

    // Обновляем данные книги, при необходимости заменяем файлы
    public function updateBook(int $id, string $title, string $author, string $readDate, bool $download, string $username, ?array $newCover, ?array $newBook, array $oldBook): void {
        $timestamp = date('Ymd_His');
        $coverPath = $oldBook['cover'] ?? null;
        $bookPath  = $oldBook['book'] ?? null;

        $basePath = "uploads/$username";
        // Создаём папки если их нет
        if (!is_dir("$basePath/covers")) mkdir("$basePath/covers", 0777, true);
        if (!is_dir("$basePath/books")) mkdir("$basePath/books", 0777, true);

        // Загружаем новую обложку, если есть
        if ($newCover) {
            $coverFilename = $timestamp . "_" . basename($newCover['name']);
            $coverPath = "$basePath/covers/$coverFilename";
            move_uploaded_file($newCover['tmp_name'], $coverPath);
        }

        // Загружаем новый файл книги, если есть
        if ($newBook) {
            $bookFilename = $timestamp . "_" . basename($newBook['name']);
            $bookPath = "$basePath/books/$bookFilename";
            move_uploaded_file($newBook['tmp_name'], $bookPath);
        }

        // Обеспечиваем, что переменные либо null, либо строка
        $coverPath = $coverPath ?: null;
        $bookPath = $bookPath ?: null;

        // Обновляем запись в базе
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
            'download'  => (int) $download,      // приводим boolean к int для базы
            'cover'     => $coverPath,
            'book'      => $bookPath
        ]);
    }
}

?>
