<?php
    function solve($data) {

        $arrayDates = []; // Словарь реклама => последняя дата публикации
        $arrayCounts = []; // Словарь реклама => количество показов

        $strings = explode("\n", trim($data)); // Разделение входных данных на строки по \т
        foreach ($strings as $line) { // Проходимся по каждой строке
            $parts = preg_split('/ {2,}/', trim($line)); // Разделяем строку на две части по 2-м и более пробелам
            if (isset($arrayDates[$parts[0]])) { // Если в arrayDates уже есть такая реклама
                $arrayCounts[$parts[0]] += 1; // Прибавляем количество показов в arrayCounts
                $date1 = DateTime::createFromFormat('d.m.Y H:i:s', $arrayDates[$parts[0]]); // Форматирование в тип данных datetime
                $date2 = DateTime::createFromFormat('d.m.Y H:i:s', $parts[1]); // Форматирование в тип данных datetime
                if ($date2 > $date1) { // Если дата новее, обновляем дату в массиве arrayDates для этой рекламы
                    $arrayDates[$parts[0]] = $parts[1];
                }
            }
            else { // Если реклама встретилась первый раз
                $arrayCounts[$parts[0]] = 1; // Добавляем количество показов
                $arrayDates[$parts[0]] = $parts[1]; // Ставим текущую дату показа
            }  
        }

        $result = "";

        foreach ($arrayCounts as $ad => $value) { // Проходимся по массиву с количествами показов
            $result .= "$arrayCounts[$ad] $ad $arrayDates[$ad]\n"; // Записываем в строку количество показов и тд.
        }
        
        return(rtrim($result, "\n"));
    }
?>