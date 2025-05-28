<?php
// Устанавливаем временную зону для корректного отображения даты и времени
date_default_timezone_set("Europe/Moscow");

// Подключаем файл с настройками подключения к базе данных
require 'db/db.php';

// Функция для отправки письма через SMTP-сервер
function send_mail($to, $subject, $message) : void {
    // Получаем настройки SMTP из переменных окружения
    $smtp_server = getenv("SMTP_SERVER");
    $smtp_port = getenv("SMTP_PORT");
    $smtp_user = getenv("SMTP_USER");
    $smtp_pass = getenv("SMTP_PASS");
    
    // Заголовки письма
    $headers = "From: kisterev-volgait24@yandex.ru\r\n";
    $headers .= "Reply-To: skisterev78@mail.ru\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // Открываем сокетное соединение с SMTP сервером по SSL
    $socket = fsockopen("ssl://$smtp_server", $smtp_port, $errno, $errstr, 30);
    
    // Отправляем SMTP-команды по протоколу
    fputs($socket, "EHLO $smtp_server\r\n");
    fgets($socket, 512);

    fputs($socket, "AUTH LOGIN\r\n");
    fgets($socket, 512);

    // Отправляем логин и пароль в base64
    fputs($socket, base64_encode($smtp_user) . "\r\n");
    fgets($socket, 512);

    fputs($socket, base64_encode($smtp_pass) . "\r\n");
    fgets($socket, 512);

    fputs($socket, "MAIL FROM: <$smtp_user>\r\n");
    fgets($socket, 512);

    fputs($socket, "RCPT TO: <$to>\r\n");
    fgets($socket, 512);

    fputs($socket, "DATA\r\n");
    fgets($socket, 512);

    // Отправляем тему, заголовки и тело письма
    fputs($socket, "Subject: $subject\r\n");
    fputs($socket, "$headers\r\n");
    fputs($socket, "$message\r\n");
    fputs($socket, ".\r\n");  // Конец тела письма
    fgets($socket, 512);

    // Завершаем соединение
    fputs($socket, "QUIT\r\n");
    fclose($socket);
}

// Проверяем, был ли отправлен POST-запрос
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Получаем и очищаем данные из формы
    $name = trim($_POST['name']);         // Имя пользователя
    $email = trim($_POST['email']);       // Email пользователя
    $phone = trim($_POST['phone']);       // Телефон пользователя
    $comment = trim($_POST['comment']);   // Комментарий пользователя

    // Получаем текущую дату и время для записи в базу
    $created_at = date("Y-m-d H:i:s");

    // Проверяем, отправлял ли пользователь уже комментарий в течение последнего часа
    $stmt = $pdo->prepare("SELECT created_at FROM users_comments WHERE email = ? AND created_at >= NOW() - INTERVAL '1 hour'");
    $stmt->execute([$email]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Если такой комментарий уже есть — запрещаем повторную отправку
    if ($data) {
        // Рассчитываем, когда пользователь сможет отправить следующую заявку
        $next_request_time = date("H:i:s d.m.Y", strtotime($data['created_at'] . " +1 hour"));
        
        // Отправляем JSON-ответ с ошибкой и прекращаем выполнение скрипта
        echo json_encode([
            "success" => false,
            "message" => "Вы уже отправляли заявку. Повторная заявка возможна после $next_request_time."
        ]);
        exit;
    }

    // Добавляем новый комментарий в базу данных
    $stmt = $pdo->prepare("INSERT INTO users_comments (name, email, phone, comment, created_at) VALUES (?, ?, ?, ?, ?)");
    $result = $stmt->execute([$name, $email, $phone, $comment, $created_at]);

    // Если запись успешно добавлена — отправляем уведомление на почту
    if ($result) {
        send_mail("skisterev78@mail.ru", "Request", "$name, $email, $phone, $comment, $created_at");
    }

    // Рассчитываем время, когда можно будет связаться с пользователем (через 90 минут)
    $contact_time = date("H:i:s d.m.Y", strtotime("+90 min"));

    // Отправляем успешный JSON-ответ с информацией
    echo json_encode([
        "success" => true,
        "name" => $name,
        "email" => $email,
        "phone" => $phone,
        "contact_time" => $contact_time
    ]);
}
?>
