<?php
namespace App\controllers;

use App\models\Book;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class BookController {
    private Book $bookModel;         // модель для работы с книгами
    private Environment $twig;       // движок шаблонов Twig

    public function __construct() {
        $this->bookModel = new Book();                             // создаём модель книги
        $loader = new FilesystemLoader(__DIR__ . '/../views');    // загружаем шаблоны из папки views
        $this->twig = new Environment($loader);                   // инициализируем Twig
    }

    public function index(): void {
        $books = $this->bookModel->getAll();                      // получаем все книги
        $username = $_SESSION['username'] ?? null;                 // имя пользователя из сессии (если есть)

        echo $this->twig->render('books.twig', [                   // выводим страницу со списком книг
            'books'    => $books,
            'username' => $username
        ]);
    }

    public function showAddForm(): void {
        if (!isset($_SESSION['username'])) {                       // проверяем, авторизован ли пользователь
            header("Location: /login");                            // если нет — перенаправляем на вход
            exit;
        }

        echo $this->twig->render('book_add.twig', [                // показываем форму добавления книги
            'username' => $_SESSION['username']
        ]);
    }

    public function add(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['username'])) {
            // получаем данные из формы
            $title    = trim($_POST['title']);
            $author   = trim($_POST['author']);
            $readDate = $_POST['read_date'];
            $download = isset($_POST['download']) ? true : false;
            $username = $_SESSION['username'];

            // добавляем книгу через модель, передаём файлы обложки и книги
            $this->bookModel->addBook(
                $title,
                $author,
                $username,
                $readDate,
                $download,
                $_FILES['cover'],
                $_FILES['book']
            );

            header("Location: /");   // редирект на главную после добавления
            exit;
        }
    }

    public function delete(int $id): void {
        if (!isset($_SESSION['username'])) {                       // проверка авторизации
            header("Location: /login");
            exit;
        }

        $this->bookModel->deleteBook($id);                         // удаляем книгу по ID
        header("Location: /");                                      // редирект на главную
        exit;
    }

    public function showEditForm(int $id): void {
        if (!isset($_SESSION['username'])) {                       // проверка авторизации
            header("Location: /login");
            exit;
        }

        $book = $this->bookModel->getById($id);                    // получаем книгу по ID
        if (!$book) {
            echo "Книга не найдена.";                               // если книга не найдена, выводим сообщение
            return;
        }

        echo $this->twig->render('book_edit.twig', [               // показываем форму редактирования
            'book'     => $book,
            'username' => $_SESSION['username']
        ]);
    }

    public function edit(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['username'])) {
            $book = $this->bookModel->getById($id);                // получаем книгу по ID
            if (!$book) {
                echo "Книга не найдена.";
                return;
            }

            // Получаем данные из формы
            $title    = trim($_POST['title']);
            $author   = trim($_POST['author']);
            $readDate = $_POST['read_date'];
            $download = !empty($_POST['download']);
            $username = $_SESSION['username'];

            // Удаляем старую обложку, если отмечено удаление и файл существует
            if (isset($_POST['delete_cover']) && !empty($book['cover']) && file_exists($book['cover'])) {
                unlink($book['cover']);
                $book['cover'] = null;
            }

            // Удаляем старый файл книги по аналогии
            if (isset($_POST['delete_book']) && !empty($book['book']) && file_exists($book['book'])) {
                unlink($book['book']);
                $book['book'] = null;
            }

            // Загружаем новую обложку, если есть, и удаляем старую
            $newCover = isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK ? $_FILES['cover'] : null;
            if ($newCover && !empty($book['cover']) && file_exists($book['cover'])) {
                unlink($book['cover']);
                $book['cover'] = null;
            }

            // Загружаем новый файл книги, если есть, и удаляем старый
            $newBook = isset($_FILES['book']) && $_FILES['book']['error'] === UPLOAD_ERR_OK ? $_FILES['book'] : null;
            if ($newBook && !empty($book['book']) && file_exists($book['book'])) {
                unlink($book['book']);
                $book['book'] = null;
            }

            // Обновляем книгу в модели
            $this->bookModel->updateBook(
                $id,
                $title,
                $author,
                $readDate,
                $download,
                $username,
                $newCover,
                $newBook,
                $book
            );

            header("Location: /");   // редирект на главную после обновления
            exit;
        }
    }
}
