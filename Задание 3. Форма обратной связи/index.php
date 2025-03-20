<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Форма обратной связи</title>
</head>
<body>

    <div class="form-container">
        <h1>Форма обратной связи</h1>
        <form id="feedbackForm">
            <div class="form-line">
                <label for="name">ФИО:</label>
                <input type="text" id="name" name="name" pattern="[^0-9]+" title="Только буквы, без цифр" required placeholder="Введите ФИО">
            </div>
            <div class="form-line">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required placeholder="your@email.com">
            </div>
            <div class="form-line">
                <label for="phone">Телефон:</label>
                <input type="tel" id="phone" name="phone" required placeholder="+79991234567">
            </div>
            <div class="form-text">
                <label for="comment">Комментарий:</label>
                <textarea id="comment" name="comment" rows="5" cols="30" required></textarea>
            </div>
            
            <input type="submit" value="Отправить">
        </form>
    </div>

    <div class="container-info" style="display: none;">
        <p>Оставлено сообщение из формы обратной связи:</p>
        <div class="info-line"><p>ФИО: <span id="info-name"></span></p></div>
        <div class="info-line"><p>E-mail: <span id="info-email"></span></p></div>
        <div class="info-line"><p>Телефон: <span id="info-phone"></span></p></div>
        <p>С Вами свяжутся после <span id="info-time"></span></p>
    </div>

    <script src="script.js"></script>

</body>
</html>
