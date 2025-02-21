<?php

    // Функция для замены ссылки на новый формат
    function replace_link($matches) {
        return "http://sozd.parlament.gov.ru/bill/" . $matches[1];
    }

    // Читаем содержимое файла с html текстом
    $string = file_get_contents('index_input.txt');
    // Регулярное выражение для поиска ссылок в старом формате
    $pattern = "/http:\/\/asozd\.duma\.gov\.ru\/main\.nsf\/\(Spravka\)\?OpenAgent&RN=(\d*-\d*)&\d*/";
    // Заменяем найденные ссылки с помощью функции replace_link
    $result = preg_replace_callback($pattern, "replace_link", $string);

    // Записываем изменённое содержимое в новый файл
    file_put_contents('index_output.txt', $result);