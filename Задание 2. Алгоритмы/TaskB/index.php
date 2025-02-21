<?php

    function solve($data) {
        // Разбиваем входные данные на строки
        $lines = explode("\n", trim($data));
        $output = "";

        // Проходим по каждой строке
        foreach ($lines as $line) {
            // Заменяем `::` на `:qqqq:` для дальнейшей обработки
            $line = str_replace("::", ":qqqq:", $line);

            // Разбиваем адрес по двоеточию
            $ips = explode(":", trim($line));
            $str = "";

            // Обрабатываем каждую группу чисел
            foreach ($ips as $num) {
                if (strlen($num) == 4) {
                    // Если длина 4 символа, оставляем как есть
                    $str .= $num . ":";
                } elseif (strlen($num) == 1) {
                    // Если длина 1, добавляем 3 нуля спереди
                    $str .= "000" . $num . ":";
                } elseif (strlen($num) == 2) {
                    // Если длина 2, добавляем 2 нуля спереди
                    $str .= "00" . $num . ":";
                } elseif (strlen($num) == 3) {
                    // Если длина 3, добавляем 1 ноль спереди
                    $str .= "0" . $num . ":";
                } elseif (strlen($num) == 0) {
                    // Если пусто, заменяем 4 нуля
                    $str .= "0000" . ":";
                }
            }

            // Удаляем последнее двоеточие
            $str = substr($str, 0, -1);

            // Заменяем "qqqq:" на недостающие блоки "0000" (чтобы было 8 блоков)
            $str = str_replace("qqqq:", str_repeat("0000:", (7 - substr_count($str, ":") + 1)), $str);

            // Добавляем в итоговый результат
            $output .= $str . "\n";
        }

        // Удаляем лишний перенос строки в конце
        return substr($output, 0, -1);
    }
?>