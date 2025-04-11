<?php
    function solve($data) {

        $names = []; // Идентификаторы баннера
        $sum = 0; // общая сумма весов баннера
        $result = ""; // Итоговый ответ

        $strings = explode("\n", trim($data)); // Разделение входных данных по переносу строки
        
        foreach ($strings as $line) { // Проходимся по входным строкам
            $temp = explode(" ", trim($line)); // Разделяем строку на идентификатор и вес
            $sum += $temp[1]; // Прибавляем вес к общей сумме
            $names[] = [$temp[0], $temp[1]]; // В идентификаторы баннера добавляем [id, вес]
        }

        foreach ($names as $name) { // Проходимся по всем баннерам
            $value = $name[1]/$sum; // Делим вес баннера на сумму всех весов
            $result .= "{$name[0]} {$value}\n"; // Добавляем ответ к конечной строке
        }

        return(rtrim($result, "\n"));
    }
?>