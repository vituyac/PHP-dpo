<?php
date_default_timezone_set("Europe/Moscow");
require 'db.php';

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
