<?php

    // “протокол”://”домен”.ru|com”/<контекст>”,
    // где протокол - http или https,
    // домен - непустая строка из строчных латинских букв,
    // контекст - может отсутствовать, если присутствует то это непустая строка из строчных
    // латинских букв.

    $data = trim(file_get_contents('Data/A.txt'));
    $result = "";
    $pattern = "/(^https?)(.*?)(ru|com)(.*)/";

    if (preg_match($pattern, $data, $matches)) {
        $result = "$matches[1]://" . "$matches[2]." . "$matches[3]/" . "$matches[4]";
    }

    echo($result);

?>
