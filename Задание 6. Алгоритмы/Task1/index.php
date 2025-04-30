<?php
    function solve($data) {
        $strings = explode("\n", trim($data)); // Получаем входные данные
        $flightCount = (int)$strings[0]; // Количество полётов

        $result = "";

        for ($i = 1; $i <= $flightCount; $i++) {
            $parts = explode(" ", $strings[$i]);

            // Получаем строки с датами и смещениями
            $datetimeOne = str_replace("_", " ", $parts[0]);
            $offsetOne = (int)$parts[1];

            $datetimeTwo = str_replace("_", " ", $parts[2]);
            $offsetTwo = (int)$parts[3];

            // Форматируем строки в даты
            $date1 = DateTime::createFromFormat("d.m.Y H:i:s", $datetimeOne);
            $date2 = DateTime::createFromFormat("d.m.Y H:i:s", $datetimeTwo);

            // Вычитание дат, учитывая смещение
            $answer = ($date2->getTimestamp() - ($offsetTwo * 3600)) - ($date1->getTimestamp() - ($offsetOne * 3600));

            $result .= $answer . "\n";
        }

        return(rtrim($result, "\n"));
    }
?>
