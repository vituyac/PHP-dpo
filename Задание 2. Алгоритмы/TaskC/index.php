<?php
    function solve($data) {
        // Разделяем входные данные на строки
        $lines = explode("\n", trim($data));
        $output = "";

        foreach ($lines as $line) {
            // Используем регулярное выражение для извлечения строки и параметров
            $pattern = '/<(.*)>\s(.*)/';
            preg_match($pattern, $line, $matches);

            // Извлекаем строку и параметры
            $str = $matches[1];
            $params = explode(" ", trim($matches[2]));

            // Проверка типа данных
            if($params[0] == "S") { // Проверка строки по длине
                if ($params[1] <= strlen($str) && strlen($str) <= $params[2]) {
                    $output .= "OK\n";
                } else {
                    $output .= "FAIL\n";
                }
            } elseif($params[0] == "N") { // Проверка числового диапазона
                if ($params[1] <= $str && $str <= $params[2] && (int)$str == $str) {
                    $output .= "OK\n";
                } else {
                    $output .= "FAIL\n";
                }
            } elseif($params[0] == "P") { // Проверка номера телефона (формат: +7 (XXX) XXX-XX-XX)
                $num_pattern = "/^\+7\s\(\d{3}\)\s\d{3}\-\d{2}-\d{2}$/";
                if (preg_match($num_pattern, $str)) {
                    $output .= "OK\n";
                } else {
                    $output .= "FAIL\n";
                }
            } elseif($params[0] == "D") { // Проверка даты и времени (формат: DD.MM.YYYY HH:MM)
                $date_pattern = "/(\d{1,2})\.(\d{1,2})\.(\d{4})\s(\d{1,2}):(\d{2})/";
                if (preg_match($date_pattern, $str, $date_match) && 
                    checkdate($date_match[2], $date_match[1], $date_match[3]) && 
                    $date_match[4] < 24 && $date_match[5] < 60) {
                    $output .= "OK\n";
                } else {
                    $output .= "FAIL\n";
                }
            } elseif($params[0] == "E") { // Проверка email-адреса (формат: username@domain.ext)
                $email_pattern = "/^(?!_)[A-Za-z0-9_]{4,30}@[A-Za-z]{2,30}\.[a-z]{2,10}$/";
                if (preg_match($email_pattern, $str)) {
                    $output .= "OK\n";
                } else {
                    $output .= "FAIL\n";
                }
            }
        }
        // Убираем последний перевод строки
        return substr($output, 0, -1);
    }
?>
