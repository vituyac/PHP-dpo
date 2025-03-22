<?php
    date_default_timezone_set("Europe/Moscow");
    require 'db/db.php';

    function send_mail($to, $subject, $message) : void {
        $smtp_server = getenv("SMTP_SERVER");
        $smtp_port = getenv("SMTP_PORT");
        $smtp_user = getenv("SMTP_USER");
        $smtp_pass = getenv("SMTP_PASS");
        
        $headers = "From: kisterev-volgait24@yandex.ru\r\n";
        $headers .= "Reply-To: skisterev78@mail.ru\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
        $socket = fsockopen("ssl://$smtp_server", $smtp_port, $errno, $errstr, 30);
        
        fputs($socket, "EHLO $smtp_server\r\n");
        fgets($socket, 512);
    
        fputs($socket, "AUTH LOGIN\r\n");
        fgets($socket, 512);
    
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
    
        fputs($socket, "Subject: $subject\r\n");
        fputs($socket, "$headers\r\n");
        fputs($socket, "$message\r\n");
        fputs($socket, ".\r\n");
        fgets($socket, 512);
    
        fputs($socket, "QUIT\r\n");
        fclose($socket);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $comment = trim($_POST['comment']);
        $created_at = date("Y-m-d H:i:s");

        $stmt = $pdo->prepare("SELECT created_at FROM users_comments WHERE email = ? AND created_at >= NOW() - INTERVAL '1 hour'");
        $stmt->execute([$email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $next_request_time = date("H:i:s d.m.Y", strtotime($data['created_at'] . " +1 hour"));
            echo json_encode(["success" => false, "message" => "Вы уже отправляли заявку. Повторная заявка возможна после $next_request_time."]);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO users_comments (name, email, phone, comment, created_at) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([$name, $email, $phone, $comment, $created_at]);

        if ($result) {
            send_mail("skisterev78@mail.ru", "Request", "$name, $email, $phone, $comment, $created_at");
        }

        $contact_time = date("H:i:s d.m.Y", strtotime("+90 min"));

        echo json_encode([
            "success" => true,
            "name" => $name,
            "email" => $email,
            "phone" => $phone,
            "contact_time" => $contact_time
        ]);
    }
?>
