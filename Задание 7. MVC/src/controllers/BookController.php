<?php
    namespace App\controllers;

    use App\models\Book;
    use Twig\Environment;
    use Twig\Loader\FilesystemLoader;

    class BookController {
        private Book $bookModel;
        private Environment $twig;

        public function __construct() {
            $this->bookModel = new Book();
            $loader = new FilesystemLoader(__DIR__ . '/../views');
            $this->twig = new Environment($loader);
        }

        public function index(): void {
            $books = $this->bookModel->getAll();
            $username = $_SESSION['username'] ?? null;

            echo $this->twig->render('books.twig', [
                'books'    => $books,
                'username' => $username
            ]);
        }

        public function showAddForm(): void {
            if (!isset($_SESSION['username'])) {
                header("Location: /login");
                exit;
            }

            echo $this->twig->render('book_add.twig', [
                'username' => $_SESSION['username']
            ]);
        }

        public function add(): void {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['username'])) {
                $title    = trim($_POST['title']);
                $author   = trim($_POST['author']);
                $readDate = $_POST['read_date'];
                $download = isset($_POST['download']) ? true : false;
                $username = $_SESSION['username'];

                $this->bookModel->addBook(
                    $title,
                    $author,
                    $username,
                    $readDate,
                    $download,
                    $_FILES['cover'],
                    $_FILES['book']
                );

                header("Location: /");
                exit;
            }
        }

        public function delete(int $id): void {
            if (!isset($_SESSION['username'])) {
                header("Location: /login");
                exit;
            }

            $this->bookModel->deleteBook($id);
            header("Location: /");
            exit;
        }

        public function showEditForm(int $id): void {
            if (!isset($_SESSION['username'])) {
                header("Location: /login");
                exit;
            }

            $book = $this->bookModel->getById($id);
            if (!$book) {
                echo "Книга не найдена.";
                return;
            }

            echo $this->twig->render('book_edit.twig', [
                'book'     => $book,
                'username' => $_SESSION['username']
            ]);
        }

        public function edit(int $id): void {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['username'])) {
                $book = $this->bookModel->getById($id);
                if (!$book) {
                    echo "Книга не найдена.";
                    return;
                }

                $title    = trim($_POST['title']);
                $author   = trim($_POST['author']);
                $readDate = $_POST['read_date'];
                $download = !empty($_POST['download']);
                $username = $_SESSION['username'];

                // Удаление обложки по флажку
                if (isset($_POST['delete_cover']) && !empty($book['cover']) && file_exists($book['cover'])) {
                    unlink($book['cover']);
                    $book['cover'] = null;
                }

                // Удаление файла книги по флажку
                if (isset($_POST['delete_book']) && !empty($book['book']) && file_exists($book['book'])) {
                    unlink($book['book']);
                    $book['book'] = null;
                }

                // Загрузка новой обложки
                $newCover = isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK ? $_FILES['cover'] : null;
                if ($newCover && !empty($book['cover']) && file_exists($book['cover'])) {
                    unlink($book['cover']);
                    $book['cover'] = null;
                }

                // Загрузка нового файла книги
                $newBook = isset($_FILES['book']) && $_FILES['book']['error'] === UPLOAD_ERR_OK ? $_FILES['book'] : null;
                if ($newBook && !empty($book['book']) && file_exists($book['book'])) {
                    unlink($book['book']);
                    $book['book'] = null;
                }

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

                header("Location: /");
                exit;
            }
        }
    }
